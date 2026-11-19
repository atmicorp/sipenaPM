<?php

namespace App\Services;

use App\Imports\LogbookRawImport;
use App\Models\DataPengujiMagang;
use App\Models\DataPerusahaanMagang;
use App\Models\PesertaMagang;
use App\Models\StatusDosen;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

/**
 * Import data mahasiswa magang dari LOGBOOK ke SIPENA.
 *
 * Aturan:
 * - Data master adalah logbook (mahasiswa + relasi dosen pembimbing).
 * - Dosen di logbook dicocokkan ke Dosen di SIPENA lewat email.
 * - Import hanya boleh jalan kalau data mahasiswa di SIPENA benar-benar kosong.
 * - Semua baris harus cocok; kalau ada satu saja yang tidak cocok,
 *   tidak ada yang boleh disimpan.
 */
class LogbookImportService
{
    protected const ROLE_PENGECUALIAN = ['Admin', 'Dosen'];
    protected const ROLE_MAHASISWA = 'Mahasiswa';
    protected const NAMA_STATUS_PEMBIMBING = 'Pembimbing';

    protected const TEMP_DISK = 'local';
    protected const TEMP_DIR = 'temp-imports-logbook';

    /**
     * Cek apakah data mahasiswa di SIPENA sudah kosong.
     * Kriteria sama dengan ManageController::resetDatabase():
     * mahasiswa = semua user yang BUKAN Admin/Dosen.
     *
     * @return array{kosong: bool, alasan: array<int, string>}
     */
    public function cekSyaratKosong(): array
    {
        $alasan = [];

        $jumlahMahasiswa = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', self::ROLE_PENGECUALIAN);
        })->count();

        if ($jumlahMahasiswa > 0) {
            $alasan[] = "Masih ada {$jumlahMahasiswa} akun mahasiswa di database.";
        }

        $jumlahPeserta = PesertaMagang::count();

        if ($jumlahPeserta > 0) {
            $alasan[] = "Masih ada {$jumlahPeserta} data peserta magang di database.";
        }

        return [
            'kosong' => empty($alasan),
            'alasan' => $alasan,
        ];
    }

    /**
     * Peta email => id user untuk semua Dosen di SIPENA.
     * Email dijadikan huruf kecil supaya pencocokan tidak sensitif huruf besar/kecil.
     *
     * @return array<string, int>
     */
    protected function petaDosenSipena(): array
    {
        $peta = [];

        foreach (User::role('Dosen')->get(['id', 'email']) as $dosen) {
            $peta[strtolower(trim($dosen->email))] = $dosen->id;
        }

        return $peta;
    }

    /**
     * Cocokkan satu email instruktur logbook dengan Dosen SIPENA.
     *
     * @param  array<string, int>  $peta  hasil petaDosenSipena()
     * @return array{cocok: bool, id_dosen: int|null, pesan: string}
     */
    protected function cocokkanDosen(string $emailInstruktur, array $peta): array
    {
        $email = strtolower(trim($emailInstruktur));

        if ($email === '') {
            return [
                'cocok' => false,
                'id_dosen' => null,
                'pesan' => 'Dosen pembimbing di logbook kosong (instruktur tidak ada atau emailnya hilang).',
            ];
        }

        if (!isset($peta[$email])) {
            return [
                'cocok' => false,
                'id_dosen' => null,
                'pesan' => "Dosen {$email} ada di logbook tetapi tidak terdaftar sebagai Dosen di SIPENA.",
            ];
        }

        return [
            'cocok' => true,
            'id_dosen' => $peta[$email],
            'pesan' => 'Dosen cocok dengan data SIPENA.',
        ];
    }

    /**
     * Path absolut file sementara di disk.
     */
    protected function pathLengkap(string $tempPath): string
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(self::TEMP_DISK);

        return $disk->path($tempPath);
    }

    /**
     * Tahap 1: simpan file sementara, lalu periksa semua baris.
     * TIDAK menyimpan apa pun ke database.
     * Hasilnya dipakai controller untuk disimpan di session.
     */
    public function preview(UploadedFile $file): array
    {
        $tempPath = $file->storeAs(
            self::TEMP_DIR,
            Str::uuid() . '.xlsx',
            self::TEMP_DISK
        );

        try {
            $hasil = $this->analisis($tempPath);
        } catch (\Throwable $e) {
            // File gagal dibaca: jangan tinggalkan sampah di storage
            $this->batal($tempPath);
            throw $e;
        }

        // File dengan error struktur (bukan export logbook, kosong, dsb.)
        // tidak akan dipakai lagi, jadi langsung dihapus.
        if ($hasil['error_file'] !== null) {
            $this->batal($tempPath);
            $hasil['temp_path'] = null;

            return $hasil;
        }

        $hasil['temp_path'] = $tempPath;

        return $hasil;
    }

    /**
     * Batal: hapus file sementara. Tidak ada yang disimpan.
     * Hanya file di folder sementara import ini yang boleh dihapus.
     */
    public function batal(?string $tempPath): void
    {
        if (!$tempPath || !str_starts_with($tempPath, self::TEMP_DIR . '/')) {
            return;
        }

        Storage::disk(self::TEMP_DISK)->delete($tempPath);
    }

    /**
     * Baca file sementara dan periksa SETIAP baris. Tidak menyimpan apa pun.
     *
     * Aturan: satu baris dianggap 'cocok' hanya kalau semua pemeriksaan lolos.
     * Tombol Simpan hanya boleh muncul kalau 'bisa_simpan' = true, yaitu
     * semua baris cocok DAN syarat data kosong terpenuhi.
     */
    public function analisis(string $tempPath): array
    {
        $syarat = $this->cekSyaratKosong();

        $raw = new LogbookRawImport();
        Excel::import($raw, $this->pathLengkap($tempPath));

        $hasil = [
            'error_file' => null,
            'syarat' => $syarat,
            'rows' => [],
            'summary' => ['total' => 0, 'cocok' => 0, 'tidak_cocok' => 0],
            'bisa_simpan' => false,
        ];

        if (empty($raw->rows)) {
            $hasil['error_file'] = 'File kosong atau tidak ada baris data.';
            return $hasil;
        }

        $hilang = $raw->kolomHilang();
        if (!empty($hilang)) {
            $hasil['error_file'] = 'File bukan hasil export logbook. Kolom tidak ditemukan: '
                . implode(', ', $hilang) . '.';
            return $hasil;
        }

        $petaDosen = $this->petaDosenSipena();
        $nimDiFile = [];
        $emailDiFile = [];

        foreach ($raw->rows as $i => $row) {
            $baris = $i + 2; // baris 1 = heading

            $name = trim((string) ($row['name'] ?? ''));
            $email = strtolower(trim((string) ($row['email'] ?? '')));
            $nim = trim((string) ($row['nim'] ?? ''));
            $perusahaan = trim((string) ($row['nama_perusahaan'] ?? ''));
            $alamat = trim((string) ($row['alamat_perusahaan'] ?? ''));
            $emailInstruktur = strtolower(trim((string) ($row['email_instruktur'] ?? '')));
            $namaInstruktur = trim((string) ($row['nama_instruktur'] ?? ''));
            $tahun = trim((string) ($row['tahun_ajaran'] ?? ''));

            // Baris benar-benar kosong: lewati diam-diam
            if ($name === '' && $email === '' && $nim === '' && $perusahaan === '') {
                continue;
            }

            $masalah = [];

            // 1. Kelengkapan data mahasiswa
            if ($name === '') {
                $masalah[] = 'Nama mahasiswa kosong.';
            }
            if ($email === '') {
                $masalah[] = 'Email mahasiswa kosong.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $masalah[] = "Format email \"{$email}\" tidak valid.";
            }
            if ($nim === '') {
                $masalah[] = 'NIM kosong.';
            }
            if ($perusahaan === '') {
                $masalah[] = 'Nama perusahaan kosong.';
            }

            if ($tahun === '') {
                $masalah[] = 'Tahun ajaran kosong di logbook.';
            } elseif (!preg_match('/^\d{4}\/\d{4}$/', $tahun)) {
                $masalah[] = "Format tahun ajaran \"{$tahun}\" tidak valid (harus seperti 2026/2027).";
            }

            // 2. Duplikat di dalam file
            if ($nim !== '') {
                if (isset($nimDiFile[$nim])) {
                    $masalah[] = "NIM {$nim} duplikat dengan baris {$nimDiFile[$nim]} (mahasiswa punya lebih dari satu internship aktif di logbook).";
                } else {
                    $nimDiFile[$nim] = $baris;
                }
            }
            if ($email !== '') {
                if (isset($emailDiFile[$email])) {
                    $masalah[] = "Email {$email} duplikat dengan baris {$emailDiFile[$email]}.";
                } else {
                    $emailDiFile[$email] = $baris;
                }
            }

            // 3. Bentrok dengan data yang sudah ada di SIPENA
            if ($email !== '' && User::where('email', $email)->exists()) {
                $masalah[] = "Email {$email} sudah dipakai akun lain di SIPENA.";
            }
            if ($nim !== '' && UserDetail::where('nim', $nim)->exists()) {
                $masalah[] = "NIM {$nim} sudah terdaftar di SIPENA.";
            }

            // 4. Dosen pembimbing harus ada di SIPENA (data master = logbook)
            $dosen = $this->cocokkanDosen($emailInstruktur, $petaDosen);
            if (!$dosen['cocok']) {
                $masalah[] = $dosen['pesan'];
            }

            $cocok = empty($masalah);

            $hasil['rows'][] = [
                'row' => $baris,
                'name' => $name,
                'email' => $email,
                'nim' => $nim,
                'prodi' => trim((string) ($row['prodi'] ?? '')),
                'angkatan' => trim((string) ($row['angkatan'] ?? '')),
                'tahun' => $tahun,
                'perusahaan' => $perusahaan,
                'alamat' => $alamat,
                'email_instruktur' => $emailInstruktur,
                'nama_instruktur' => $namaInstruktur,
                'id_dosen' => $dosen['id_dosen'],
                'perusahaan_sudah_ada' => $perusahaan !== ''
                    && DataPerusahaanMagang::where('nama', $perusahaan)->exists(),
                'status' => $cocok ? 'cocok' : 'tidak_cocok',
                'masalah' => $masalah,
            ];

            $hasil['summary']['total']++;
            $hasil['summary'][$cocok ? 'cocok' : 'tidak_cocok']++;
        }

        if ($hasil['summary']['total'] === 0) {
            $hasil['error_file'] = 'Tidak ada baris data yang bisa dibaca.';
            return $hasil;
        }

        $hasil['bisa_simpan'] = $hasil['summary']['tidak_cocok'] === 0 && $syarat['kosong'];

        return $hasil;
    }

    /**
     * Tahap 2: dipanggil setelah admin klik "Simpan".
     *
     * File dibaca dan diperiksa ULANG di dalam transaksi, jadi kalau ada
     * perubahan data sejak preview (misalnya admin lain menambah mahasiswa),
     * penyimpanan dibatalkan. Semua atau tidak sama sekali.
     *
     * @throws \RuntimeException kalau file hilang, syarat tidak terpenuhi,
     *                           ada baris tidak cocok, atau master data kurang.
     */
    public function commit(string $tempPath): array
    {
        if (!str_starts_with($tempPath, self::TEMP_DIR . '/')
            || !Storage::disk(self::TEMP_DISK)->exists($tempPath)) {
            throw new \RuntimeException('File sementara sudah tidak ada (mungkin sudah kadaluarsa). Silakan upload ulang.');
        }

        $role = Role::where('name', self::ROLE_MAHASISWA)->first();
        if (!$role) {
            throw new \RuntimeException('Role "' . self::ROLE_MAHASISWA . '" belum ada di SIPENA.');
        }

        $statusPembimbing = StatusDosen::where('status_dosen', self::NAMA_STATUS_PEMBIMBING)->first();
        if (!$statusPembimbing) {
            throw new \RuntimeException('Status dosen "' . self::NAMA_STATUS_PEMBIMBING . '" tidak ditemukan di SIPENA.');
        }

        $ringkasan = DB::transaction(function () use ($tempPath, $role, $statusPembimbing) {
            $hasil = $this->analisis($tempPath);

            if ($hasil['error_file'] !== null) {
                throw new \RuntimeException($hasil['error_file']);
            }

            if (!$hasil['syarat']['kosong']) {
                throw new \RuntimeException(
                    'Data mahasiswa di SIPENA tidak kosong lagi: ' . implode(' ', $hasil['syarat']['alasan'])
                );
            }

            if ($hasil['summary']['tidak_cocok'] > 0) {
                throw new \RuntimeException(
                    "Masih ada {$hasil['summary']['tidak_cocok']} baris yang tidak cocok. Data tidak disimpan."
                );
            }

            $jumlahMahasiswa = 0;
            $jumlahPerusahaanBaru = 0;
            $jumlahPenempatan = 0;
            $jumlahPembimbing = 0;

            foreach ($hasil['rows'] as $row) {
                // 1. Akun mahasiswa. Login lewat Google, jadi password diacak.
                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make(Str::random(32)),
                ]);

                UserDetail::create([
                    'user_id' => $user->id,
                    'nim' => $row['nim'],
                    'jabatan' => 'Mahasiswa',
                ]);

                $user->assignRole($role->name);
                $jumlahMahasiswa++;

                // 2. Perusahaan: pakai yang sudah ada, kalau belum ada dibuat baru.
                $perusahaan = DataPerusahaanMagang::where('nama', $row['perusahaan'])->first();
                if (!$perusahaan) {
                    $perusahaan = DataPerusahaanMagang::create([
                        'nama' => $row['perusahaan'],
                        'alamat' => $row['alamat'],
                    ]);
                    $jumlahPerusahaanBaru++;
                }

                // 3. Penempatan magang. Jadwal presentasi diisi admin belakangan.
                PesertaMagang::create([
                    'id_mahasiswa' => $user->id,
                    'id_perusahaan' => $perusahaan->id,
                    'tanggal_presentasi' => null,
                    'tahun' => $row['tahun'],
                    'jam_presentasi' => null,
                    'jam_presentasi_selesai' => null,
                    'lokasi' => null,
                ]);
                $jumlahPenempatan++;

                // 4. Dosen pembimbing (sudah dipastikan cocok oleh analisis).
                DataPengujiMagang::create([
                    'id_dosen' => (int) $row['id_dosen'],
                    'status_dosen' => (int) $statusPembimbing->id,
                    'id_mahasiswa' => $user->id,
                ]);
                $jumlahPembimbing++;
            }

            return [
                'rows' => $hasil['rows'],
                'mahasiswa' => $jumlahMahasiswa,
                'perusahaan_baru' => $jumlahPerusahaanBaru,
                'penempatan' => $jumlahPenempatan,
                'pembimbing' => $jumlahPembimbing,
            ];
        });

        // Transaksi sukses: file sementara tidak diperlukan lagi.
        $this->batal($tempPath);

        return $ringkasan;
    }
}
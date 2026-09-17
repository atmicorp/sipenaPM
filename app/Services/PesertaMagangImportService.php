<?php

namespace App\Services;

use App\Imports\PesertaMagangRawImport;
use App\Models\DataPerusahaanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

/**
 * Import Peserta Magang dengan alur: upload -> preview (belum simpan)
 * -> admin konfirmasi -> baru disimpan ke database. Baris yang error /
 * duplikat akan dilewati (tidak menggagalkan baris lain yang valid),
 * dan tetap dilaporkan ke admin.
 *
 * PENTING: role untuk semua user yang masuk lewat jalur ini SELALU
 * dipaksa "Mahasiswa", apa pun isi kolom 'role' di file Excel-nya.
 */
class PesertaMagangImportService
{
    protected const ROLE_MAHASISWA = 'Mahasiswa';
    protected const TEMP_DISK = 'local';
    protected const TEMP_DIR = 'temp-imports';

    /**
     * Tahap 1: baca & validasi file yang baru diupload, TANPA menyimpan
     * apa pun ke database. File disimpan sementara di storage supaya
     * bisa dipakai lagi saat admin klik "Simpan".
     */
    public function preview(UploadedFile $file): array
    {
        $tempPath = $file->storeAs(self::TEMP_DIR, Str::uuid().'.xlsx', self::TEMP_DISK);

        $result = $this->readAndValidate($tempPath, persist: false);
        $result['temp_path'] = $tempPath;

        return $result;
    }

    /**
     * Tahap 2: dipanggil setelah admin klik "Simpan" di halaman preview.
     * Membaca ULANG file sementara yang sama (supaya data terbaru di DB
     * ikut dicek ulang, menghindari race condition), lalu benar-benar
     * menyimpan baris-baris yang valid.
     */
    public function commit(string $tempPath): array
    {
        if (!Storage::disk(self::TEMP_DISK)->exists($tempPath)) {
            throw new \RuntimeException('File sementara sudah tidak ada (mungkin sudah kadaluarsa). Silakan upload ulang.');
        }

        $result = DB::transaction(function () use ($tempPath) {
            return $this->readAndValidate($tempPath, persist: true);
        });

        Storage::disk(self::TEMP_DISK)->delete($tempPath);

        return $result;
    }

    /**
     * Batal: hapus saja file sementaranya, tidak ada yang disimpan.
     */
    public function cancel(string $tempPath): void
    {
        Storage::disk(self::TEMP_DISK)->delete($tempPath);
    }

    protected function readAndValidate(string $tempPath, bool $persist): array
    {
        $fullPath = storage_path('app/'.$tempPath);

        $raw = new PesertaMagangRawImport();
        Excel::import($raw, $fullPath);

        [$mahasiswa, $nimMap] = $this->processMahasiswa($raw->sheet1->rows, $persist);
        [$perusahaan, $namaMap] = $this->processPerusahaan($raw->sheet2->rows, $persist);
        $magang = $this->processMagang($raw->sheet3->rows, $nimMap, $namaMap, $persist);

        return [
            'mahasiswa' => $mahasiswa,
            'perusahaan' => $perusahaan,
            'magang' => $magang,
            'summary' => $this->summarize($mahasiswa, $perusahaan, $magang),
        ];
    }

    /**
     * @return array{0: array, 1: array<string,int>} [daftar hasil, map nim => user_id]
     */
    protected function processMahasiswa(array $rows, bool $persist): array
    {
        $result = [];
        $nimMap = [];
        $nimSeenInFile = [];
        $role = Role::where('name', self::ROLE_MAHASISWA)->first();

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // baris 1 = heading

            $name = trim((string) ($row['name'] ?? ''));
            $email = trim((string) ($row['email'] ?? ''));
            $password = (string) ($row['password'] ?? '');
            $nim = trim((string) ($row['nim'] ?? ''));

            // Baris kosong (sisa format / bekas baris catatan) -> lewati diam-diam
            if ($name === '' && $email === '' && $nim === '') {
                continue;
            }

            $entry = ['row' => $rowNum, 'name' => $name, 'email' => $email, 'nim' => $nim];

            if ($name === '' || $email === '' || $password === '' || $nim === '') {
                $result[] = $entry + [
                    'status' => 'error',
                    'message' => 'Data tidak lengkap. Kolom name, email, password, dan nim wajib diisi.',
                ];
                continue;
            }

            if (isset($nimSeenInFile[$nim])) {
                $result[] = $entry + [
                    'status' => 'error',
                    'message' => "NIM duplikat dengan baris {$nimSeenInFile[$nim]} pada file ini.",
                ];
                continue;
            }
            $nimSeenInFile[$nim] = $rowNum;

            $existingByEmail = User::where('email', $email)->first();
            $existingByNim = UserDetail::where('nim', $nim)->first();

            if ($existingByEmail || $existingByNim) {
                $userId = $existingByEmail->id ?? $existingByNim->user_id ?? null;
                if ($userId) {
                    $nimMap[$nim] = $userId;
                }
                $result[] = $entry + [
                    'status' => 'dilewati',
                    'message' => $existingByEmail
                        ? 'Email sudah terdaftar. Data mahasiswa ini tidak diubah.'
                        : 'NIM sudah terdaftar. Data mahasiswa ini tidak diubah.',
                ];
                continue;
            }

            if ($persist) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);

                UserDetail::create([
                    'user_id' => $user->id,
                    'nim' => $nim,
                    'nik' => $row['nik'] ?? null,
                    'nidn' => $row['nidn'] ?? null,
                    'gelar_depan' => $row['gelar_depan'] ?? null,
                    'gelar_belakang' => $row['gelar_belakang'] ?? null,
                    'jabatan' => $row['jabatan'] ?? null,
                    'photo' => $row['photo'] ?? null,
                ]);

                if ($role) {
                    $user->assignRole($role->name);
                }

                $nimMap[$nim] = $user->id;
            }

            $result[] = $entry + [
                'status' => 'valid',
                'message' => 'Siap disimpan sebagai Mahasiswa baru.',
            ];
        }

        return [$result, $nimMap];
    }

    /**
     * @return array{0: array, 1: array<string,int>} [daftar hasil, map nama perusahaan => id]
     */
    protected function processPerusahaan(array $rows, bool $persist): array
    {
        $result = [];
        $namaMap = [];
        $namaSeenInFile = [];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2;

            $nama = trim((string) ($row['nama'] ?? ''));
            $alamat = trim((string) ($row['alamat'] ?? ''));

            if ($nama === '' && $alamat === '') {
                continue;
            }

            $entry = ['row' => $rowNum, 'nama' => $nama];

            if ($nama === '') {
                $result[] = $entry + [
                    'status' => 'error',
                    'message' => 'Nama perusahaan wajib diisi.',
                ];
                continue;
            }

            if (isset($namaSeenInFile[$nama])) {
                $result[] = $entry + [
                    'status' => 'error',
                    'message' => "Nama perusahaan duplikat dengan baris {$namaSeenInFile[$nama]} pada file ini.",
                ];
                continue;
            }
            $namaSeenInFile[$nama] = $rowNum;

            $existing = DataPerusahaanMagang::where('nama', $nama)->first();
            if ($existing) {
                $namaMap[$nama] = $existing->id;
                $result[] = $entry + [
                    'status' => 'dilewati',
                    'message' => 'Perusahaan ini sudah ada di database. Data tidak diubah.',
                ];
                continue;
            }

            if ($persist) {
                $perusahaan = DataPerusahaanMagang::create([
                    'nama' => $nama,
                    'alamat' => $alamat,
                ]);
                $namaMap[$nama] = $perusahaan->id;
            }

            $result[] = $entry + [
                'status' => 'valid',
                'message' => 'Siap disimpan sebagai perusahaan baru.',
            ];
        }

        return [$result, $namaMap];
    }

    protected function processMagang(array $rows, array $nimMap, array $namaMap, bool $persist): array
    {
        $result = [];
        $nimUsedInFile = [];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2;

            $nim = trim((string) ($row['nim_mahasiswa'] ?? ''));
            $namaPerusahaan = trim((string) ($row['nama_perusahaan'] ?? ''));

            if ($nim === '' && $namaPerusahaan === '') {
                continue;
            }

            $entry = ['row' => $rowNum, 'nim' => $nim, 'nama_perusahaan' => $namaPerusahaan];

            if ($nim === '' || $namaPerusahaan === '') {
                $result[] = $entry + [
                    'status' => 'error',
                    'message' => 'Kolom nim_mahasiswa dan nama_perusahaan wajib diisi.',
                ];
                continue;
            }

            $idMahasiswa = $nimMap[$nim]
                ?? optional(UserDetail::where('nim', $nim)->first())->user_id;

            if (!$idMahasiswa) {
                $result[] = $entry + [
                    'status' => 'error',
                    'message' => "NIM {$nim} tidak ditemukan di Sheet1 (Data Mahasiswa) maupun database.",
                ];
                continue;
            }

            $idPerusahaan = $namaMap[$namaPerusahaan]
                ?? optional(DataPerusahaanMagang::where('nama', $namaPerusahaan)->first())->id;

            if (!$idPerusahaan) {
                $result[] = $entry + [
                    'status' => 'error',
                    'message' => "Perusahaan \"{$namaPerusahaan}\" tidak ditemukan di Sheet2 (Data Perusahaan) maupun database.",
                ];
                continue;
            }

            if (isset($nimUsedInFile[$nim])) {
                $result[] = $entry + [
                    'status' => 'error',
                    'message' => "NIM {$nim} duplikat penempatan dengan baris {$nimUsedInFile[$nim]} pada file ini.",
                ];
                continue;
            }

            if (PesertaMagang::where('id_mahasiswa', $idMahasiswa)->exists()) {
                $result[] = $entry + [
                    'status' => 'dilewati',
                    'message' => 'Mahasiswa ini sudah punya data penempatan magang sebelumnya.',
                ];
                continue;
            }
            $nimUsedInFile[$nim] = $rowNum;

            if ($persist) {
                PesertaMagang::create([
                    'id_mahasiswa' => $idMahasiswa,
                    'id_perusahaan' => $idPerusahaan,
                    'tanggal_presentasi' => $this->excelSerialToDate($row['tanggal_presentasi'] ?? null),
                    'jam_presentasi' => $this->excelSerialToTime($row['jam_presentasi'] ?? null),
                    'jam_presentasi_selesai' => $this->excelSerialToTime($row['jam_presentasi_selesai'] ?? null),
                    'lokasi' => $row['lokasi'] ?? null,
                ]);
            }

            $result[] = $entry + [
                'status' => 'valid',
                'message' => 'Siap disimpan sebagai penempatan magang baru.',
            ];
        }

        return $result;
    }

    protected function excelSerialToDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return Carbon::createFromFormat('Y-m-d', '1900-01-01')
                ->addDays((float) $value - 2)
                ->format('Y-m-d');
        }
        return (string) $value;
    }

    protected function excelSerialToTime($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return Carbon::createFromTimeString('00:00:00')
                ->addMinutes((int) round(((float) $value) * 1440))
                ->format('H:i:s');
        }
        return (string) $value;
    }

    protected function summarize(array $mahasiswa, array $perusahaan, array $magang): array
    {
        $count = function (array $rows) {
            return [
                'total' => count($rows),
                'valid' => count(array_filter($rows, fn ($r) => $r['status'] === 'valid')),
                'dilewati' => count(array_filter($rows, fn ($r) => $r['status'] === 'dilewati')),
                'error' => count(array_filter($rows, fn ($r) => $r['status'] === 'error')),
            ];
        };

        return [
            'mahasiswa' => $count($mahasiswa),
            'perusahaan' => $count($perusahaan),
            'magang' => $count($magang),
        ];
    }
}
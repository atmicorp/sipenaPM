<?php

namespace App\Http\Controllers;

use App\Models\DataPengujiMagang;
use App\Models\DataPerusahaanMagang;
use App\Models\PesertaMagang;
use App\Models\StatusDosen;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\LogbookImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Halaman "Tambah Data Peserta Magang": tambah manual (susulan),
 * import Excel, dan import dari Logbook dalam satu pintu.
 */
class TambahPesertaMagangController extends Controller
{
    protected const ROLE_MAHASISWA = 'Mahasiswa';
    protected const NAMA_STATUS_PEMBIMBING = 'Pembimbing';

    public function __construct(protected LogbookImportService $logbook)
    {
    }

    public function index()
    {
        return view('main.tambahpesertamagang', [
            'perusahaan' => DataPerusahaanMagang::orderBy('nama')->get(),
            'dosen' => User::role('Dosen')->with('details')->orderBy('name')->get(),
            'syaratLogbook' => $this->logbook->cekSyaratKosong(),
        ]);
    }

    /**
     * Tambah satu mahasiswa susulan (di luar logbook) beserta penempatan
     * dan dosen pembimbingnya. Semua atau tidak sama sekali.
     */
    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'nim' => ['required', 'string', 'max:20', Rule::unique(UserDetail::class, 'nim')],
            'perusahaan_id' => 'nullable|required_without:perusahaan_baru|integer',
            'perusahaan_baru' => 'nullable|string|max:255',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'id_dosen' => 'required|integer',
            'tanggal_presentasi' => 'nullable|date',
            'tahun' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'jam_presentasi' => 'nullable|date_format:H:i',
            'jam_presentasi_selesai' => 'nullable|date_format:H:i',
            'lokasi' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'Email sudah dipakai akun lain.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'perusahaan_id.required_without' => 'Pilih perusahaan, atau isi nama perusahaan baru.',
            'id_dosen.required' => 'Pilih dosen pembimbing.',
            'tahun.required' => 'Tahun ajaran wajib diisi.',
            'tahun.regex' => 'Format tahun harus seperti 2026/2027.',
        ]);

        $namaBaru = trim((string) ($data['perusahaan_baru'] ?? ''));

        // Dosen harus benar-benar berole Dosen
        $dosenValid = User::role('Dosen')->where('id', $data['id_dosen'])->exists();
        if (!$dosenValid) {
            return back()->withInput()->with('error', 'Dosen pembimbing tidak valid.')->with('tab', 'manual');
        }

        $role = Role::where('name', self::ROLE_MAHASISWA)->first();
        $statusPembimbing = StatusDosen::where('status_dosen', self::NAMA_STATUS_PEMBIMBING)->first();

        if (!$role || !$statusPembimbing) {
            return back()->withInput()
                ->with('error', 'Role "Mahasiswa" atau status dosen "Pembimbing" belum ada di SIPENA.')
                ->with('tab', 'manual');
        }

        try {
            DB::transaction(function () use ($data, $namaBaru, $role, $statusPembimbing) {
                // Perusahaan: nama baru diutamakan (dicek dulu supaya tidak dobel)
                if ($namaBaru !== '') {
                    $perusahaan = DataPerusahaanMagang::firstOrCreate(
                        ['nama' => $namaBaru],
                        ['alamat' => trim((string) ($data['alamat_perusahaan'] ?? ''))]
                    );
                } else {
                    $perusahaan = DataPerusahaanMagang::find($data['perusahaan_id']);
                    if (!$perusahaan) {
                        throw new \RuntimeException('Perusahaan yang dipilih tidak ditemukan.');
                    }
                }

                $user = User::create([
                    'name' => trim($data['name']),
                    'email' => strtolower(trim($data['email'])),
                    'password' => Hash::make(Str::random(32)), // login lewat Google
                ]);

                UserDetail::create([
                    'user_id' => $user->id,
                    'nim' => trim($data['nim']),
                    'jabatan' => 'Mahasiswa',
                ]);

                $user->assignRole($role->name);

                PesertaMagang::create([
                    'id_mahasiswa' => $user->id,
                    'id_perusahaan' => $perusahaan->id,
                    'tahun' => $data['tahun'] ?? null,
                    'tanggal_presentasi' => $data['tanggal_presentasi'] ?? null,
                    'jam_presentasi' => $data['jam_presentasi'] ?? null,
                    'jam_presentasi_selesai' => $data['jam_presentasi_selesai'] ?? null,
                    'lokasi' => $data['lokasi'] ?? null,
                ]);

                DataPengujiMagang::create([
                    'id_dosen' => (int) $data['id_dosen'],
                    'status_dosen' => (int) $statusPembimbing->id,
                    'id_mahasiswa' => $user->id,
                ]);
            });
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Data tidak disimpan: ' . $e->getMessage())
                ->with('tab', 'manual');
        }

        return redirect()->route('tambahpesertamagang')
            ->with('success', 'Mahasiswa ' . trim($data['name']) . ' berhasil ditambahkan beserta penempatan dan dosen pembimbingnya.')
            ->with('tab', 'manual');
    }
}
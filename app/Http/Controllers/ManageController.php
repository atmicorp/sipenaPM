<?php

namespace App\Http\Controllers;

use App\Models\AspekPenilaianTA;
use App\Models\AspekPenilaianTAIndividu;
use App\Models\DataAspekPenilaianMagang;
use App\Models\DataAspekPenilaianSP;
use App\Models\DataPengujiMagang;
use App\Models\DataPengujiTa;
use App\Models\DataPerusahaanMagang;
use App\Models\JadwalTA;
use App\Models\KategoriTA;
use App\Models\KelompokTA;
use App\Models\PenilaianMagang;
use App\Models\PenilaianSP;
use App\Models\PenilaianTA;
use App\Models\PenilaianTAindividu;
use App\Models\PesertaMagang;
use App\Models\PesertaTA;
use App\Models\StatusDosen;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\VerifikasiKelompokTA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;


class ManageController extends Controller
{
    public function viewpenempatanmagang()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $pesertamagang = PesertaMagang::with('usermahasiswa', 'perusahaanmagang')->get();
            $perusahaanmagang = DataPerusahaanMagang::all();

            // dd($pesertamagang);
           
            return view("main.viewpenempatanmagang", compact('pesertamagang','perusahaanmagang'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }   
    }

    public function vpesertataupdate($id, $idKategoriTa)
    {
        // dd("sss");
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            $statusdosen = StatusDosen::where('status_dosen', '!=', 'Penguji')->get();

            $mahasiswa = User::role('Mahasiswa')
                ->where(function ($query) {
                    $query->whereDoesntHave('pesertaTA')
                        ->orWhereHas('pesertaTA', function ($q) {
                            $q->whereNull('id_kelompok_ta')
                                ->orWhere('id_kelompok_ta', '');
                        });
                })
                ->get();

            $dosen = User::role('Dosen')->get();

            $pesertaTA = PesertaTA::with('usermahasiswaTA')
                ->where('id_kelompok_ta', $id)
                ->get();

            $pengujiTA = DataPengujiTa::with('userdosenTA', 'statusdosenTA')
                ->where('id_kelompok_ta', $id)
                ->where('id_kategori_ta', $idKategoriTa)
                ->get();

            // Guard dosen: tandai per baris apakah sudah ada nilai (kategori yang sedang dibuka)
            $pengujiTA->each(function ($item) {
                $item->sudah_dinilai = PenilaianTA::where('id_data_pengujiTA', $item->id)->exists()
                    || PenilaianTAindividu::where('id_data_pengujiTA', $item->id)->exists();
            });

            $kelompokTA = KelompokTA::where('id', $id)->first();
            $kategoriTA = KategoriTA::where('id', $idKategoriTa)->first();

            if (!$kelompokTA) {
                return redirect()->route('manageTA')->with('error', 'Kelompok TA tidak ditemukan');
            }

            if (!$kategoriTA) {
                return redirect()->route('manageTA')->with('error', 'Kategori TA tidak ditemukan');
            }

            // Guard dosen: level kategori yang sedang dibuka
            $sudahDinilai = PenilaianTA::where('id_kelompok_ta', $id)
                ->where('id_kategori_TA', $idKategoriTa)
                ->exists()
                ||
                PenilaianTAindividu::whereIn('id_mahasiswa', $pesertaTA->pluck('id_mahasiswa'))
                    ->where('id_kategori_TA', $idKategoriTa)
                    ->exists();

            // Guard mahasiswa: lintas SEMUA kategori untuk kelompok ini
            $mahasiswaLocked = PenilaianTA::where('id_kelompok_ta', $id)->exists()
                ||
                PenilaianTAindividu::whereIn('id_mahasiswa', $pesertaTA->pluck('id_mahasiswa'))->exists();

            return view("main.viewupdateta", compact(
                'pesertaTA', 'pengujiTA', 'kelompokTA', 'dosen', 'mahasiswa',
                'statusdosen', 'idKategoriTa', 'kategoriTA', 'sudahDinilai', 'mahasiswaLocked'
            ));
        }
        // catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'Data tidak ditemukan');
        // }

        catch (\Exception $e) {
            Log::error('vpesertataupdate error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        
    }
        public function storeusermanual(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'role' => 'required|in:Admin,Dosen,Mahasiswa',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'nik' => 'required_if:role,Dosen|nullable|string|max:20',
                'nidn' => 'nullable|string|max:20',
                'gelar_depan' => 'nullable|string|max:20',
                'gelar_belakang' => 'nullable|string|max:20',
                'nim' => 'required_if:role,Mahasiswa|nullable|string|max:20',
            ]);

            DB::beginTransaction();

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $user->assignRole($validatedData['role']);

            // Admin tidak punya baris di user_details
            if ($validatedData['role'] === 'Dosen') {
                UserDetail::create([
                    'user_id' => $user->id,
                    'nik' => $validatedData['nik'],
                    'nidn' => $validatedData['nidn'] ?? null,
                    'gelar_depan' => $validatedData['gelar_depan'] ?? null,
                    'gelar_belakang' => $validatedData['gelar_belakang'] ?? null,
                    'jabatan' => 'Dosen/Instruktur',
                ]);
            } elseif ($validatedData['role'] === 'Mahasiswa') {
                UserDetail::create([
                    'user_id' => $user->id,
                    'nim' => $validatedData['nim'],
                    'jabatan' => 'Mahasiswa',
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function aspekpenilaianindividu()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $aspekindividu = AspekPenilaianTAIndividu::with('kategoriTA')->get();
            $kategoriTa = KategoriTA::get();

            // Kumpulkan id_kategori_ta yang aspek penilaiannya sudah pernah dipakai
            // untuk menyimpan nilai individu (PenilaianTAindividu)
            $kategoriTerkunci = PenilaianTAindividu::with('aspekpenilaianTAindividu')
                ->get()
                ->pluck('aspekpenilaianTAindividu.id_kategori_ta')
                ->filter()
                ->unique()
                ->values();

            return view("main.setuppenilaianindividu", compact('aspekindividu', 'kategoriTa', 'kategoriTerkunci'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }  
    }

    public function aspekpenilaianta($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $aspekpenilaianta = AspekPenilaianTA::with('kategoriTA')->where('id_kategori_ta', $id)->get();
            $aspekpenilaianindividu = AspekPenilaianTAIndividu::with('kategoriTA')->where('id_kategori_ta', $id)->get();

            $totalporsi = $aspekpenilaianta->sum('porsi_penilaian') + $aspekpenilaianindividu->sum('porsi_penilaian');
            $kategoriTa = KategoriTA::where('id', $id)->first();

            if (!$kategoriTa) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            // Guard: sudah dinilai untuk kategori TA ini
            $sudahDinilai = PenilaianTA::where('id_kategori_TA', $id)->exists();

            return view("main.setupjpenilaianta", compact('aspekpenilaianta','kategoriTa' ,'totalporsi', 'sudahDinilai'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }  
    }

    public function setupjadwalta($id)
    {
        try {
            $user = Auth::user();
       
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $jadwalta = JadwalTA::with(['kategoriTA','kelompokTA'])->where('id_kategori_ta', $id)->get();

            $verifikasistatus = VerifikasiKelompokTA::with(['KelompokTA','kategoriTA'])->where('id_kategori_ta', $id)->get();


            $verifikasiMap = $verifikasistatus->keyBy(function ($item) {
                return $item->id_kelompok_ta . '_' . $item->id_kategori_ta;
            });
            
            // Gabungkan data jadwal dengan status verifikasi
            $jadwaltaWithStatus = $jadwalta->map(function ($item) use ($verifikasiMap) {
                $key = $item->id_kelompok_ta . '_' . $item->id_kategori_ta;
                $status = $verifikasiMap[$key]->status ?? null;
            
                return [
                    'id' => $item->id,
                    'id_kelompok_ta' => $item->id_kelompok_ta,
                    'nama_kelompok_ta' => $item->kelompokTA->nama_kelompok,
                    'id_kategori_ta' => $item->id_kategori_ta,
                    'tanggal_presentasi' => $item->tanggal_presentasi,
                    'jam_presentasi' => $item->jam_presentasi,
                    'jam_presentasi_selesai' => $item->jam_presentasi_selesai,
                    'lokasi' => $item->lokasi,
                    'status' => $status,
                    // Jika ingin menyertakan relasi, bisa tambahkan:
                    'nama_kelompok' => $item->kelompokTA->nama_kelompok ?? null,
                    'nama_kategori' => $item->kategoriTA->nama_kategori ?? null,
                ];
            });
            
            // Optional: convert ke array biasa
            $jadwalArray = $jadwaltaWithStatus->toArray();
            
            // Debug hasilnya
            // dd($jadwalArray);
            $kategoriTa = KategoriTA::where('id', $id)->first();
            if (!$kategoriTa) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }
           
            return view("main.setupjadwalta", compact('jadwalArray','kategoriTa'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }  

    }

    public function updatejadwalta(Request $request, $id)
    {
        // dd($request, $id);
        try {

            $jadwalta = JadwalTA::where('id', $id)->first();
            // dd($jadwalta);
            $validatedData = $request->validate([
                'tanggal_presentasi' => 'nullable|string',
                'jam_presentasi' => 'nullable|string',
                'jam_presentasi_selesai' => 'nullable|string',
                'lokasi' => 'nullable|string',
            ]);
            $jadwalTA = JadwalTA::findOrFail($id);
            $jadwalTA->update($validatedData);

            // Cek jika semua value null atau kosong
            $isAllEmpty = empty($validatedData['tanggal_presentasi']) &&
                          empty($validatedData['jam_presentasi']) &&
                          empty($validatedData['jam_presentasi_selesai']) &&
                          empty($validatedData['lokasi']);
            
            $status = $isAllEmpty ? '0' : '1';
            
            VerifikasiKelompokTA::where('id_kelompok_ta', $jadwalta->id_kelompok_ta)
                ->where('id_kategori_ta', $jadwalta->id_kategori_ta)
                ->update(['status' => $status]);
           
            return redirect()->back()->with('success', 'Peserta berhasil diupdate.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }  

    }

   public function manageTA()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            $kelompokTA = KelompokTA::get();
            $kategoriTA = KategoriTA::orderBy('id')->get(); // ambil semua kategori (1,2,3)

            return view("main.viewmanageta", compact('kelompokTA', 'kategoriTA'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
    }

        public function destroypesertata($id)
        {
            try {
                $peserta = PesertaTA::findOrFail($id);

                $terkunci = PenilaianTA::where('id_kelompok_ta', $peserta->id_kelompok_ta)->exists()
                    || PenilaianTAindividu::where('id_mahasiswa', $peserta->id_mahasiswa)->exists();

                if ($terkunci) {
                    return redirect()->back()->with('error', 'Data mahasiswa tidak bisa dihapus karena penilaian TA sudah dimulai.');
                }

                $peserta->update(['id_kelompok_ta' => null]);

                return redirect()->back()->with('success', 'Peserta berhasil dihapus.');
            }
            catch (\Exception $e) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            }
        }

        public function destroypengujita($id)
        {
            try {
                $penguji = DataPengujiTa::findOrFail($id);

                $sudahDinilai = PenilaianTA::where('id_data_pengujiTA', $penguji->id)->exists()
                    || PenilaianTAindividu::where('id_data_pengujiTA', $penguji->id)->exists();

                if ($sudahDinilai) {
                    return redirect()->back()->with('error', 'Data penguji tidak bisa dihapus karena sudah dilakukan penilaian.');
                }

                $penguji->delete();

                return redirect()->back()->with('success', 'Penguji berhasil dihapus.');
            }
            catch (\Exception $e) {
                return redirect()->back()->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
            }
        }
   
    public function dosentaupdate(Request $request)
    {
        try {
            // dd($request->all());
            $validatedData = $request->validate([
                'dosen' => 'required|array',
                'statusdosen' => 'required|array',
                'id_kel_TA' => 'required|integer',   
            ]);
        
            if (count($validatedData['dosen']) !== count(array_unique($validatedData['dosen']))) {
                return redirect()->back()->with('error', 'Duplikasi Nama Dosen, Silahkan Ulangi !');
            }
            
            foreach ($validatedData['dosen'] as $key => $dosenId) {

                $existingPeserta = DataPengujiTa::where('id_dosen', $dosenId) ->where('id_kelompok_ta', $request->id_mhs)
                ->exists();
                // jika sudah terdaftar, returnback
                if ($existingPeserta) {
                    return redirect()->back()->with('error', 'Data Dosen Sudah Terdaftar');
                }
                DataPengujiTa::create([
                    'id_dosen' => (int)$dosenId,
                    'status_dosen' => (int)$validatedData['statusdosen'][$key],
                    'id_kelompok_ta' => (int)$validatedData['id_kel_TA'],
                    
                     // $key digunakan mengambil ID perusahaan yang sesuai dengan id mahasiswa di atas
                ]);
            }
          
            return redirect()->back()->with('success', 'Penguji berhasil diupdate.');
        }
        catch (\Exception $e) {
                return redirect()->back()->with('error', 'Update Gagal,silahkan coba lagi');
        } 
       
    }

    public function updatekta(Request $request)
    {
        try {
            // dd($request->all());
            $validatedData = $request->validate([
                'id_kel_ta' => 'required|integer', 
                'sk' => 'required|string', 
                'judulta' => 'nullable|string|max:65535', 
            ]);
            // dd($validatedData);
            // Cari Kelompok TA berdasarkan ID
            $kelompokTA = KelompokTA::findOrFail($validatedData['id_kel_ta']);

            // Update judul TA
            $kelompokTA->judul_ta = $validatedData['judulta'];
            $kelompokTA->sk = $validatedData['sk'];
            $kelompokTA->save();
            return redirect()->back()->with('success', 'Data Berhasil Diupdate.');
        }
        catch (\Exception $e) {
                return redirect()->back()->with('error', 'Update Gagal,silahkan coba lagi'.  $e->getMessage());
        } 
       
    }

    public function updatekjudulta(Request $request)
    {
        try {
            // dd($request->all());
            $validatedData = $request->validate([
                'id_kel_ta' => 'required|integer', 
                'judulta' => 'required|string|max:65535',   
            ]);
            // dd($validatedData);
            // Cari Kelompok TA berdasarkan ID
            $kelompokTA = KelompokTA::findOrFail($validatedData['id_kel_ta']);

            // Update judul TA
            $kelompokTA->judul_ta = $validatedData['judulta'];
            $kelompokTA->save();
            return redirect()->back()->with('success', 'Data Berhasil Diupdate.');
        }
        catch (\Exception $e) {
                return redirect()->back()->with('error', 'Update Gagal,silahkan coba lagi');
        } 
       
       
    }

   public function pesertataupdate(Request $request, $id, $idKategoriTa)
    {
        try {
            // Validasi input (tanpa error jika null)
            $validatedData = $request->validate([
                'mahasiswa' => 'nullable|array',
                'dosen' => 'nullable|array',
                'statusdosen' => 'nullable|array',
            ]);

            $mahasiswa = $validatedData['mahasiswa'] ?? [];
            $dosen = $validatedData['dosen'] ?? [];
            $statusDosen = $validatedData['statusdosen'] ?? [];

            // ================= GUARD MAHASISWA (lintas semua kategori) =================
            $mahasiswaLocked = PenilaianTA::where('id_kelompok_ta', $id)->exists()
                ||
                PenilaianTAindividu::whereIn(
                    'id_mahasiswa',
                    PesertaTA::where('id_kelompok_ta', $id)->pluck('id_mahasiswa')
                )->exists();

            if (!empty($mahasiswa) && $mahasiswaLocked) {
                return redirect()->back()->with('error', 'Tidak bisa menambah mahasiswa, penilaian TA untuk kelompok ini sudah dimulai.');
            }

            // ================= GUARD DOSEN (khusus kategori yang sedang dibuka) =================
            $sudahDinilai = PenilaianTA::where('id_kelompok_ta', $id)
                ->where('id_kategori_TA', $idKategoriTa)
                ->exists()
                ||
                PenilaianTAindividu::whereIn(
                    'id_mahasiswa',
                    PesertaTA::where('id_kelompok_ta', $id)->pluck('id_mahasiswa')
                )
                    ->where('id_kategori_TA', $idKategoriTa)
                    ->exists();

            if (!empty($dosen) && $sudahDinilai) {
                return redirect()->back()->with('error', 'Tidak bisa menambah dosen, penilaian untuk kategori ini sudah dilakukan.');
            }

            // ================= PROSES MAHASISWA =================
            if (!empty($mahasiswa)) {
                if (count($mahasiswa) !== count(array_unique($mahasiswa))) {
                    return redirect()->back()->with('error', 'Duplikasi Nama Mahasiswa, Silahkan Ulangi !');
                }

                $existingPeserta = PesertaTA::whereIn('id_mahasiswa', $mahasiswa)
                    ->whereNotNull('id_kelompok_ta')
                    ->get();

                if ($existingPeserta->isNotEmpty()) {
                    $namaMahasiswa = $existingPeserta->first()->usermahasiswaTA->name;
                    $namakelompok = $existingPeserta->first()->kelompokTA->nama_kelompok;
                    return redirect()->back()->with('error', "$namaMahasiswa sudah masuk dalam kelompok $namakelompok, Silahkan Ulangi!");
                }

                foreach ($mahasiswa as $mhsId) {
                    PesertaTA::updateOrCreate(
                        ['id_mahasiswa' => $mhsId],
                        ['id_kelompok_ta' => $id]
                    );
                }
            }

            // ================= PROSES DOSEN =================
            if (!empty($dosen) && !empty($statusDosen)) {
                if (count($dosen) !== count(array_unique($dosen))) {
                    return redirect()->back()->with('error', 'Duplikasi Nama Dosen, Silahkan Ulangi !');
                }

                foreach ($dosen as $key => $dosenId) {
                    // Cek duplikasi HARUS termasuk id_kategori_ta,
                    // supaya dosen yang sama boleh jadi penguji di kategori lain
                    $existingPenguji = DataPengujiTa::where('id_dosen', $dosenId)
                        ->where('id_kelompok_ta', $id)
                        ->where('id_kategori_ta', $idKategoriTa)
                        ->exists();

                    if ($existingPenguji) {
                        return redirect()->back()->with('error', 'Data Dosen Sudah Terdaftar di kategori ini');
                    }

                    DataPengujiTa::create([
                        'id_dosen' => (int) $dosenId,
                        'status_dosen' => (int) ($statusDosen[$key] ?? 0),
                        'id_kelompok_ta' => $id,
                        'id_kategori_ta' => $idKategoriTa, // fix: sebelumnya tidak diisi (NULL)
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update Gagal! Error: ' . $e->getMessage());
        }
    }

    public function changePassword(Request $request , $id) 
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'new_password' => 'required|string|',
                'confirm_password' => 'required|string|', // Pastikan confirm_password sama dengan new_password
            ]);
            if ($request->new_password !== $request->confirm_password) {
                return redirect()->back()->with('error', 'Password dan konfirmasi password tidak cocok.');
            }
            // dd($validatedData);

            $user = User::findOrFail($id);
            // Perbarui data di tabel `users`
            $user->password = Hash::make($request->new_password); // Hash password baru
            $user->save();
    
            return redirect()->back()->with('success', 'Password berhasil diperbarui.');
        } catch (\Exception $e) {
            // Menangani kesalahan dan memberikan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui password.');
        } 
    }

    public function datauser()
    {
   
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $datauser = User::with('roles')->get();
            return view("main.viewdatauser", compact('datauser'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
       
    }

    public function edituser($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $datauser = User::where('id', $id)->first();
            // dd($datauser);
            return view("main.viewdetailuser", compact('datauser'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
       
    }

    public function updateuser(Request $request, $id)
    { 
        try 
        {

           
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'gelar_depan' => 'nullable|string|max:20',
                'gelar_belakang' => 'nullable|string|max:20',
                'nik' => 'nullable|string|max:20',
                'nim' => 'nullable|string|max:20',
                'nidn' => 'nullable|string|max:20',
            ]);
            //  dd($validatedData);

                $user = User::findOrFail($id);

                // Perbarui data di tabel `users`
                $user->name = $validatedData['first_name'];
                $user->email = $validatedData['email'];
                $user->save();

                // Cari data di tabel `user_details` berdasarkan `user_id`
                $userDetail = UserDetail::where('user_id', $id)->first();

                if ($userDetail) {
                   // Perbarui data jika ditemukan
                if ($user->hasRole('Mahasiswa')) {
                    $userDetail->nim = $validatedData['nim'];
                }

                if ($user->hasRole('Dosen')) {
                    $userDetail->nik = $validatedData['nik'];
                    $userDetail->nidn = $validatedData['nidn'];
                }

                $userDetail->gelar_depan = $validatedData['gelar_depan'];
                $userDetail->gelar_belakang = $validatedData['gelar_belakang'];
                $userDetail->save();
                } else {
                        // Jika tidak ditemukan, buat data baru
                    $userData = [
                        'user_id' => $id,
                        'gelar_depan' => $validatedData['gelar_depan'],
                        'gelar_belakang' => $validatedData['gelar_belakang'],
                    ];

                    if ($user->hasRole('Mahasiswa')) {
                        $userData['nim'] = $validatedData['nim'];
                    }

                    if ($user->hasRole('Dosen')) {
                        $userData['nik'] = $validatedData['nik'];
                        $userData['nidn'] = $validatedData['nidn'];
                    }

                    UserDetail::create($userData);
                }      
            return redirect()->back()->with('success', 'Data Berhasil Diperbaharui');
        }   
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }  

    }

    public function uploadphotoeditadmin(Request $request, $id)
    {
        $userId = $id; // Dapatkan user_id dari user yang terautentikasi

        if ($request->hasFile('profileImage')) {
            $file = $request->file('profileImage');

            // Validasi ekstensi file jika perlu
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, ['jpg', 'jpeg'])) {
                return redirect()->back()->with('error', 'File harus berformat JPG/JPEG.');
            }

            // Ambil data crop dari request
            $cropData = json_decode($request->cropData);

            // Baca file gambar
            $image = imagecreatefromjpeg($file->path());

            // Validasi jika gambar berhasil dibaca
            if (!$image) {
                return redirect()->back()->with('error', 'Gagal membaca gambar.');
            }

            // Memangkas gambar berdasarkan cropData
            $croppedImage = imagecrop($image, [
                'x' => (int)$cropData->x,
                'y' => (int)$cropData->y,
                'width' => (int)$cropData->width,
                'height' => (int)$cropData->height
            ]);

            // Hapus gambar sumber dari memori
            imagedestroy($image);

            if ($croppedImage !== false) {
                // Buat output base64 dari gambar yang dipangkas
                ob_start();
                imagejpeg($croppedImage, null, 80); // Simpan ke output dengan kualitas 80%
                $base64Image = base64_encode(ob_get_clean());

                // Simpan atau update base64 image di tabel user_details
                $userDetail = UserDetail::where('user_id', $userId)->first();
                if ($userDetail) {
                    // Jika sudah ada, update foto
                    $userDetail->photo = $base64Image;
                    $userDetail->save();
                } else {
                    // Jika belum ada, buat entri baru
                    UserDetail::create([
                        'user_id' => $userId,
                        'photo' => $base64Image
                    ]);
                }

                // Hapus gambar yang dipangkas dari memori
                imagedestroy($croppedImage);

                return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
            }

            return redirect()->back()->with('error', 'Gagal memotong gambar.');
        }

        return redirect()->back()->with('error', 'Tidak ada gambar yang diunggah.');
    }

    public function penempatanmagang()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            $penempatanmagang = PesertaMagang::pluck('id_mahasiswa')->toArray();

            $mahasiswa = User::whereHas('roles', function ($query) {
                $query->where('name', 'Mahasiswa');
            })->whereNotIn('id', $penempatanmagang)->get();

            

            $perusahaan = DataPerusahaanMagang::all();
            
           
            return view("main.formpenempatanmagang", compact('mahasiswa', 'perusahaan'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }  
    }

    // public function storepenempatanmagang(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'peserta' => 'required|array',
    //             'perusahaan' => 'required|array',
    //             'tanggal_presentasi' => 'required|array',
    //             'jam_presentasi' => 'required|array',
    //         ]);
        
    //         // Pastikan jumlah elemen dalam array cocok
    //         if (count($validatedData['peserta']) !== count($validatedData['perusahaan']) || 
    //             count($validatedData['peserta']) !== count($validatedData['tanggal_presentasi'])) {
    //             return redirect()->back()->with('error', 'Jumlah data peserta, perusahaan, dan tanggal presentasi tidak sesuai.');
    //         }
        
    //         // Cek apakah ada ID peserta yang duplikat
    //         if (count($validatedData['peserta']) !== count(array_unique($validatedData['peserta']))) {
    //             return redirect()->back()->with('error', 'Duplikasi ID Mahasiswa, Silahkan Ulangi!');
    //         }
        
    //         foreach ($validatedData['peserta'] as $key => $pesertaId) {
    //             // Cek data mahasiswa pada tabel
    //             $existingPeserta = PesertaMagang::where('id_mahasiswa', $pesertaId)->first();
        
    //             // Jika sudah terdaftar, kembalikan dengan error
    //             if ($existingPeserta) {
    //                 return redirect()->back()->with('error', "Mahasiswa dengan ID {$pesertaId} sudah terdaftar.");
    //             }
        
    //             // Pastikan indeks yang diakses ada di array lainnya
    //             if (!isset($validatedData['perusahaan'][$key]) || !isset($validatedData['tanggal_presentasi'][$key])) {
    //                 return redirect()->back()->with('error', 'Data tidak lengkap untuk peserta ID: ' . $pesertaId);
    //             }
        
    //             // Menyimpan pasangan peserta dan perusahaan
    //             PesertaMagang::create([
    //                 'id_mahasiswa' => (int)$pesertaId,
    //                 'id_perusahaan' => (int)$validatedData['perusahaan'][$key],
    //                 'tanggal_presentasi' => (string)$validatedData['tanggal_presentasi'][$key],
    //                 'jam_presentasi' => (string)$validatedData['jam_presentasi'][$key],
    //             ]);
    //         }
    //         return redirect()->route('viewpenempatanmagang')->with('success', 'Data Berhasil Ditambahkan');
           
    //     }
    //     catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Data Gagal Ditambahkan'  . $e->getMessage());
    //     }
    // }

    public function storedatapembimbing(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'dosen' => 'required|array',
                'statusdosen' => 'required|array',
                'mahasiswa' => 'required|integer',
            ]);

            // Guard: kalau mahasiswa ini sudah dinilai, tidak boleh ada perubahan pembimbing/penguji lagi.
            $sudahDinilai = PenilaianMagang::where('id_mahasiswa', $validatedData['mahasiswa'])->exists();
            if ($sudahDinilai) {
                return redirect()->back()->with('error', 'Tidak bisa menambah dosen, penilaian magang untuk mahasiswa ini sudah dilakukan.');
            }

            if (count($validatedData['dosen']) !== count(array_unique($validatedData['dosen']))) {
                return redirect()->back()->with('error', 'Duplikasi Nama Dosen, Silahkan Ulangi !');
            }

            foreach ($validatedData['dosen'] as $key => $dosenId) {

                $existingPeserta = DataPengujiMagang::where('id_dosen', $dosenId) ->where('id_mahasiswa', $request->mahasiswa)
                ->exists();
                if ($existingPeserta) {
                    return redirect()->back()->with('error', 'Data Dosen Sudah Terdaftar');
                }
                DataPengujiMagang::create([
                    'id_dosen' => (int)$dosenId,
                    'status_dosen' => (int)$validatedData['statusdosen'][$key],
                    'id_mahasiswa' => (int)$validatedData['mahasiswa'],
                ]);
            }
            return redirect()->back()->with('success', 'Data Berhasil Ditambahkan');
        
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan'  . $e->getMessage());
        }
    }
 
    // Penilaian ta------------------------------------------------
    public function storeaspekdatata(Request $request)
    {
        // dd($request);
        try {
            $validatedData = $request->validate([
                "id_kategori_ta" => 'required|integer',
                'aspek' => 'required|array',
                'desk' => 'required|array',
                'porsi' => 'required|array',
                'porsi.*' => 'numeric', // Validasi bahwa setiap elemen dalam array 'porsi' adalah angka
            ]);

            $id_kategori_ta = $validatedData['id_kategori_ta'];
            // Cari data PenilaianTA berdasarkan id_kategori_ta
            $penilaian = PenilaianTA::where('id_kategori_TA', $id_kategori_ta)->first();
            // Jika data penilaian TA sudah ada, kembalikan dengan pesan error
            if ($penilaian) {
                return redirect()->back()->with('error', 'Anda tidak bisa menambah data, karena sudah dilakukan penilaian');
            }

            // Persiapan data untuk dimasukkan ke database
            $data = [];
            foreach ($validatedData['aspek'] as $key => $aspekId) {
                $data[] = [
                    'aspek_penilaian' => $aspekId,
                    'porsi_penilaian' => (int) $validatedData['porsi'][$key], // Konversi ke integer
                    'deskripsi_penilaian' => $validatedData['desk'][$key],
                    'id_kategori_ta' => $validatedData['id_kategori_ta'], // Tidak pakai [$key]
                    'tipedata' => 'Input',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Simpan ke database
            AspekPenilaianTA::insert($data);
            return redirect()->back()->with('success', 'Data Berhasil Ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan: ' . $e->getMessage());
        }
    }
   
    public function deleteaspekta($id)
    {
        $item = AspekPenilaianTA::findOrFail($id);

        // Guard: item dengan tipedata "Deskripsi" tidak boleh dihapus sama sekali,
        // sesuai proteksi yang juga diterapkan di blade (item->tipedata != "Deskripsi")
        if ($item->tipedata == "Deskripsi") {
            return redirect()->back()->with('error', 'Data ini tidak bisa dihapus.');
        }

        $penilaian = PenilaianTA::where('id_kategori_TA', $item->id_kategori_ta)->first();
        if ($penilaian) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus data, karena sudah dilakukan penilaian');
        }

        $item->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function storeaspekdatataindividu(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'aspek' => 'required|array',
                'id_kategori_ta' => 'required|array',
                'desk' => 'required|array',
                'porsi' => 'required|array',
                'porsi.*' => 'numeric',
            ]);

            $kategoriIds = $validatedData['id_kategori_ta'];

            // Guard: cek tabel PenilaianTAindividu, di-scope ke kategori-kategori
            // yang ada di input (bukan PenilaianTA/global)
            $sudahDinilai = PenilaianTAindividu::whereHas('aspekpenilaianTAindividu', function ($q) use ($kategoriIds) {
                $q->whereIn('id_kategori_ta', $kategoriIds);
            })->exists();

            if ($sudahDinilai) {
                return redirect()->back()->with('error', 'Anda tidak bisa menambah data, karena sudah dilakukan penilaian untuk kategori ini');
            }

            $data = [];
            foreach ($validatedData['aspek'] as $key => $aspekId) {
                $data[] = [
                    'aspek_penilaian' => $aspekId,
                    'porsi_penilaian' => (int) $validatedData['porsi'][$key],
                    'deskripsi_penilaian' => $validatedData['desk'][$key],
                    'id_kategori_ta' => $validatedData['id_kategori_ta'][$key],
                    'tipedata' => 'Input',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            AspekPenilaianTAIndividu::insert($data);
            return redirect()->back()->with('success', 'Data Berhasil Ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan: ' . $e->getMessage());
        }
    }

    public function deleteaspektaindividu($id)
    {
        $item = AspekPenilaianTAIndividu::findOrFail($id);

        // Guard: item dengan tipedata "Deskripsi" tidak boleh dihapus sama sekali,
        // sesuai proteksi yang juga diterapkan di blade (item->tipedata != "Deskripsi")
        if ($item->tipedata == "Deskripsi") {
            return redirect()->back()->with('error', 'Data ini tidak bisa dihapus.');
        }

        // Guard: scope ke kategori milik item ini saja, bukan global
        $sudahDinilai = PenilaianTAindividu::whereHas('aspekpenilaianTAindividu', function ($q) use ($item) {
            $q->where('id_kategori_ta', $item->id_kategori_ta);
        })->exists();

        if ($sudahDinilai) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus data, karena sudah dilakukan penilaian untuk kategori ini');
        }

        $item->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
    // end ta------------------------------------------------

   
    public function storeaspekdata(Request $request)
    {
        
        try {
            $validatedData = $request->validate([
                'aspek' => 'required|array',
                'desk' => 'required|array',
                'porsi' => 'required|array',
                'porsi.*' => 'numeric', // Validasi bahwa setiap elemen dalam array 'porsi' adalah angka
            ]);
            $totalPorsi = DataAspekPenilaianMagang::sum('porsi_penilaian');
           
            // dd($totalPorsi);
            if ($totalPorsi >= 100) {
                return redirect()->back()->with('error', 'Total Porsi Penilaian sudah mencapai 100%, silahkan lakukan Reset Penilaian untuk input ulang');
            }

            $totalPorsiinput = array_sum($validatedData['porsi']);
            
           
            // dd($totalPorsi + $totalPorsiinput);
            // Periksa apakah total porsi sama dengan 100
            if ($totalPorsi + $totalPorsiinput !== 100) {
                return redirect()->back()->with('error', 'Total Porsi Penilaian harus 100%');
            }

            

            $penilaian = PenilaianMagang::first();  
            // Jika data penilaian magang ada, arahkan kembali dengan pesan error
            if ($penilaian) {
            return redirect()->back()->with('error', 'Anda tidak bisa menambah data, karena sudah dilakukan penilaian');
            }

            
            $data = [];
            foreach ($validatedData['aspek'] as $key => $aspekId) {
                $data[] = [
                    'aspek_penilaian' => $aspekId,
                    'porsi_penilaian' => (int) $validatedData['porsi'][$key],  // Mengonversi ke integer
                    'deskripsi_penilaian' => $validatedData['desk'][$key],
                    'tipedata' => 'Input',
                ];
            }
         

            DataAspekPenilaianMagang::insert($data);
            return redirect()->back()->with('success', 'Data Berhasil Ditambahkan');
           
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan'  . $e->getMessage());
        }
    }
    public function pesertaupdate(Request $request, $id)
    {
        try {
            $peserta = PesertaMagang::find($id);
            if (!$peserta) {
                return redirect()->back()->with('error', 'Data Tidak Ditemukan');
            }
            $perusahaanMagang = DataPerusahaanMagang::find($request->perusahaan_id);

            if (!$perusahaanMagang) {
                return redirect()->back()->with('error', 'Data Perusahaan Tidak Ditemukan');
            }
    
            $peserta->id_perusahaan = $request->perusahaan_id;
            $peserta->tanggal_presentasi = $request->tanggal_presentasi;
            $peserta->jam_presentasi = $request->jam_presentasi;
            $peserta->jam_presentasi_selesai = $request->jam_presentasi_selesai;
            $peserta->lokasi = $request->lokasi;
             $peserta->sk = $request->sk;
            $peserta->save();
    
            return redirect()->back()->with('success', 'Data Berhasil Diubah');

        }   
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data Tidak Ditemukan');
        }   
    }

    public function setupdatamagang($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            $dosen = User::whereHas('roles', function ($query) 
                { $query->where('name', 'Dosen');
                })->get();

            $pesertamagang = PesertaMagang::where('id', $id)->first();
            if (!$pesertamagang) {
                return redirect()->back()->with('error', 'Data peserta tidak ditemukan');
            }

            $statusdosen = StatusDosen::all();
            $pengujimagang = DataPengujiMagang::where('id_mahasiswa', $pesertamagang->id_mahasiswa)->get();

            // Guard: kalau penilaian magang untuk mahasiswa ini sudah dilakukan,
            // form tambah/edit pembimbing harus dikunci.
            $sudahDinilai = PenilaianMagang::where('id_mahasiswa', $pesertamagang->id_mahasiswa)->exists();

            return view("main.setupmagang", compact('dosen', 'pesertamagang','statusdosen','pengujimagang','sudahDinilai'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }  
    }

    public function deletedatamagang($id)
    {
        try {
            $penguji = DataPengujiMagang::findOrFail($id);

            $sudahDinilai = PenilaianMagang::where('id_mahasiswa', $penguji->id_mahasiswa)->exists();

            if ($sudahDinilai) {
                return redirect()->back()->with('error', 'Data pembimbing/penguji tidak bisa dihapus karena penilaian magang sudah dilakukan.');
            }

            $penguji->delete();

            return redirect()->back()->with('success', 'Data berhasil dihapus');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        }
    }

    public function aspekpenilaian()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            $dataaspek = DataAspekPenilaianMagang::all();
            $sudahDinilai = PenilaianMagang::exists();

            return view("main.aspekpenilaian", compact('dataaspek', 'sudahDinilai'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }  
    }



    public function deleteaspek($id)
    {
        $item = DataAspekPenilaianMagang::findOrFail($id);

        // Guard: item dengan id 1 tidak boleh dihapus sama sekali,
        // sesuai proteksi yang juga diterapkan di blade (item->id != 1)
        if ($item->id == 1) {
            return redirect()->back()->with('error', 'Data aspek ini tidak bisa dihapus.');
        }

        $penilaian = PenilaianMagang::first();
        // Jika data penilaian magang ada, arahkan kembali dengan pesan error
        if ($penilaian) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus data, karena sudah dilakukan penilaian');
        }

        // Menghapus data
        $item->delete();

        // Mengembalikan respons dengan pesan sukses
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

   

    public function resetPenilaian()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
           
            return view("main.viewresetpenilaianmagang");
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        } 

    }

    public function deletePenilaian()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
           PenilaianMagang::truncate();
           DataAspekPenilaianMagang::where('id', '!=', 1)->delete();
           
           return redirect()->back()->with('success', 'Data Berhasil Dihapus');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        } 

    }

    public function viewresetdatabase()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
           
            return view("main.viewresetdata");
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        } 
    }

    public function resetDatabase()
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Profil Tidak Ditemukan');
        }

        DB::beginTransaction();
        try {
            // Cari User yang BUKAN Admin/Dosen (otomatis = Mahasiswa).
            // User Admin & Dosen TIDAK disentuh sama sekali di bawah ini.
            $userIdsToDelete = User::whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Dosen']);
            })->pluck('id');

            // ---------- MODUL MAGANG ----------
            PenilaianMagang::query()->delete();
            DataPengujiMagang::query()->delete();
            PesertaMagang::query()->delete();
            DataAspekPenilaianMagang::query()->delete();
            DataPerusahaanMagang::query()->delete();

            // ---------- MODUL TUGAS AKHIR / TA ----------
            PenilaianTA::query()->delete();
            PenilaianTAindividu::query()->delete();
            DataPengujiTa::query()->delete();
            VerifikasiKelompokTA::query()->delete();
            JadwalTA::query()->delete();
            PesertaTA::query()->delete();
            KelompokTA::query()->delete();
            AspekPenilaianTA::query()->delete();
            AspekPenilaianTAIndividu::query()->delete();
            // KategoriTA SENGAJA TIDAK dihapus: belum ada form Admin untuk
            // input ulang KategoriTA, dan urutan id-nya dipakai sebagai
            // acuan tahapan sidang (lihat AssessmentController::lanjutPenilaian,
            // $idkatTA = id_kategori_ta + 1). Menghapus & menginput ulang
            // manual di DB berisiko mengacaukan urutan tahapan.

            // ---------- MASTER DATA PENUNJANG ----------
            // StatusDosen SENGAJA TIDAK dihapus: belum ada form Admin untuk
            // input ulang StatusDosen, dan datanya dipakai lintas tahun ajaran
            // (Pembimbing/Penguji/Ketua Penguji/Anggota Penguji tidak berubah).

            // ---------- HAPUS USER SELAIN ADMIN & DOSEN ----------
            DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->whereIn('model_id', $userIdsToDelete)
                ->delete();

            User::whereIn('id', $userIdsToDelete)->delete(); // user_details ikut kehapus (cascade)

            DB::commit();

            return back()->with('success', 'Semua data berhasil direset. Data Admin, Dosen, Kategori TA, dan Status Dosen tetap tersimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function createKelompokTA()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            // Selama masih ada penilaian TA (kelompok maupun individu) yang tersimpan,
            // form generate kelompok TA baru harus dikunci. Admin wajib reset data
            // penilaian tahun ajaran sebelumnya dahulu sebelum bisa membuat kelompok baru.
            $sudahAdaPenilaian = PenilaianTA::exists() || PenilaianTAindividu::exists();

            return view('main.createkelompokta', compact('sudahAdaPenilaian'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
    }


    public function storeKelompokTA(Request $request)
    {
        $sudahAdaPenilaian = PenilaianTA::exists() || PenilaianTAindividu::exists();

        if ($sudahAdaPenilaian) {
            return redirect()->back()->with('error', 'Tidak bisa membuat kelompok TA baru, karena sudah ada penilaian yang dilakukan.');
        }

        $validated = $request->validate([
            'nama_kelompok' => 'required|array|min:1',
            'nama_kelompok.*' => 'required|string|max:255',
            'tahun_perkuliahan' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $kategoriList = KategoriTA::pluck('id');

            foreach ($validated['nama_kelompok'] as $nama) {
                $kelompokTA = KelompokTA::create([
                    'nama_kelompok' => $nama,
                    'judul_ta' => null,
                    'sk' => null,
                    'tahun_perkuliahan' => $validated['tahun_perkuliahan'],
                ]);

                foreach ($kategoriList as $kategoriId) {
                    JadwalTA::create([
                        'id_kelompok_ta' => $kelompokTA->id,
                        'id_kategori_ta' => $kategoriId,
                        'tanggal_presentasi' => null,
                        'jam_presentasi' => null,
                        'jam_presentasi_selesai' => null,
                        'lokasi' => null,
                    ]);

                    VerifikasiKelompokTA::create([
                        'id_kelompok_ta' => $kelompokTA->id,
                        'id_kategori_ta' => $kategoriId,
                        'status' => '0',
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Kelompok TA berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan kelompok TA: ' . $e->getMessage());
        }
    }
}
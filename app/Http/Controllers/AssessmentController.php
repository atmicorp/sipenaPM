<?php

namespace App\Http\Controllers;

use App\Models\AspekPenilaianTA;
use App\Models\AspekPenilaianTAIndividu;
use App\Models\DataAspekPenilaianMagang;
use App\Models\DataPengujiMagang;
use App\Models\DataPengujiTa;
use App\Models\JadwalTA;
use App\Models\KategoriTA;
use App\Models\PenilaianMagang;
use App\Models\PenilaianTA;
use App\Models\PenilaianTAindividu;
use App\Models\PesertaTA;
use App\Models\User;
use App\Models\VerifikasiKelompokTA;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Log;

class AssessmentController extends Controller
{
    public function penilaianmagang()
    {
        try {
            $user =Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
           // Ambil data penguji magang berdasarkan dosen yang sedang login
            $pesertamagang = DataPengujiMagang::where('id_dosen', Auth::user()->id)->get();
            // dd($pesertamagang);

            // Ambil semua id_dosen dari $pesertamagang
            $datapenguji = $pesertamagang->pluck('id');
            //  dd($datapenguji);
           
            // Cari data penilaian magang yang sesuai dengan $datapenguji
            $datapenilaian = PenilaianMagang::whereIn('id_data_penguji_magangs', $datapenguji)->get(); 
            // foreach ($datapenilaian as $penilaian) {
            //     dd($penilaian->id_data_penguji_magangs); 
            // }
            // dd($datapenilaian);


            $datanilai = PenilaianMagang::whereIn('id_data_penguji_magangs', $datapenguji)
            ->with('aspekpenilaianmagang:id,aspek_penilaian,tipedata') // Eager loading relasi untuk mengakses nama aspek
            ->get(['id_aspek_penilaian', 'nilai','id_mahasiswa']);
            // dd($datanilai);

            $datanilai = $datanilai->map(function ($item) {
                return [
                    'id_mahasiswa' => $item->id_mahasiswa,
                    'aspek_penilaian' => $item->aspekpenilaianmagang->aspek_penilaian ?? 'Tidak Ditemukan',
                    'nilai' => $item->nilai,
                    'tipedata' => $item->aspekpenilaianmagang->tipedata ?? 'Tidak Ditemukan',
                ];
            })->groupBy('id_mahasiswa');

        //    dd($datanilai);
                        
          
            return view("main.penilaianmagang", compact('user','pesertamagang','datapenilaian','datanilai'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Profile tidak ditemukan');
        }
       
    }

    public function penilaianTA($id, Request $request)
    {
        // $idKategoriTA = $request->query('id_kategori_ta');
        // dd($id, $idKategoriTA);

        try {
            $user =Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
           // Ambil data penguji magang berdasarkan dosen yang sedang login
            $pengujiTA = DataPengujiTa::with(['statusdosenTA','KelompokTA'])->where('id_dosen', Auth::user()->id)->get();
            $idKelompokTA = $pengujiTA->pluck('id_kelompok_ta');

            // Ambil data Peserta TA dari data peserta berdasarkan kelompok TA data penguji
            $pesertaTA = PesertaTA::with('usermahasiswaTA')
                ->whereIn('id_kelompok_ta', $idKelompokTA)
                ->get();
                // dd($pesertaTA);

            $kategoriTA = KategoriTA::where('id', $id)->first();
            // dd($kategoriTA);
           
            // // Ambil semua id_dosen dari $pesertamagang
            // $datapenguji = $pengujiTA->pluck('id');
            //  dd($pengujiTA);
                                   
            return view("main.penilaianTA", compact('user','pesertaTA','pengujiTA','kategoriTA'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Profile tidak ditemukan');
        }
       
    }

    public function tolakPenilaian(Request $request)
    {
        // dd($request);
        try {
            $datamahasiswa = PesertaTA::where('id_kelompok_ta', $request->id_kelompok_ta)
            ->pluck('id_mahasiswa');
            // dd($datamahasiswa);

            PenilaianTAindividu::whereIn('id_mahasiswa', $datamahasiswa)
            ->where('id_kategori_ta', $request->id_kategori_ta)
            ->delete();

            PenilaianTA::where('id_kelompok_ta', $request->id_kelompok_ta)
            ->where('id_kategori_ta', $request->id_kategori_ta)
            ->delete();

            JadwalTA::where('id_kelompok_ta', $request->id_kelompok_ta)
            ->where('id_kategori_ta', $request->id_kategori_ta)
            ->update([
                'tanggal_presentasi' => null,
                'jam_presentasi' => null,
                'jam_presentasi_selesai' => null,
                'lokasi' => null,
            ]);

            
            VerifikasiKelompokTA::where('id_kelompok_ta', $request->id_kelompok_ta)
            ->where('id_kategori_ta',  $request->id_kategori_ta)
            ->update(['status'=>'3']);

            return redirect()->back()->with('success', 'Penilaian berhasil ditolak dan data terkait telah dihapus.');
        }
          
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
    }

    public function lanjutPenilaian(Request $request)
    {
        // dd($request);
      
        try {
            $idkelTA = $request->id_kelompok_ta;
            $idkatTA = $request->id_kategori_ta + 1;
            $idkatTAnow = $request->id_kategori_ta;

           $cek = VerifikasiKelompokTA::where('id_kelompok_ta', $idkelTA)->get();
           $lastIdKategori = $cek->last()->id_kategori_ta;

            if ($request->id_kategori_ta == $lastIdKategori) {
                VerifikasiKelompokTA::where('id_kelompok_ta', $idkelTA)
                    ->where('id_kategori_ta', $request->id_kategori_ta)
                    ->update(['status' => '4']);

                return redirect()->back()->with('success', 'Silahkan Lakukan Penilaian');
            } else {
                VerifikasiKelompokTA::where('id_kelompok_ta', $idkelTA)
                    ->where('id_kategori_ta', $idkatTA)
                    ->update(['status' => '1']);
                
                    VerifikasiKelompokTA::where('id_kelompok_ta', $idkelTA)
                    ->where('id_kategori_ta', $idkatTAnow)
                    ->update(['status' => '2']);
            
                return redirect()->back()->with('success', 'Silahkan Lakukan Penilaian');
            }

            return redirect()->back()->with('success', 'Silahkan Lakukan Penilaian');
        }
          
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
    }

    public function formpenilaianTA(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            $idKategoriTA = $request->query('id_kategori_ta');

            $datapenguji = DataPengujiTa::with(['statusdosenTA', 'KelompokTA', 'userdosenTA'])
                ->where('id', $id)
                ->first();

            if (!$datapenguji) {
                return redirect()->back()->with('error', 'Data penguji tidak ditemukan');
            }

            // ==========================================
            // CEK OTORISASI: admin bisa lihat semua data,
            // dosen biasa hanya bisa lihat datanya sendiri
            // ==========================================
            $isAdmin = $user->role === 'Admin'; // sesuaikan dengan cara cek role di project kamu
            // contoh alternatif kalau pakai Spatie: $isAdmin = $user->hasRole('admin');

            if (!$isAdmin && $datapenguji->id_dosen != $user->id) {
                abort(403, 'Anda tidak memiliki akses ke data penilaian ini');
            }

            $validasiTA = VerifikasiKelompokTA::where('id_kelompok_ta', $datapenguji->id_kelompok_ta)
                ->select('id_kelompok_ta', 'id_kategori_ta', 'status')
                ->get();

            $nextkategori = $idKategoriTA + 1;
            $statusnext = VerifikasiKelompokTA::where('id_kelompok_ta', $datapenguji->id_kelompok_ta)
                ->where('id_kategori_ta', $nextkategori)
                ->select('id_kelompok_ta', 'id_kategori_ta', 'status')
                ->first();

            $statusnow = VerifikasiKelompokTA::where('id_kelompok_ta', $datapenguji->id_kelompok_ta)
                ->where('id_kategori_ta', $idKategoriTA)
                ->select('id_kelompok_ta', 'id_kategori_ta', 'status')
                ->first();

            $kategoriTA = KategoriTA::where('id', $idKategoriTA)->first();

            $aspekpenilaian = AspekPenilaianTA::where('id_kategori_ta', $idKategoriTA)->get();
            [$deskripsiItems, $nonDeskripsiItems] = $aspekpenilaian->partition(function ($item) {
                return $item->tipedata === 'Deskripsi';
            });
            $aspekpenilaian = $nonDeskripsiItems->concat($deskripsiItems);

            $id_kelompok_ta = $datapenguji->id_kelompok_ta;
            $pesertaTA = PesertaTA::with('usermahasiswaTA')->where('id_kelompok_ta', $id_kelompok_ta)->get();

            $aspekpenilaianindividu = AspekPenilaianTAIndividu::where('id_kategori_ta', $idKategoriTA)->get();
            [$deskripsiItems, $nonDeskripsiItems] = $aspekpenilaianindividu->partition(function ($item) {
                return $item->tipedata === 'Deskripsi';
            });
            $aspekpenilaianindividu = $nonDeskripsiItems->concat($deskripsiItems);

            $hasilpenilaianTA = PenilaianTA::where('id_data_pengujiTA', $datapenguji->id)
                ->where('id_kategori_TA', $idKategoriTA)
                ->get();

            $hasilpenilaianTAindividu = PenilaianTAindividu::with('pengujiTAindividu')
                ->where('id_data_pengujiTA', $datapenguji->id)
                ->where('id_kategori_TA', $idKategoriTA)
                ->get();

            $nilaiIndividu = [];
            foreach ($hasilpenilaianTAindividu as $penilaian) {
                $userMahasiswa = User::where('id', $penilaian->id_mahasiswa)->first();
                $aspekPenilaian = AspekPenilaianTAIndividu::where('id', $penilaian->id_aspekTA_individu)->first();
                $idMahasiswa = $penilaian->id_mahasiswa;

                if (!isset($nilaiIndividu[$idMahasiswa])) {
                    $nilaiIndividu[$idMahasiswa] = [
                        "id_mahasiswa" => $idMahasiswa,
                        "Nama" => $userMahasiswa ? $userMahasiswa->name : "Tidak Ditemukan",
                        "NIM" => $userMahasiswa ? ($userMahasiswa->details ? $userMahasiswa->details->nim : "Tidak Ditemukan") : "Tidak Ditemukan",
                        "photo" => $userMahasiswa ? ($userMahasiswa->details ? $userMahasiswa->details->photo : "Tidak Ditemukan") : "Tidak Ditemukan",
                        "Penilaian" => []
                    ];
                }
                $nilaiIndividu[$idMahasiswa]['Penilaian'][] = [
                    "Aspek_penilaian" => $aspekPenilaian ? $aspekPenilaian->aspek_penilaian : "Tidak Ditemukan",
                    "Nilai" => $penilaian->nilai
                ];
            }
            $nilaiIndividu = array_values($nilaiIndividu);

            return view("main.formpenilaianTA", compact(
                'datapenguji', 'aspekpenilaian', 'kategoriTA', 'aspekpenilaianindividu',
                'pesertaTA', 'hasilpenilaianTA', 'nilaiIndividu', 'statusnext', 'statusnow'
            ));
        } catch (\Throwable $e) {
            Log::error('formpenilaianTA error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
    }
    public function hasilpenilaianmagang()
    {
        // jika data penilaian kosong maka return back
        $cekdatapenilaian = PenilaianMagang::all(); // Atau query lain sesuai kebutuhan Anda

        if ($cekdatapenilaian->isEmpty()) { // Periksa jika koleksi kosong
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        try {
            // 1. ambil data penguji magang, dan kelompokkan berdasarkan mahasiswa
            $datapengujimagang = DataPengujiMagang::get(['id','id_mahasiswa', 'id_dosen']); // Gunakan eager loading untuk menghindari N+1 query
            $datapengujimagang = $datapengujimagang->map(function ($item) {
                return [
                    'id_penguji' => $item->id,
                    'id_mahasiswa' => $item->id_mahasiswa,
                    'mahasiswa'    => $item->usermahasiswa->name, // hanya nama
                    'nim'          => $item->usermahasiswa->details->nim, // hanya nim
                    'dosen' =>$item->userdosen->details->gelar_depan. ' ' .$item->userdosen->name.','.' '. $item->userdosen->details->gelar_belakang, 
                ];
            })->groupBy('id_mahasiswa'); // Mengelompokkan berdasarkan 'mahasiswa'
       

            // 2. ambil data aspek penilaian
            $aspekpenilaianmagang  = DataAspekPenilaianMagang::get(['id','aspek_penilaian','porsi_penilaian']);
            $aspekpenilaianmagang = $aspekpenilaianmagang->map(function ($item) {
                return [
                    'id_aspek_penilaian' => (string) $item->id, // Konversi ID menjadi string
                    'aspek_penilaian' => (string) $item->aspek_penilaian, // Konversi ID menjadi string
                    'porsi_penilaian' => (integer) $item->porsi_penilaian,

                ];
            });
         

            // 3. ambil data penilaian
            $datanilaimagang = PenilaianMagang::get(['id_data_penguji_magangs','id_aspek_penilaian', 'nilai']);
            $datanilaimagang = $datanilaimagang->map(function ($item) {
                return [
                    'id_penguji'=> $item->id_data_penguji_magangs,
                    'id_aspek_penilaian'=> $item->id_aspek_penilaian,
                    'nilai' => $item->nilai,
                ];
            })->groupBy('id_penguji');
          
  
            // 4. Gabungkan data penguji magang dengan aspek penilaian dan isi nilai dengan data penilaian
            $datapengujimagang = $datapengujimagang->map(function ($items) use ($aspekpenilaianmagang, $datanilaimagang) {
                return $items->map(function ($item) use ($aspekpenilaianmagang, $datanilaimagang) {
                    // dd($item);
                    $penilaian = []; 
                    // Aspek penilaian dengan ID sebagai kunci dan nama aspek sebagai nilai
                    foreach ($aspekpenilaianmagang as $aspect) {
                        // Menambahkan nama aspek penilaian dengan nilai default null
                        $penilaian[$aspect['aspek_penilaian']] = null;
                    }
                    // Pastikan id_penguji ada dalam $datanilaimagang dan data penilaian ada
                    if (isset($datanilaimagang[$item['id_penguji']])) {
                        foreach ($datanilaimagang[$item['id_penguji']] as $nilai) {
                            // Gantilah kunci dengan nama aspek penilaian
                            foreach ($aspekpenilaianmagang as $aspect) {
                                if ($nilai['id_aspek_penilaian'] == $aspect['id_aspek_penilaian']) {
                                    
                                    // Cek apakah nilai adalah angka atau huruf
                                    if (is_numeric($nilai['nilai'])) {
                                        // Jika angka, konversi menjadi integer
                                        $penilaian[$aspect['aspek_penilaian']] = (int)$nilai['nilai'];
                                    } else {
                                        // Jika huruf, tampilkan 5 karakter pertama
                                        $penilaian[$aspect['aspek_penilaian']] = $nilai['nilai'];
                                    }
                                }
                            }
                        }
                    }
                    // Gabungkan menggunakan operator `+` untuk menjaga kunci string
                    return Arr::except(array_merge($item, $penilaian), ['Catatan Revisi']);
                    // jika cataten revisi ingin muncul aktifkan kode di bawah, dan disable kode di atas
                    // return array_merge($item, $penilaian);
                    
                });
            });
            // dd($datapengujimagang);
            

            // --------------------------------Hitung nilai Rata2 berdasarkan proporsi nilai--------------------------------

            // 1 Duplikasi $datapengujimagang dengan menghilangkan string
            $aspekpenilaianmagangModified = $aspekpenilaianmagang->map(function ($item) {
                return [
                    'id_aspek_penilaian' => $item['id_aspek_penilaian'],
                    'aspek_penilaian' => $item['aspek_penilaian'],
                    'porsi_penilaian' => (int) $item['porsi_penilaian'], // Mengonversi 'porsi_penilaian' menjadi integer
                ];
            });
            // dd($aspekpenilaianmagangModified);
            
            // 2 Duplikasi $datapengujimagang kemudian di rata-rata
            $datapengujimagangModified = $datapengujimagang->map(function ($items) use ($aspekpenilaianmagangModified, $datanilaimagang) {
                // Mengelompokkan data berdasarkan id_mahasiswa
                $groupedByMahasiswa = $items->groupBy('id_mahasiswa');
            
                return $groupedByMahasiswa->map(function ($mahasiswaItems) use ($aspekpenilaianmagangModified, $datanilaimagang) {
                    $totalPenilaian = [];
                    $totalCount = count($mahasiswaItems);
            
                    // Inisialisasi nilai default untuk setiap aspek penilaian
                    foreach ($aspekpenilaianmagangModified as $aspect) {
                        $totalPenilaian[$aspect['aspek_penilaian']] = 0;
                    }
            
                    // Menghitung total nilai untuk setiap aspek berdasarkan penguji
                    foreach ($mahasiswaItems as $item) {
                        $penilaian = [];
                        // Memastikan id_penguji ada dalam $datanilaimagang dan data penilaian ada
                        if (isset($datanilaimagang[$item['id_penguji']])) {
                            foreach ($datanilaimagang[$item['id_penguji']] as $nilai) {
                                foreach ($aspekpenilaianmagangModified as $aspect) {
                                    if ($nilai['id_aspek_penilaian'] == $aspect['id_aspek_penilaian']) {
                                        // Cek apakah nilai adalah angka atau huruf
                                        if (is_numeric($nilai['nilai'])) {
                                            $penilaian[$aspect['aspek_penilaian']] = (int)$nilai['nilai'];
                                        } else {
                                            $penilaian[$aspect['aspek_penilaian']] = 0;
                                        }
                                    }
                                }
                            }
                        }
            
                        // Menambahkan nilai dari penguji saat ini ke total penilaian
                        foreach ($penilaian as $aspectName => $value) {
                            $totalPenilaian[$aspectName] += $value;
                        }
                    }
            
                    // Menghitung rata-rata untuk setiap aspek
                    $averagePenilaian = [];
                    foreach ($totalPenilaian as $aspectName => $totalValue) {
                        $averagePenilaian[$aspectName] = $totalValue / $totalCount;
                    }



            
                    // Mengalikan nilai rata-rata dengan porsi penilaian
                    $finalPenilaian = [];
                    $totalNilai = 0; // Menyimpan total nilai
                    foreach ($averagePenilaian as $aspectName => $averageValue) {
                        // Cari porsi penilaian yang sesuai
                        $porsi = $aspekpenilaianmagangModified->firstWhere('aspek_penilaian', $aspectName)['porsi_penilaian'];
                        // Hitung nilai akhir
                        $finalPenilaian[$aspectName] = $averageValue;

                         // Hitung nilai akhir
                        //  $finalPenilaian[$aspectName] = $averageValue * ($porsi / 100);
                        // Jumlahkan nilai akhir untuk mendapatkan total nilai
                        $totalNilai += $averageValue * ($porsi / 100);
                    }
            
                    // Tentukan nilai indeks berdasarkan total nilai
                    if ($totalNilai >= 78.1) {
                        $indeks = 'A';
                    } elseif ($totalNilai >= 74.6) {
                        $indeks = 'B+';
                    } elseif ($totalNilai >= 64.1) {
                        $indeks = 'B';
                    } elseif ($totalNilai >= 59.6) {
                        $indeks = 'C+';
                    } elseif ($totalNilai >= 55.1) {
                        $indeks = 'C';
                    } elseif ($totalNilai >= 36.1) {
                        $indeks = 'D';
                    } else {
                        $indeks = 'E';
                    }

                    
                    // dd($averageValue);
            
                    // Gabungkan hasil nilai akhir, total nilai, dan indeks dengan data mahasiswa
                    return array_merge($mahasiswaItems->first(), $finalPenilaian, ['total_nilai' => $totalNilai, 'nilai_indeks' => $indeks]);
                });
            });
            
            // dd($datapengujimagangModified);
           
            return view("main.datapenilaianmagang", compact('datapengujimagang','datapengujimagangModified'));

        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
       

    }

    public function hasilpenilaianta($id)
    {
            try {
             // Penilaian Kelompok ----------------------------------------------------------------------
            // 1. ambil data penguji magang, dan kelompokkan berdasarkan Kelompok TA
            $datapengujiTA = DataPengujiTa::get(['id', 'id_dosen', 'id_kelompok_ta'])
            ->groupBy('id_kelompok_ta')
            ->map(function ($items) { // $items adalah collection dari kelompok TA yang sama
                return $items->map(function ($item) { // Iterasi tiap objek dalam kelompok
                    return [
                        'id_penguji' => $item->id,
                        'id_kelompok_ta' => $item->id_kelompok_ta,
                        'kelompok_ta' => $item->KelompokTA->nama_kelompok,
                        'dosen' => $item->userdosenTA->details->gelar_depan . ' ' . 
                                $item->userdosenTA->name . ', ' . 
                                $item->userdosenTA->details->gelar_belakang, 
                    ];
                });
            });
            // dd($datapengujiTA);
            // 2. ambil data aspek penilaian TA berdasarkan kategorinya pada $id
            //  kelompok
            $aspekpenilaianTA = AspekPenilaianTA::where('id_kategori_ta', $id)
            ->get(['id', 'id_kategori_ta', 'aspek_penilaian', 'porsi_penilaian'])
            ->map(function ($item) {
                return [
                    'id_aspek_penilaian' => (string) $item->id, // Konversi ID menjadi string
                    'id_kategori_ta' => (string) $item->id_kategori_ta, // Konversi ID menjadi string
                    'aspek_penilaian' => (string) $item->aspek_penilaian, // Konversi ID menjadi string
                    'porsi_penilaian' => (integer) $item->porsi_penilaian,
                ];
            });
            // dd($aspekpenilaianTA);
            // 3. ambil data penilaian
            $datanilaiTA = PenilaianTA::get(['id_data_pengujiTA','id_aspekTA', 'nilai'])
                ->map(function ($item) {
                return [
                    'id_penguji'=> $item->id_data_pengujiTA,
                    'id_aspek_penilaian'=> $item->id_aspekTA,
                    'nilai' => $item->nilai,
                ];
            })->groupBy('id_penguji');
            //   dd($datanilaiTA );
            // 4. Gabungkan data penguji magang dengan aspek penilaian dan isi nilai dengan data penilaian
            $datapengujiTA =  $datapengujiTA->map(function ($items) use ($aspekpenilaianTA, $datanilaiTA) {
                return $items->map(function ($item) use ($aspekpenilaianTA, $datanilaiTA) {
                    // dd($item);
                    $penilaian = []; 
                    // Aspek penilaian dengan ID sebagai kunci dan nama aspek sebagai nilai
                    foreach ($aspekpenilaianTA as $aspect) {
                        // Menambahkan nama aspek penilaian dengan nilai default null
                        $penilaian[$aspect['aspek_penilaian']] = null;
                    }
                    // Pastikan id_penguji ada dalam $datanilaiTA dan data penilaian ada
                    if (isset($datanilaiTA[$item['id_penguji']])) {
                        foreach ($datanilaiTA[$item['id_penguji']] as $nilai) {
                            // Gantilah kunci dengan nama aspek penilaian
                            foreach ($aspekpenilaianTA as $aspect) {
                                if ($nilai['id_aspek_penilaian'] == $aspect['id_aspek_penilaian']) {
                                    
                                    // Cek apakah nilai adalah angka atau huruf
                                    if (is_numeric($nilai['nilai'])) {
                                        // Jika angka, konversi menjadi integer
                                        $penilaian[$aspect['aspek_penilaian']] = (int)$nilai['nilai'];
                                    } else {
                                        // Jika huruf, tampilkan 5 karakter pertama
                                        $penilaian[$aspect['aspek_penilaian']] = $nilai['nilai'];
                                    }
                                }
                            }
                        }
                    }
                    // Gabungkan menggunakan operator `+` untuk menjaga kunci string
                    return array_merge($item, $penilaian);
                });
                });
                // dd($datapengujiTA);
                    // 1 Duplikasi $datapengujimagang dengan menghilangkan string
                    $aspekpenilaianTAModified =  $aspekpenilaianTA->map(function ($item) {
                        return [
                            'id_aspek_penilaian' => $item['id_aspek_penilaian'],
                            'aspek_penilaian' => $item['aspek_penilaian'],
                            'porsi_penilaian' => (int) $item['porsi_penilaian'], // Mengonversi 'porsi_penilaian' menjadi integer
                        ];
                    });
                    // dd($aspekpenilaianTAModified);

                    // 2 Duplikasi $datapengujimagang kemudian di rata-rata
                    $datapengujiTAModified = $datapengujiTA->map(function ($items) use ($aspekpenilaianTAModified, $datanilaiTA) {
                        // Mengelompokkan data berdasarkan id_kelompok_ta
                        $groupedByMahasiswa = $items->groupBy('id_kelompok_ta');
                    
                        return $groupedByMahasiswa->map(function ($keltaitems) use ($aspekpenilaianTAModified, $datanilaiTA) {
                            $totalPenilaian = [];
                            $totalCount = count($keltaitems);
                    
                            // Inisialisasi nilai default untuk setiap aspek penilaian
                            foreach ($aspekpenilaianTAModified as $aspect) {
                                $totalPenilaian[$aspect['aspek_penilaian']] = 0;
                            }
                    
                            // Menghitung total nilai untuk setiap aspek berdasarkan penguji
                            foreach ($keltaitems as $item) {
                                $penilaian = [];
                                // Memastikan id_penguji ada dalam $datanilaiTA dan data penilaian ada
                                if (isset($datanilaiTA[$item['id_penguji']])) {
                                    foreach ($datanilaiTA[$item['id_penguji']] as $nilai) {
                                        foreach ($aspekpenilaianTAModified as $aspect) {
                                            if ($nilai['id_aspek_penilaian'] == $aspect['id_aspek_penilaian']) {
                                                // Cek apakah nilai adalah angka atau huruf
                                                if (is_numeric($nilai['nilai'])) {
                                                    $penilaian[$aspect['aspek_penilaian']] = (int)$nilai['nilai'];
                                                } else {
                                                    $penilaian[$aspect['aspek_penilaian']] = 0;
                                                }
                                            }
                                        }
                                    }
                                }
                    
                                // Menambahkan nilai dari penguji saat ini ke total penilaian
                                foreach ($penilaian as $aspectName => $value) {
                                    $totalPenilaian[$aspectName] += $value;
                                }
                            }
                    
                            // Menghitung rata-rata untuk setiap aspek
                            $averagePenilaian = [];
                            foreach ($totalPenilaian as $aspectName => $totalValue) {
                                $averagePenilaian[$aspectName] = $totalValue / $totalCount;
                            }
                    
                        // Mengalikan nilai rata-rata dengan porsi penilaian
                        $finalPenilaian = [];
                        $totalNilai = 0; // Menyimpan total nilai
                        foreach ($averagePenilaian as $aspectName => $averageValue) {
                            // Cari porsi penilaian yang sesuai
                            $porsi = $aspekpenilaianTAModified->firstWhere('aspek_penilaian', $aspectName)['porsi_penilaian'];
                            // Hitung nilai akhir
                            $finalPenilaian[$aspectName] = $averageValue;
                            // Hitung nilai akhir
                            // $finalPenilaian[$aspectName] = $averageValue * ($porsi / 100);

                            // Jumlahkan nilai akhir untuk mendapatkan total nilai
                            $totalNilai += $averageValue * ($porsi / 100);
                        }
                
            
                        // dd($averageValue);
                
                        // Gabungkan hasil nilai akhir, total nilai, dan indeks dengan data mahasiswa
                        return array_merge($keltaitems->first(), $finalPenilaian, ['total_nilai_kelompok' => $totalNilai]);
                        });
                    });
                    
                    // Menambahkan dd() untuk melihat hasil akhirnya
                    // dd($datapengujiTAModified);

            

            // Penilaian Individu
           // 1. Ambil data nilai individu dan kelompokkan berdasarkan id_mahasiswa
            $penilaianindividu = PenilaianTAindividu::where('id_kategori_TA', $id)->get()
            ->map(function ($item) {
                return [
                    "id_mahasiswa" => (int) $item->id_mahasiswa,
                    "id_kategori_TA" => (int) $item->id_kategori_TA,
                    "id_dosen" => (int) $item->id_dosen,
                    "id_aspekTA_individu" => (int) $item->id_aspekTA_individu,
                    "nilai" => (int) $item->nilai,
                ];
            })
            ->groupBy('id_mahasiswa');
            // 2. Ambil daftar aspek penilaian (Gunakan ID untuk pencocokan)
            $aspekpenilaianindividu = AspekPenilaianTAIndividu::where('id_kategori_TA', $id)->get(['id', 'aspek_penilaian',])
            ->mapWithKeys(function ($item) {
                return [(int) $item->id => $item->aspek_penilaian]; 
            })
            ->toArray();
            // 3. Ambil data peserta TA dan kelompokkan berdasarkan kelompok TA
            $pesertaTA = PesertaTA::get(['id_mahasiswa', 'id_kelompok_ta'])
            ->groupBy('id_kelompok_ta');
            // 4. Ambil data penguji TA dan kelompokkan berdasarkan kelompok TA
            $datapengujiTAindividu = DataPengujiTa::get(['id', 'id_dosen', 'id_kelompok_ta'])
            ->groupBy('id_kelompok_ta')
            ->map(function ($items) use ($aspekpenilaianindividu) {
                return $items->map(function ($item) use ($aspekpenilaianindividu) {
                    return [
                        'id_penguji' => (int) $item->id,
                        'id_kelompok_ta' => (int) $item->id_kelompok_ta,
                        'id_dosen' => (int) $item->userdosenTA->id,
                        'nama_dosen' => (string) $item->userdosenTA->name,
                    ] + array_fill_keys(array_values($aspekpenilaianindividu), null); // Ubah ID ke aspek_penilaian dengan nilai NULL
                });
            });
            // 5. Buat struktur data penguji berdasarkan mahasiswa dengan nilai yang sesuai
            $datapengujiByMahasiswa = collect();
            foreach ($pesertaTA as $id_kelompok_ta => $mahasiswaCollection) {
            if (isset($datapengujiTAindividu[$id_kelompok_ta])) {
                foreach ($mahasiswaCollection as $mahasiswa) {
                    $idMahasiswa = (int) $mahasiswa->id_mahasiswa;
                    $namaMahasiswa = optional($mahasiswa->usermahasiswaTA)->name ?? "Tidak Diketahui"; 
                    $nim = optional(optional($mahasiswa->usermahasiswaTA)->details)->nim ?? "Tidak Diketahui";

                    $datapengujiByMahasiswa[$idMahasiswa] = $datapengujiTAindividu[$id_kelompok_ta]
                        ->map(function ($penguji) use ($penilaianindividu, $idMahasiswa, $namaMahasiswa, $nim, $aspekpenilaianindividu) {
                            $penguji['id_mahasiswa'] = $idMahasiswa;
                            $penguji['nama_mahasiswa'] = $namaMahasiswa;
                            $penguji['nim'] = (string) $nim;

                            if (isset($penilaianindividu[$idMahasiswa])) {
                                foreach ($penilaianindividu[$idMahasiswa] as $nilaiItem) {
                                    if ((int) $penguji['id_dosen'] === (int) $nilaiItem['id_dosen']) {
                                        $aspekId = (int) $nilaiItem['id_aspekTA_individu'];
                                        $namaAspek = $aspekpenilaianindividu[$aspekId] ?? "Tidak Diketahui";

                                        // Simpan dengan nama aspek_penilaian, bukan ID
                                        $penguji[$namaAspek] = $nilaiItem['nilai'];
                                    }
                                }
                            }
                            return $penguji;
                        });
                }
            }
            }
            // Debug hasil akhirnya
            // dd($datapengujiByMahasiswa->toArray());
            // rata2
            $rataRataNilaiindividu = collect();

            foreach ($datapengujiByMahasiswa as $idMahasiswa => $pengujiGroup) {
                $namaMahasiswa = optional($pengujiGroup->first())['nama_mahasiswa'] ?? "Tidak Diketahui";
                $idKelompokTA = optional($pengujiGroup->first())['id_kelompok_ta'] ?? null;
                $nim = optional($pengujiGroup->first())['nim'] ?? "Tidak Diketahui";

                $aspekNilai = [];
                $jumlahDosen = count($pengujiGroup);
                $totalKeseluruhan = 0; // Variabel untuk menyimpan total nilai

                // Iterasi untuk menghitung total nilai per aspek
                foreach ($pengujiGroup as $penguji) {
                    foreach ($penguji as $key => $value) {
                        if (!in_array($key, ['id_penguji', 'id_kelompok_ta', 'id_dosen', 'id_mahasiswa', 'nama_mahasiswa', 'nama_dosen', 'nim'])) {
                            $aspekNilai[$key] = ($aspekNilai[$key] ?? 0) + ($value ?? 0);
                        }
                    }
                }

                // Ambil aspek penilaian individu dari database
                $aspeknilaiindividu = AspekPenilaianTAIndividu::where('id_kategori_ta', $id)
                    ->get(['id', 'aspek_penilaian', 'porsi_penilaian']);

                $aspeknilaiindividuAModified = $aspeknilaiindividu->map(function ($item) {
                    return [
                        'id_aspek_penilaian' => $item->id,
                        'aspek_penilaian' => $item->aspek_penilaian,
                        'porsi_penilaian' => (int) $item->porsi_penilaian,
                    ];
                });

                // Hitung rata-rata per aspek berdasarkan jumlah dosen
                foreach ($aspekNilai as $aspek => $totalNilai) {
                    $porsi = optional($aspeknilaiindividuAModified->firstWhere('aspek_penilaian', $aspek))['porsi_penilaian'] ?? 0;
                    $averageValue = $jumlahDosen > 0 ? round($totalNilai / $jumlahDosen, 2) : 0;
                    $nilaiFinal = $averageValue * ($porsi / 100);
                    $aspekNilai[$aspek] = $averageValue;  
                    // $aspekNilai[$aspek] = $nilaiFinal;
                    $totalKeseluruhan += $nilaiFinal;
                }

                // Susun array hasil agar total_nilai berada di bawah sendiri
                $dataMahasiswa = [
                    'id_mahasiswa' => $idMahasiswa,
                    'nim' => (string) $nim,
                    'nama_mahasiswa' => $namaMahasiswa,
                    'id_kelompok_ta' => $idKelompokTA,
                ] + $aspekNilai; // Tambahkan nilai aspek dulu
                $dataMahasiswa['total_nilai_individu'] = round($totalKeseluruhan, 2); // Tambahkan total_nilai di akhir

                // Tambahkan data ke koleksi hasil akhir
                $rataRataNilaiindividu->push($dataMahasiswa);
            }

            // Debugging output
            // dd($rataRataNilaiindividu->toArray());

            // Koleksi untuk menyimpan hasil akhir
            $hasilGabungan = collect();

            // Iterasi data individu
            $hasilGabungan = collect();

            // Iterasi data individu
            foreach ($rataRataNilaiindividu as $individu) {
                $idKelompok = $individu['id_kelompok_ta'];

                // Temukan nilai kelompok berdasarkan id_kelompok_ta
                $nilaiKelompok = $datapengujiTAModified->first(function ($group) use ($idKelompok) {
                    return isset($group[$idKelompok]);
                });

                // Jika ditemukan, ambil nilai kelompok yang sesuai
                $nilaiKelompokData = $nilaiKelompok[$idKelompok] ?? [];

                // Hapus atribut yang tidak perlu dari nilai kelompok sebelum digabungkan
                $nilaiKelompokData = collect($nilaiKelompokData)->except([
                    'id_penguji', 'id_kelompok_ta', 'kelompok_ta', 'dosen'
                ])->toArray();

                // Hitung total nilai keseluruhan
                $totalNilaiIndividu = $individu['total_nilai_individu'] ?? 0;
                $totalNilaiKelompok = $nilaiKelompokData['total_nilai_kelompok'] ?? 0;
                $totalNilaiKeseluruhan = $totalNilaiIndividu + $totalNilaiKelompok;

                // Gabungkan nilai individu dengan nilai kelompok
                $hasilAkhir = array_merge($individu, $nilaiKelompokData);

                // Tambahkan total nilai keseluruhan
                $hasilAkhir['total_nilai_keseluruhan'] = round($totalNilaiKeseluruhan, 2);

                // Simpan hasil ke koleksi akhir
                $hasilGabungan->push($hasilAkhir);
            }

            // Debugging Output
            // dd($hasilGabungan->toArray());
            if ($datapengujiTA->isEmpty()) {
                return redirect()->back()->with('error', 'Data penilaian tidak ditemukan.');
            }
            
            // untuk modal edit
            $kategoriTA = KategoriTA::where('id', $id)->first();
            // dd($kategoriTA);

            return view("main.datapenilaianTA", compact('datapengujiTA','datapengujiByMahasiswa','hasilGabungan','kategoriTA'));
        }
            catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

    }

    public function hasilpenilaianTAuntukdosen($id)
    {
            try {
                    $user = Auth::user();
                    $kategoriTA = KategoriTA::where('id', $id)->first();
                    // dd($kategoriTA);
                
        
                    //  // jika data penilaian kosong maka return back
                    //     $cekdatapenilaian = PenilaianMagang::where('id_dosen', $user->id)->get(); // Atau query lain sesuai kebutuhan Anda
        
                    //     if ($cekdatapenilaian->isEmpty()) { // Periksa jika koleksi kosong
                    //         return redirect()->back()->with('error', 'Data tidak ditemukan');
                    //     }
                
                // Penilaian Kelompok ----------------------------------------------------------------------
                // 1. ambil data penguji magang, dan kelompokkan berdasarkan Kelompok TA
                $datapengujiTA = DataPengujiTa::where('id_dosen', $user->id)->get(['id', 'id_dosen', 'id_kelompok_ta'])
                ->groupBy('id_kelompok_ta')
                ->map(function ($items) { // $items adalah collection dari kelompok TA yang sama
                    return $items->map(function ($item) { // Iterasi tiap objek dalam kelompok
                        return [
                            'id_penguji' => $item->id,
                            'id_kelompok_ta' => $item->id_kelompok_ta,
                            'kelompok_ta' => $item->KelompokTA->nama_kelompok,
                            'dosen' => $item->userdosenTA->details->gelar_depan . ' ' . 
                                    $item->userdosenTA->name . ', ' . 
                                    $item->userdosenTA->details->gelar_belakang, 
                        ];
                    });
                });
                // dd($datapengujiTA);
                // 2. ambil data aspek penilaian TA berdasarkan kategorinya pada $id
                //  kelompok
                $aspekpenilaianTA = AspekPenilaianTA::where('id_kategori_ta', $id)
                ->get(['id', 'id_kategori_ta', 'aspek_penilaian', 'porsi_penilaian'])
                ->map(function ($item) {
                    return [
                        'id_aspek_penilaian' => (string) $item->id, // Konversi ID menjadi string
                        'id_kategori_ta' => (string) $item->id_kategori_ta, // Konversi ID menjadi string
                        'aspek_penilaian' => (string) $item->aspek_penilaian, // Konversi ID menjadi string
                        'porsi_penilaian' => (integer) $item->porsi_penilaian,
                    ];
                });
                // dd($aspekpenilaianTA);
                // 3. ambil data penilaian
                $datanilaiTA = PenilaianTA::where('id_dosen', $user->id)->get(['id_data_pengujiTA','id_aspekTA', 'nilai'])
                    ->map(function ($item) {
                    return [
                        'id_penguji'=> $item->id_data_pengujiTA,
                        'id_aspek_penilaian'=> $item->id_aspekTA,
                        'nilai' => $item->nilai,
                    ];
                })->groupBy('id_penguji');
                //   dd($datanilaiTA );
                // 4. Gabungkan data penguji magang dengan aspek penilaian dan isi nilai dengan data penilaian
                $datapengujiTA =  $datapengujiTA->map(function ($items) use ($aspekpenilaianTA, $datanilaiTA) {
                    return $items->map(function ($item) use ($aspekpenilaianTA, $datanilaiTA) {
                        // dd($item);
                        $penilaian = []; 
                        // Aspek penilaian dengan ID sebagai kunci dan nama aspek sebagai nilai
                        foreach ($aspekpenilaianTA as $aspect) {
                            // Menambahkan nama aspek penilaian dengan nilai default null
                            $penilaian[$aspect['aspek_penilaian']] = null;
                        }
                        // Pastikan id_penguji ada dalam $datanilaiTA dan data penilaian ada
                        if (isset($datanilaiTA[$item['id_penguji']])) {
                            foreach ($datanilaiTA[$item['id_penguji']] as $nilai) {
                                // Gantilah kunci dengan nama aspek penilaian
                                foreach ($aspekpenilaianTA as $aspect) {
                                    if ($nilai['id_aspek_penilaian'] == $aspect['id_aspek_penilaian']) {
                                        
                                        // Cek apakah nilai adalah angka atau huruf
                                        if (is_numeric($nilai['nilai'])) {
                                            // Jika angka, konversi menjadi integer
                                            $penilaian[$aspect['aspek_penilaian']] = (int)$nilai['nilai'];
                                        } else {
                                            // Jika huruf, tampilkan 5 karakter pertama
                                            $penilaian[$aspect['aspek_penilaian']] = $nilai['nilai'];
                                        }
                                    }
                                }
                            }
                        }
                        // Gabungkan menggunakan operator `+` untuk menjaga kunci string
                        return array_merge($item, $penilaian);
                    });
                    });
                    // dd($datapengujiTA);
                        
                

                // Penilaian Individu
            // 1. Ambil data nilai individu dan kelompokkan berdasarkan id_mahasiswa
            $penilaianindividu = PenilaianTAindividu::where('id_kategori_TA', $id)
            ->where('id_dosen', $user->id)
            ->get()
                ->map(function ($item) {
                    return [
                        "id_mahasiswa" => (int) $item->id_mahasiswa,
                        "id_kategori_TA" => (int) $item->id_kategori_TA,
                        "id_dosen" => (int) $item->id_dosen,
                        "id_aspekTA_individu" => (int) $item->id_aspekTA_individu,
                        "nilai" => (int) $item->nilai,
                    ];
                })
                ->groupBy('id_mahasiswa');

                // dd($penilaianindividu);
                // 2. Ambil daftar aspek penilaian (Gunakan ID untuk pencocokan)
                $aspekpenilaianindividu = AspekPenilaianTAIndividu::where('id_kategori_TA', $id)->get(['id', 'aspek_penilaian',])
                ->mapWithKeys(function ($item) {
                    return [(int) $item->id => $item->aspek_penilaian]; 
                })
                ->toArray();
                // 3. Ambil data peserta TA dan kelompokkan berdasarkan kelompok TA
                $pesertaTA = PesertaTA::get(['id_mahasiswa', 'id_kelompok_ta'])
                ->groupBy('id_kelompok_ta');
                // 4. Ambil data penguji TA dan kelompokkan berdasarkan kelompok TA
                $datapengujiTAindividu = DataPengujiTa::where('id_dosen', $user->id)->get(['id', 'id_dosen', 'id_kelompok_ta'])
                ->groupBy('id_kelompok_ta')
                ->map(function ($items) use ($aspekpenilaianindividu) {
                    return $items->map(function ($item) use ($aspekpenilaianindividu) {
                        return [
                            'id_penguji' => (int) $item->id,
                            'id_kelompok_ta' => (int) $item->id_kelompok_ta,
                            'id_dosen' => (int) $item->userdosenTA->id,
                            'nama_dosen' => (string) $item->userdosenTA->name,
                        ] + array_fill_keys(array_values($aspekpenilaianindividu), null); // Ubah ID ke aspek_penilaian dengan nilai NULL
                    });
                });
                // 5. Buat struktur data penguji berdasarkan mahasiswa dengan nilai yang sesuai
                $datapengujiByMahasiswa = collect();
                foreach ($pesertaTA as $id_kelompok_ta => $mahasiswaCollection) {
                    if (isset($datapengujiTAindividu[$id_kelompok_ta])) {
                        foreach ($mahasiswaCollection as $mahasiswa) {
                            $idMahasiswa = (int) $mahasiswa->id_mahasiswa;
                            $namaMahasiswa = optional($mahasiswa->usermahasiswaTA)->name ?? "Tidak Diketahui";
                
                            // Ambil nilai kelompok berdasarkan id_kelompok_ta
                            $nilaiKelompok = $datapengujiTA[$id_kelompok_ta] ?? collect();
                
                            $datapengujiByMahasiswa[$idMahasiswa] = $datapengujiTAindividu[$id_kelompok_ta]
                                ->map(function ($penguji) use ($penilaianindividu, $idMahasiswa, $namaMahasiswa, $aspekpenilaianindividu, $nilaiKelompok) {
                                    $penguji['id_mahasiswa'] = $idMahasiswa;
                                    $penguji['nama_mahasiswa'] = $namaMahasiswa;
                
                                    if (isset($penilaianindividu[$idMahasiswa])) {
                                        foreach ($penilaianindividu[$idMahasiswa] as $nilaiItem) {
                                            if ((int) $penguji['id_dosen'] === (int) $nilaiItem['id_dosen']) {
                                                $aspekId = (int) $nilaiItem['id_aspekTA_individu'];
                                                $namaAspek = $aspekpenilaianindividu[$aspekId] ?? "Tidak Diketahui";
                
                                                // Simpan nilai individu
                                                $penguji[$namaAspek] = $nilaiItem['nilai'];
                                            }
                                        }
                                    }
                
                                    // Gabungkan nilai kelompok dengan nilai individu
                                    foreach ($nilaiKelompok as $nilaiGroup) {
                                        foreach ($nilaiGroup as $key => $value) {
                                            if (!isset($penguji[$key])) {
                                                $penguji[$key] = $value; // Tambahkan nilai kelompok jika belum ada
                                            }
                                        }
                                    }
                
                                    return $penguji;
                                });
                        }
                    }
                }
                
                // // Dump hasil akhir
                // dd($datapengujiByMahasiswa->toArray());
        
            
                    // dd($datapengujimagang);
                
                    return view("main.hasilpenilaianTAdosen", compact('datapengujiByMahasiswa','kategoriTA'));
        
                    
                }
                catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Data tidak ditemukan');
                }
    }
   
    public function hasilpenilaianmaganguntukdosen()
    {
       
        try {
            $user =Auth::user();

             // jika data penilaian kosong maka return back
                $cekdatapenilaian = PenilaianMagang::where('id_dosen', $user->id)->get(); // Atau query lain sesuai kebutuhan Anda

                if ($cekdatapenilaian->isEmpty()) { // Periksa jika koleksi kosong
                    return redirect()->back()->with('error', 'Data tidak ditemukan');
                }
         
            
             // 1. ambil data penguji magang, dan kelompokkan berdasarkan mahasiswa
             $datapengujimagang = DataPengujiMagang::where('id_dosen', $user->id)->get(['id','id_mahasiswa', 'id_dosen', 'status_dosen']); // Gunakan eager loading untuk menghindari N+1 query
             $datapengujimagang = $datapengujimagang->map(function ($item) {
                 return [
                     'id_penguji' => $item->id,
                     'id_mahasiswa' => $item->id_mahasiswa,
                     'mahasiswa' => $item->usermahasiswa->name . ' '. '(' . $item->usermahasiswa->details->nim . ')',
                     'dosen' =>$item->userdosen->details->gelar_depan. ' ' .$item->userdosen->name.','.' '. $item->userdosen->details->gelar_belakang. ' '. '('.$item->status->status_dosen. ')', 
                    //  'status dosen' =>$item->status->status_dosen
                 ];
             })->groupBy('id_mahasiswa'); // Mengelompokkan berdasarkan 'mahasiswa'
            //  dd($datapengujimagang);

             // 2. ambil data aspek penilaian
            $aspekpenilaianmagang  = DataAspekPenilaianMagang::get(['id','aspek_penilaian','porsi_penilaian']);
            $aspekpenilaianmagang = $aspekpenilaianmagang->map(function ($item) {
                return [
                    'id_aspek_penilaian' => (string) $item->id, // Konversi ID menjadi string
                    'aspek_penilaian' => (string) $item->aspek_penilaian, // Konversi ID menjadi string
                    'porsi_penilaian' => (integer) $item->porsi_penilaian,

                ];
            });
            // dd($aspekpenilaianmagang);

             // 3. ambil data penilaian
             $datanilaimagang = PenilaianMagang::where('id_dosen', $user->id)->get(['id_data_penguji_magangs','id_mahasiswa','id_aspek_penilaian', 'nilai']);
             $datanilaimagang = $datanilaimagang->map(function ($item) {
                 return [
                     'id_penguji'=> $item->id_data_penguji_magangs,
                     'id_mahasiswa'=> $item->id_mahasiswa,
                     'id_aspek_penilaian'=> $item->id_aspek_penilaian,
                     'nilai' => $item->nilai,
                 ];
             })->groupBy('id_penguji');
            //  dd($datanilaimagang);

            // 4. Gabungkan data penguji magang dengan aspek penilaian dan isi nilai dengan data penilaian
            $datapengujimagang = $datapengujimagang->map(function ($items) use ($aspekpenilaianmagang, $datanilaimagang) {
                return $items->map(function ($item) use ($aspekpenilaianmagang, $datanilaimagang) {
                    $penilaian = []; 
                    // Aspek penilaian dengan ID sebagai kunci dan nama aspek sebagai nilai
                    foreach ($aspekpenilaianmagang as $aspect) {
                        // Menambahkan nama aspek penilaian dengan nilai default null
                        $penilaian[$aspect['aspek_penilaian']] = null;
                    }
                    // Pastikan id_penguji ada dalam $datanilaimagang dan data penilaian ada
                    if (isset($datanilaimagang[$item['id_penguji']])) {
                        foreach ($datanilaimagang[$item['id_penguji']] as $nilai) {
                            // Gantilah kunci dengan nama aspek penilaian
                            foreach ($aspekpenilaianmagang as $aspect) {
                                if ($nilai['id_aspek_penilaian'] == $aspect['id_aspek_penilaian']) {
                                    
                                    // Cek apakah nilai adalah angka atau huruf
                                    if (is_numeric($nilai['nilai'])) {
                                        // Jika angka, konversi menjadi integer
                                        $penilaian[$aspect['aspek_penilaian']] = (int)$nilai['nilai'];
                                    } else {
                                        // Jika huruf, tampilkan 5 karakter pertama
                                        $penilaian[$aspect['aspek_penilaian']] = substr($nilai['nilai'], 0, 10). ' ...'; 
                                    }
                                }
                            }
                        }
                    }
                    // Gabungkan menggunakan operator `+` untuk menjaga kunci string
                    return array_merge($item, $penilaian);
                });
            });
            // dd($datapengujimagang);
            return view("main.hasilpenilaianmagangdosen", compact('datapengujimagang'));

            
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
    }


    public function formpenilaianmagang($id)
    {
        
        try {
            $user =Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            $datapenguji = DataPengujiMagang::where('id', $id)->first();
            // dd($datapenguji);

            // Pisahkan aspekpenilaian berdasarkan tipedata
            // agar tipe data deskripsi pada view tampil paling bawah
            $aspekpenilaian = DataAspekPenilaianMagang::all();
            [$deskripsiItems, $nonDeskripsiItems] = $aspekpenilaian->partition(function ($item) {
                return $item->tipedata === 'Deskripsi';
            });
            // partition() adalah metode koleksi Laravel yang membagi sebuah koleksi menjadi dua berdasarkan sebuah kondisi.
            // Fungsi ini mengembalikan array dengan dua elemen:
            // Elemen pertama berisi item yang memenuhi kondisi (true) $deskripsiItems.
            // Elemen kedua berisi item yang tidak memenuhi kondisi (false) $nonDeskripsiItems.


            // Fungsi callback function ($item) mengevaluasi setiap item dalam koleksi $aspekpenilaian untuk memeriksa apakah properti tipedata dari item bernilai 'Deskripsi'.
            // Jika tipedata === 'Deskripsi', item dimasukkan ke dalam $deskripsiItems (elemen pertama dari hasil partition()).
            // Jika tidak, item dimasukkan ke dalam $nonDeskripsiItems (elemen kedua dari hasil partition()).
            // Hasil Akhir:

            // $deskripsiItems akan berisi semua item dengan tipedata === 'Deskripsi'.
            // $nonDeskripsiItems akan berisi semua item lain (bukan 'Deskripsi').

            

            // Gabungkan kembali dengan "Deskripsi" di urutan terakhir
            $aspekpenilaian = $nonDeskripsiItems->concat($deskripsiItems);
          
            
            return view("main.formpenilaianmagang", compact('datapenguji','aspekpenilaian'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
       
    }

    // public function penilaianTAstore(Request $request)
    // {
    //     // dd($request);
    //     try {
    //         // Ambil data dari request
    //         $idDataPengujiTA = $request->input('data_penguji_ta');
    //         $idDosen = $request->input('id_dosen');
    //         $idKelTA = $request->input('id_kelompok_ta');
    //         $idKatTA = $request->input('id_kategori_ta');
    //         // dd($idKatTA);
    //         // Iterasi melalui input numerik (asumsi input "name" adalah angka)
    //         foreach ($request->all() as $key => $value) {
    //             if (is_numeric($key)) { // Cek jika key adalah angka
    //                 // Simpan ke tabel penilaian_magangs
    //                 penilaianTA::create([
    //                     'id_data_pengujiTA' => $idDataPengujiTA, 
    //                     'id_dosen' =>  $idDosen, 
    //                     'id_kelompok_ta' => $idKelTA, 
    //                     'id_kategori_TA' => $idKatTA,
    //                     'id_aspekTA' => $key, // Key numerik dari form input
    //                     'nilai' => $value, // Nilai yang diinputkan user
    //                 ]);
    //             }
    //         }
          

    //         // Redirect kembali dengan pesan sukses
    //         return redirect()->back()->with('success', 'Penilaian berhasil Ditambahkan');
            
    //     } catch (\Exception $e) {
    //         // Tangkap error dan tampilkan pesan error
    //         return redirect()->back()->with('error', 'Data Gagal Ditambahkan: ' . $e->getMessage());
    //     }
    // }

    // public function penilaianTAstoreIndividu(Request $request)
    // {
    //     // dd($request);
    //     try {
           
    //         $idMahasiswaList = $request->input('id_mahasiswa'); // Array mahasiswa
    //         $idPengujiTAList = $request->input('data_penguji_ta'); // Array penguji TA
    //         $idDosenList = $request->input('id_dosen'); // Array dosen
    //         $penilaianList = $request->input('penilaian'); // Array penilaian
    //         $id_kategori_ta = $request->input('id_kategori_ta'); // Array penilaian
            
    //         $dataToInsert = [];
    
    //         $i = 0; // Gunakan index untuk mengambil id_mahasiswa dengan urutan yang benar
    //         foreach ($penilaianList as $idAsliMahasiswa => $aspekPenilaian) {
    //             if (!isset($idMahasiswaList[$i])) {
    //                 continue;
    //             }
                
    //             $idMahasiswa = $idMahasiswaList[$i]; // Ambil berdasarkan urutan
    //             foreach ($aspekPenilaian as $idAspekTAIndividu => $nilai) {
    //                 $dataToInsert[] = [
    //                     'id_data_pengujiTA' => $idPengujiTAList[0], // Ambil yang pertama dari request
    //                     'id_aspekTA_individu' => $idAspekTAIndividu, // Ambil key dari array penilaian
    //                     'id_dosen' => $idDosenList[0], // Ambil yang pertama dari request
    //                     'id_kategori_TA' => $id_kategori_ta[0],
    //                     'id_mahasiswa' => $idMahasiswa, // Urutkan berdasarkan input
    //                     'nilai' => $nilai // Ambil nilai dari array
    //                 ];
    //             }
    //             $i++; // Pindah ke mahasiswa berikutnya
    //         }
    
    //         // Debugging sebelum insert
    //         // dd($dataToInsert);
    //         if (!empty($dataToInsert)) {
    //             PenilaianTAindividu::insert($dataToInsert);
    //         }

    //         // Redirect kembali dengan pesan sukses
    //         return redirect()->back()->with('success', 'Penilaian berhasil Ditambahkan');
            
    //     } catch (\Exception $e) {
    //         // Tangkap error dan tampilkan pesan error
    //         return redirect()->back()->with('error', 'Data Gagal Ditambahkan: ' . $e->getMessage());
    //     }
    // }

    public function penilaianTAstoreGabungan(Request $request)
    {
        try {
            // ========== PENILAIAN KELOMPOK ==========
            if ($request->has('penilaian_kelompok')) {
                foreach ($request->penilaian_kelompok as $idAspekTA => $nilai) {
                    penilaianTA::create([
                        'id_data_pengujiTA' => $request->data_penguji_ta,
                        'id_dosen' => $request->id_dosen,
                        'id_kelompok_ta' => $request->id_kelompok_ta,
                        'id_kategori_TA' => $request->id_kategori_ta,
                        'id_aspekTA' => $idAspekTA,
                        'nilai' => $nilai,
                    ]);
                }
            }

            // ========== PENILAIAN INDIVIDU ==========
            if ($request->has('penilaian_individu')) {
                $dataToInsert = [];
                foreach ($request->penilaian_individu as $idMahasiswa => $aspekPenilaian) {
                    foreach ($aspekPenilaian as $idAspekTAIndividu => $nilai) {
                        $dataToInsert[] = [
                            'id_data_pengujiTA' => $request->data_penguji_ta,
                            'id_aspekTA_individu' => $idAspekTAIndividu,
                            'id_dosen' => $request->id_dosen,
                            'id_kategori_TA' => $request->id_kategori_ta,
                            'id_mahasiswa' => $idMahasiswa,
                            'nilai' => $nilai,
                        ];
                    }
                }

                if (!empty($dataToInsert)) {
                    PenilaianTAindividu::insert($dataToInsert);
                }
            }

            return redirect()->back()->with('success', 'Semua penilaian berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function penilaianmagangstore(Request $request)
    {
        try {
            // Ambil id_data_penguji_magangs dari request
        $idDataPengujiMagangs = $request->input('data_penguji_magang');
        $idDosen = $request->input('id_dosen');
        $idMahasiswa = $request->input('id_mahasiswa');
         // Iterasi melalui input numerik (key mulai dari 4 hingga 9 karena name pada inputan adalah numerik)
        foreach ($request->all() as $key => $value) {
            if (is_numeric($key)) { // Cek jika key adalah angka
                // Simpan ke tabel penilaian_magangs
                PenilaianMagang::create([
                    'id_data_penguji_magangs' => $idDataPengujiMagangs, // Ambil dari request
                    'id_dosen' =>  $idDosen, // Ambil dari request
                    'id_mahasiswa' => $idMahasiswa, // Ambil dari request
                    'id_aspek_penilaian' => $key, // Key numerik (4, 5, 6, ...)
                    'nilai' => $value, // Value dari key tersebut ("60", "80", ...)
                ]);
            }
        }
        return redirect()->route('penilaianmagang')->with('success', 'Data Penilaian Berhasil Ditambahkan');

        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan'  . $e->getMessage());
        }
    }

    public function editnilaitakelompok($id_penguji, $id_kategoriTA)
    {
        try {
            $user =Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $penilaianTA = PenilaianTA::where('id_data_pengujiTA', $id_penguji)
                ->select('id_aspekTA', 'nilai')
                ->get()
                ->keyBy('id_aspekTA'); // supaya gampang dicocokkan berdasarkan id_aspekTA
            $kategoriTA = KategoriTA::where('id', $id_kategoriTA)->first();
            $aspekpenilaianta = AspekPenilaianTA::where('id_kategori_ta', $kategoriTA->id)
                ->select('id', 'aspek_penilaian', 'tipedata')
                ->get();
            
            $aspekWithNilai = $aspekpenilaianta->map(function ($aspek) use ($penilaianTA) {
                $nilai = $penilaianTA[$aspek->id]->nilai ?? 0;

                // cek kondisi khusus untuk tipedata Deskripsi
                if ($aspek->tipedata === 'Deskripsi' && ($nilai === 0 || $nilai === null)) {
                    $nilai = 'Belum ada Catatan';
                }

                return [
                    'id' => $aspek->id,
                    'aspek_penilaian' => $aspek->aspek_penilaian,
                    'tipedata' => $aspek->tipedata,
                    'nilai' => $nilai,
                ];
            });
            // dd($aspekWithNilai);
            $datapenguji = DataPengujiTa::with(['KelompokTA', 'userdosenTA'])
                ->where('id', $id_penguji)
                ->first();
            return view("main.editpenilaianTAkelompok", compact('aspekWithNilai','datapenguji','kategoriTA'));

           
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
       
    }

    public function updateNilaiKelompok(Request $request, $id_penguji, $id_kategoriTA)
    {
        try {
              $user =Auth::user();
                    if (!$user) {
                        abort(403, 'Profil Tidak Ditemukan');
                    }     
                    
                // dd($request);
               $request->validate([
                    'nilai.*' => 'nullable|string',
                ]);

                foreach ($request->nilai as $id_aspek => $nilai) {
                    // cari aspek supaya bisa ambil id_kategori_TA
                    $aspek = AspekPenilaianTA::find($id_aspek);

                    // cari penguji supaya bisa ambil id_dosen & id_kelompok_ta
                    $penguji = DataPengujiTA::find($id_penguji);

                    PenilaianTA::updateOrCreate(
                        [
                            'id_data_pengujiTA' => $id_penguji,
                            'id_aspekTA' => $id_aspek,
                        ],
                        [
                            'nilai'          => $nilai,
                            'id_kategori_TA' => $aspek ? $aspek->id_kategori_ta : null,
                            'id_dosen'       => $penguji ? $penguji->id_dosen : null,
                            'id_kelompok_ta' => $penguji ? $penguji->id_kelompok_ta : null,
                        ]
                    );
                }
                
                
                return redirect()->route('hasilpenilaianta', $id_kategoriTA)->with('success', 'Nilai berhasil diperbarui');
            }
        catch (\Exception $e) {
                 return redirect()->back()->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
            }
    }

    public function editnilaitaIndividu($id_penguji, $id_kategoriTA, $id_dosen, $id_mahasiswa)
    {
        try {
            $user =Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $penguji = DataPengujiTa::with('KelompokTA', 'userdosenTA')
            ->where('id', $id_penguji)
            ->where('id_dosen', $id_dosen)
            ->firstOrFail();

            // Data mahasiswa
            $mahasiswa = User::with('details')->findOrFail($id_mahasiswa);

            // Data kategori TA
            $kategoriTA = KategoriTA::where('id', $id_kategoriTA)->firstOrFail();

            // Ambil aspek penilaian sesuai kategori
            $aspekpenilaianta = AspekPenilaianTAIndividu::where('id_kategori_ta', $kategoriTA->id)
                ->select('id', 'aspek_penilaian', 'tipedata')
                ->get();
            
            $penilaianTA = PenilaianTAindividu::where('id_data_pengujiTA', $id_penguji)
                ->where('id_dosen', $id_dosen)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->get()
                ->keyBy('id_aspekTA_individu');

            $aspekWithNilai = $aspekpenilaianta->map(function ($aspek) use ($penilaianTA) {
                $nilai = $penilaianTA[$aspek->id]->nilai ?? 0;

                if ($aspek->tipedata === 'Deskripsi' && empty($nilai)) {
                    $nilai = "Belum ada Catatan";
                }

                return [
                    'id_aspek_penilaian_individu'=> $aspek->id,
                    'aspek_penilaian'            => $aspek->aspek_penilaian,
                    'tipedata'                   => $aspek->tipedata,
                    'nilai'                      => $nilai,
                ];
            });
                // dd($aspekWithNilai);
                
           
            return view("main.editpenilaianTAIndividu", compact('penguji', 'mahasiswa', 'kategoriTA', 'aspekWithNilai'));

           
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
       
    }

    public function updateNilaiIndividu(Request $request, $id_penguji, $id_kategoriTA, $id_dosen, $id_mahasiswa)
    {
        try {
            $user =Auth::user();
                    if (!$user) {
                        abort(403, 'Profil Tidak Ditemukan');
                    }     
            // Validasi nilai, karena di DB longText, kita boleh nullable dan string
            $request->validate([
                'nilai.*' => ['nullable'], // deskripsi bisa teks, input numeric juga bisa string
            ]);

            foreach ($request->nilai as $id_aspek => $nilai) {
                PenilaianTAindividu::updateOrCreate(
                    [
                        'id_data_pengujiTA' => $id_penguji,
                        'id_aspekTA_individu' => $id_aspek,
                        'id_dosen' => $id_dosen,
                        'id_mahasiswa' => $id_mahasiswa,
                        'id_kategori_TA' => $id_kategoriTA,
                    ],
                    [
                        'nilai' => $nilai,
                    ]
                );
            }

            // Redirect ke halaman hasil penilaian per kategori TA
            return redirect()->route('hasilpenilaianta', ['id' => $id_kategoriTA])
                ->with('success', 'Nilai berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function editnilaitamagang($id_penguji, $id_mahasiswa)
    {
        try {
            $user =Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            $penguji = DataPengujiMagang::with('usermahasiswa', 'userdosen')
            ->where('id', $id_penguji)
            ->where('id_mahasiswa', $id_mahasiswa)
            ->firstOrFail();

            // dd($penguji);

            // Data mahasiswa
            $mahasiswa = User::with('details')->findOrFail($id_mahasiswa);

            // Ambil aspek penilaian sesuai kategori
            $aspekpenilaian = DataAspekPenilaianMagang::get();
            
            $penilaian = PenilaianMagang::where('id_data_penguji_magangs', $id_penguji)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->get()
                ->keyBy('id_aspek_penilaian');


            $aspekWithNilai = $aspekpenilaian->map(function ($aspek) use ($penilaian) {
                $nilai = $penilaian[$aspek->id]->nilai ?? 0;

                if ($aspek->tipedata === 'Deskripsi' && empty($nilai)) {
                    $nilai = "Belum ada Catatan";
                }

                return [
                    'id_aspek_penilaian_magang'  => $aspek->id,
                    'aspek_penilaian'            => $aspek->aspek_penilaian,
                    'tipedata'                   => $aspek->tipedata,
                    'nilai'                      => $nilai,
                ];
            });
                // dd($aspekWithNilai);
                
           
            return view("main.editpenilaianMagang", compact('penguji', 'mahasiswa', 'aspekWithNilai'));
           
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan' . $e->getMessage());
            
        }
       
    }

    public function updateNilaimagang(Request $request, $id_penguji, $id_mahasiswa, $id_dosen)
    {
        try {
            $user =Auth::user();
                    if (!$user) {
                        abort(403, 'Profil Tidak Ditemukan');
                    }     
            // Validasi nilai, karena di DB longText, kita boleh nullable dan string
            $request->validate([
                'nilai.*' => ['nullable'], // deskripsi bisa teks, input numeric juga bisa string
            ]);

            // dd($request);
            foreach ($request->nilai as $id_aspek => $nilai) {
                PenilaianMagang::updateOrCreate(
                    [
                        'id_data_penguji_magangs' => $id_penguji,
                        'id_dosen' => $id_dosen,
                        'id_mahasiswa' => $id_mahasiswa,
                        'id_aspek_penilaian' => $id_aspek,
                    ],
                    [
                        'nilai' => $nilai,
                    ]
                );
            }

            // Redirect ke halaman hasil penilaian per kategori TA
            return redirect()->route('hasilpenilaianmagang')
                ->with('success', 'Nilai berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    

    
    
    
}

<?php

namespace App\Http\Controllers;

use App\Imports\DataPesertaMagangImport;
use App\Models\AspekPenilaianTA;
use App\Models\DataAspekPenilaianMagang;
use App\Models\DataPengujiMagang;
use App\Models\DataPengujiTa;
use App\Models\JadwalTA;
use App\Models\KategoriTA;
use App\Models\KelompokTA;
use App\Models\PenilaianMagang;
use App\Models\PenilaianTA;
use App\Models\PesertaMagang;
use App\Models\PesertaTA;
use App\Models\UserDetail;
use App\Models\VerifikasiKelompokTA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;


class MainController extends Controller
{
    public function myprofile()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }
            return view("main.myprofile", compact('user'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Profile tidak ditemukan');
        }
       
    }

    public function dokumenmagang()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Profil Tidak Ditemukan');
            }

            $datapenilaianmagang = PenilaianMagang::with('pengujimagang')->select('id_data_penguji_magangs','id_mahasiswa','id_dosen','id_aspek_penilaian', 'nilai')
            ->where('id_mahasiswa', $user->id)
            ->get();

            $tanggalpresentasi = PesertaMagang::where('id_mahasiswa', $user->id)->value('tanggal_presentasi'); // Ambil satu nilai, jika hanya ada satu tanggal_presentasi untuk setiap mahasiswa
            

            $datapenilaianmagang = $datapenilaianmagang->map(function ($item) use ($tanggalpresentasi) {
                return [
                    'id_penguji' => $item->id_data_penguji_magangs,
                    'mahasiswa' => $item->pengujimagang->usermahasiswa->name . '(' . $item->pengujimagang->usermahasiswa->details->nim . ')',
                    'dosen' => $item->pengujimagang->userdosen->details->gelar_depan . ' ' . $item->pengujimagang->userdosen->name . ' ' . $item->pengujimagang->userdosen->details->gelar_belakang,
                    'status' => $item->pengujimagang->status->status_dosen,
                    'aspek_penilaian' => $item->aspekpenilaianmagang->aspek_penilaian,
                    'nilai' => $item->nilai,
                    'tanggal_presentasi' => $tanggalpresentasi, // Tambahkan tanggal_presentasi
                ];
            });
              
            $fieldtabel = DataAspekPenilaianMagang::all();  // Mengambil semua data dari model DataAspekPenilaianMagang
            $fieldtabelaspek = $fieldtabel->pluck('aspek_penilaian')->toArray();  // Mengambil hanya kolom aspek_penilaian dan mengubahnya menjadi array
            // dd($fieldtabelaspek);


            $finalData = [];
            // Proses untuk menyusun data ke dalam format yang diinginkan
            foreach ($datapenilaianmagang as $item) {
                if (!isset($finalData[$item['id_penguji'] . '-' . $item['mahasiswa']])) {
                    $finalData[$item['id_penguji'] . '-' . $item['mahasiswa']] = [
                        'id_penguji' => $item['id_penguji'],
                        'tanggal_presentasi' => $item['tanggal_presentasi'],
                        'mahasiswa' => $item['mahasiswa'],
                        'dosen' => $item['dosen'],
                        'status' => $item['status'],
                       
                    ];
                }

                // Cek dan masukkan nilai jika aspek_penilaian ada
                $finalData[$item['id_penguji'] . '-' . $item['mahasiswa']][$item['aspek_penilaian']] = $item['nilai'];
            }
            // dd($finalData);

            $datadifaldata = array_column($finalData, 'id_penguji');
            $datapenguji = DataPengujiMagang::where('id_mahasiswa', $user->id)->pluck('id')->toArray();
            if (!empty(array_diff($datadifaldata, $datapenguji)) || !empty(array_diff($datapenguji, $datadifaldata))) {
                // Jika ada perbedaan (data tidak cocok)
                return redirect()->back()->with('error', 'Form Revisi Belum Tersedia');
            }
            // dd($datadifaldata, $datapenguji);

            // Menambahkan nilai default '-' untuk setiap aspek yang ada di $fieldtabelaspek yang belum ada di data mahasiswa
            foreach ($finalData as &$data) {
                foreach ($fieldtabelaspek as $aspek) {
                    // Jika aspek penilaian belum ada di data mahasiswa, tambahkan dengan nilai default '-'
                    if (!isset($data[$aspek])) {
                        $data[$aspek] = '-';
                    }

                    // Cek jika nilai pada aspek ini tidak numerik
                    if (!is_numeric($data[$aspek]) && $data[$aspek] != '-') {
                        // Hanya tampilkan nilai non-numerik atau tambahkan label '(Non-numeric)'
                        $data[$aspek] = $data[$aspek];
                    } else {
                        // Jika nilai numerik, hapus nilai numerik tersebut
                        unset($data[$aspek]);
                    }
                }
            }
            // Re-index array untuk hasil akhir
            $finalData = array_values($finalData);
            // Mengelompokkan data berdasarkan mahasiswa
            $groupedFinalData = collect($finalData)->groupBy('mahasiswa');
            // Debugging untuk melihat hasil akhir
            // dd($groupedFinalData);

            return view("main.dokumenmagang", compact('user','groupedFinalData'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Profile tidak ditemukan');
        }
       
    }

    public function view_dokumen_magang()
    {
        try {

            $user = Auth::user();
        if (!$user) {
            abort(403, 'Profil Tidak Ditemukan');
        }

        $datapenilaianmagang = PenilaianMagang::with('pengujimagang')->select('id_data_penguji_magangs','id_mahasiswa','id_dosen','id_aspek_penilaian', 'nilai')
        ->where('id_mahasiswa', $user->id)
        ->get();

        if ($datapenilaianmagang->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        

        $tanggalpresentasi = PesertaMagang::where('id_mahasiswa', $user->id)->value('tanggal_presentasi'); // Ambil satu nilai, jika hanya ada satu tanggal_presentasi untuk setiap mahasiswa
        

        $datapenilaianmagang = $datapenilaianmagang->map(function ($item) use ($tanggalpresentasi) {
            return [
                'id_penguji' => $item->id_data_penguji_magangs,
                'mahasiswa' => $item->pengujimagang->usermahasiswa->name . '(' . $item->pengujimagang->usermahasiswa->details->nim . ')',
                'dosen' => $item->pengujimagang->userdosen->details->gelar_depan . ' ' . $item->pengujimagang->userdosen->name . ' ' . $item->pengujimagang->userdosen->details->gelar_belakang,
                'status' => $item->pengujimagang->status->status_dosen,
                'aspek_penilaian' => $item->aspekpenilaianmagang->aspek_penilaian,
                'nilai' => $item->nilai,
                'tanggal_presentasi' => $tanggalpresentasi, // Tambahkan tanggal_presentasi
            ];
        });
          
        $fieldtabel = DataAspekPenilaianMagang::all();  // Mengambil semua data dari model DataAspekPenilaianMagang
        $fieldtabelaspek = $fieldtabel->pluck('aspek_penilaian')->toArray();  // Mengambil hanya kolom aspek_penilaian dan mengubahnya menjadi array
        // dd($fieldtabelaspek);


        $finalData = [];
        // Proses untuk menyusun data ke dalam format yang diinginkan
        foreach ($datapenilaianmagang as $item) {
            if (!isset($finalData[$item['id_penguji'] . '-' . $item['mahasiswa']])) {
                $finalData[$item['id_penguji'] . '-' . $item['mahasiswa']] = [
                    'id_penguji' => $item['id_penguji'],
                    'tanggal_presentasi' => $item['tanggal_presentasi'],
                    'mahasiswa' => $item['mahasiswa'],
                    'dosen' => $item['dosen'],
                    'status' => $item['status'],
                   
                ];
            }

            // Cek dan masukkan nilai jika aspek_penilaian ada
            $finalData[$item['id_penguji'] . '-' . $item['mahasiswa']][$item['aspek_penilaian']] = $item['nilai'];
        }
        // dd($finalData);

        $datadifaldata = array_column($finalData, 'id_penguji');
        $datapenguji = DataPengujiMagang::where('id_mahasiswa', $user->id)->pluck('id')->toArray();
        if (!empty(array_diff($datadifaldata, $datapenguji)) || !empty(array_diff($datapenguji, $datadifaldata))) {
            // Jika ada perbedaan (data tidak cocok)
            return redirect()->back()->with('error', 'Form Revisi Belum Tersedia');
        }
        // dd($datadifaldata, $datapenguji);

        // Menambahkan nilai default '-' untuk setiap aspek yang ada di $fieldtabelaspek yang belum ada di data mahasiswa
        foreach ($finalData as &$data) {
            foreach ($fieldtabelaspek as $aspek) {
                // Jika aspek penilaian belum ada di data mahasiswa, tambahkan dengan nilai default '-'
                if (!isset($data[$aspek])) {
                    $data[$aspek] = '-';
                }

                // Cek jika nilai pada aspek ini tidak numerik
                if (!is_numeric($data[$aspek]) && $data[$aspek] != '-') {
                    // Hanya tampilkan nilai non-numerik atau tambahkan label '(Non-numeric)'
                    $data[$aspek] = $data[$aspek];
                } else {
                    // Jika nilai numerik, hapus nilai numerik tersebut
                    unset($data[$aspek]);
                }
            }
        }
        // Re-index array untuk hasil akhir
        $finalData = array_values($finalData);
        // Mengelompokkan data berdasarkan mahasiswa
        $groupedFinalData = collect($finalData)->groupBy('mahasiswa');
        // dd($groupedFinalData);
       
            $htmlContent = View::make('main.dokmagang', compact('user','groupedFinalData'))->render();
            
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4-L', // L = Landscape
                'margin_top' => 50,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'margin_right' => 10,
            ]);
            $mpdf->WriteHTML($htmlContent); // Write HTML content to the PDF
            return $mpdf->Output('Bukti_Revisi_Magang.pdf', 'I'); // atau 'D' jika mau force download
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Profile tidak ditemukan');
        }


        
       
        
    }

    public function beritaacara(Request $request)
    {
        try {
           // Mengambil data query string
            $id_kelompok_ta = $request->query('id_kelompok_ta');
            $id_kategori_ta = $request->query('id_kategori_ta');
            
            $kategori = KategoriTA::where('id',$id_kategori_ta)->first();
            // dd($kategori);

            $kelompok = KelompokTA::where('id',$id_kelompok_ta)->first();
            // dd($kelompok);

            $jadwalta = JadwalTA::where('id_kelompok_ta', $id_kelompok_ta)
            ->where('id_kategori_ta', $id_kategori_ta)->first();
            // dd($jadwalta);

            $peserta = PesertaTA::with('usermahasiswaTA')->where('id_kelompok_ta', $id_kelompok_ta)->get();
            // dd($peserta);

            $penguji= DataPengujiTa::with(['statusdosenTA','userdosenTA'])->where('id_kelompok_ta', $id_kelompok_ta)->get();
            // dd($penguji);

            return view("main.beritaacara", compact('kategori','kelompok','jadwalta','peserta','penguji'));

       
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Profile tidak ditemukan');
        }
    }

    public function beritaacaramagang(Request $request)
    {
        try {

         
            $datamagang = $request->all();
            $id_mhs = $datamagang['id_mahasiswa'];
            $pengujimagang = DataPengujiMagang::with('userdosen')
                ->where('id_mahasiswa', $id_mhs)
                ->get();
                
        //    dd($datamagang);

            return view("main.beritaacaramagang", compact('datamagang','pengujimagang'));

       
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Profile tidak ditemukan');
        }
    }



    public function view_dokumen_ta($id)
    {
        try {

            $user = Auth::user();
        if (!$user) {
            abort(403, 'Profil Tidak Ditemukan');
        }

        $id_kelompok_ta = PesertaTA::where('id_mahasiswa', $user->id)->first();
        $kelompok = KelompokTA::where('id', $id_kelompok_ta->id_kelompok_ta)->first();
        $kategori = KategoriTA::where('id', $id)->first();
        // dd($kategori);
       
        $datapenilaianTA = PenilaianTA::with('pengujiTA')->select('id_data_pengujiTA','id_kelompok_ta','id_dosen','id_aspekTA', 'nilai')
        ->where('id_kelompok_ta', $id_kelompok_ta->id_kelompok_ta)
        ->where('id_kategori_TA', $id)
        ->get();
        // dd($datapenilaianTA);
        if ($datapenilaianTA->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $peserta = PesertaTA::with('usermahasiswaTA')->where('id_kelompok_ta', $id_kelompok_ta->id_kelompok_ta)->get();
   
        

        $tanggalpresentasi = JadwalTA::where('id_kelompok_ta', $id_kelompok_ta->id_kelompok_ta)
        ->where('id_kategori_TA', $id)
        ->value('tanggal_presentasi'); // Ambil satu nilai, jika hanya ada satu tanggal_presentasi untuk setiap mahasiswa
        // dd($tanggalpresentasi);

       $datapenilaianTA = $datapenilaianTA->map(function ($item) use ($tanggalpresentasi) {
            return [
                'id_penguji' => $item->id_data_pengujiTA,
                'kelompok_ta' => $item->pengujiTA->KelompokTA->nama_kelompok,
                'dosen' => $item->pengujiTA->userdosenTA->details->gelar_depan . ' ' . $item->pengujiTA->userdosenTA->name . ' ' . $item->pengujiTA->userdosenTA->details->gelar_belakang,
                'status' => $item->pengujiTA->statusdosenTA->status_dosen,
                'aspek_penilaian' => $item->aspekpenilaianTA->aspek_penilaian,
                'nilai' => $item->nilai,
                'tanggal_presentasi' => $tanggalpresentasi, // Tambahkan tanggal_presentasi
                
            ];
        });
        // dd($datapenilaianTA);
        $fieldtabel = AspekPenilaianTA::where('id_kategori_TA', $id)->get();  // Mengambil semua data dari model DataAspekPenilaianMagang
        $fieldtabelaspek = $fieldtabel->pluck('aspek_penilaian')->toArray();  // Mengambil hanya kolom aspek_penilaian dan mengubahnya menjadi array
        // dd($fieldtabelaspek);


        $finalData = [];
        // Proses untuk menyusun data ke dalam format yang diinginkan
        foreach ($datapenilaianTA as $item) {
            if (!isset($finalData[$item['id_penguji'] . '-' . $item['kelompok_ta']])) {
                $finalData[$item['id_penguji'] . '-' . $item['kelompok_ta']] = [
                    'id_penguji' => $item['id_penguji'],
                    'tanggal_presentasi' => $item['tanggal_presentasi'],
                    'kelompok_ta' => $item['kelompok_ta'],
                    'dosen' => $item['dosen'],
                    'status' => $item['status'],
                   
                ];
            }

            // Cek dan masukkan nilai jika aspek_penilaian ada
            $finalData[$item['id_penguji'] . '-' . $item['kelompok_ta']][$item['aspek_penilaian']] = $item['nilai'];
        }
        // dd($finalData);

        $datadifaldata = array_column($finalData, 'id_penguji');
        $datapenguji = DataPengujiTa::where('id_kelompok_ta', $id_kelompok_ta->id_kelompok_ta)->pluck('id')->toArray();
        // dd($datapenguji);


        $diff1 = array_diff($datadifaldata, $datapenguji);
        $diff2 = array_diff($datapenguji, $datadifaldata);

        $namaDosen = DataPengujiTa::with('userdosenTA')
            ->where('id_kelompok_ta', $id_kelompok_ta->id_kelompok_ta)
            ->whereIn('id', $diff2)
            ->get()
            ->pluck('userdosenTA.name')
            ->toArray();

            // dd($namaDosen);
        

        // dd($datadifaldata, $datapenguji, $diff1, $diff2);
        if (!empty(array_diff($datadifaldata, $datapenguji)) || !empty(array_diff($datapenguji, $datadifaldata))) {
            // Jika ada perbedaan (data tidak cocok)
            return redirect()->back()->with(
                'error',
                'Form Revisi Belum Tersedia, Silahkan Hubungi dosen terkait untuk menilai : ' . implode(', ', $namaDosen)
            );
        }
        // dd($datadifaldata, $datapenguji);

        // Menambahkan nilai default '-' untuk setiap aspek yang ada di $fieldtabelaspek yang belum ada di data mahasiswa
        foreach ($finalData as &$data) {
            foreach ($fieldtabelaspek as $aspek) {
                // Jika aspek penilaian belum ada di data mahasiswa, tambahkan dengan nilai default '-'
                if (!isset($data[$aspek])) {
                    $data[$aspek] = '-';
                }

                // Cek jika nilai pada aspek ini tidak numerik
                if (!is_numeric($data[$aspek]) && $data[$aspek] != '-') {
                    // Hanya tampilkan nilai non-numerik atau tambahkan label '(Non-numeric)'
                    $data[$aspek] = $data[$aspek];
                } else {
                    // Jika nilai numerik, hapus nilai numerik tersebut
                    unset($data[$aspek]);
                }
            }
        }
        // Re-index array untuk hasil akhir
        $finalData = array_values($finalData);
        
        // Mengelompokkan data berdasarkan mahasiswa
        $groupedFinalData = collect($finalData)->groupBy('kelompok_ta');
        // dd($finalData, $groupedFinalData);
       
            $htmlContent = View::make('main.dokta', compact('kelompok','groupedFinalData','kategori','peserta'))->render();
            
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4-L', // L = Landscape
                'margin_top' => 35,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'margin_right' => 10,
            ]);
            $mpdf->WriteHTML($htmlContent); // Write HTML content to the PDF
            return $mpdf->Output('Bukti_Revisi_TA.pdf', 'I'); // atau 'D' jika mau force download
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }


        
       
        
    }

    public function uploadphoto(Request $request)
    {
        $userId = auth()->id(); // Dapatkan user_id dari user yang terautentikasi

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

    public function uploadLaporanMagang(Request $request)
    { 
        try {
             // Validasi file
       $request->validate(
            [
                'file' => 'required|mimes:pdf|max:2048',
            ],
            [
                'file.max' => 'Dokumen gagal diupload. Ukuran file maksimal 2MB.',
            ]
        );

        $nim = Auth::user()->details->nim;
        // Proses upload file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $nim . '.' . $file->getClientOriginalExtension();
            // Simpan file ke folder "public"
            $path = $file->storeAs('uploads/laporan', $fileName, 'public');
            // Berikan response sukses
            return redirect()->back()->with('success', 'Laporan berhasil diupload!')->with('file', $path);
        }

        // Jika file tidak ditemukan
        return redirect()->back()->with('error', 'Gagal mengupload file. Silakan coba lagi.');

        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Dokumen gagal diupload');
        }

    }

    public function uploadjudulmagang(Request $request)
    {
        try {
            // 🔒 Pastikan user sudah login
            $user = Auth::user();
            if (!$user) {
                return redirect()->back()->with('error', 'Anda harus login terlebih dahulu.');
            }

            // ✅ Validasi input
            $request->validate([
                'judul_laporan' => 'required|string|max:255',
            ], [
                'judul_laporan.required' => 'Judul laporan wajib diisi.',
                'judul_laporan.string' => 'Judul laporan harus berupa teks.',
                'judul_laporan.max' => 'Judul laporan maksimal 255 karakter.',
            ]);

            // 🧭 Ambil data peserta magang berdasarkan user login
            $peserta = PesertaMagang::where('id_mahasiswa', $user->id)->first();

            if (!$peserta) {
                return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
            }

            // 💾 Simpan atau perbarui judul laporan
            $peserta->judul_laporan = $request->judul_laporan;
            $peserta->save();

            return redirect()->back()->with('success', 'Judul Laporan berhasil diperbarui.');
        } 
        catch (\Exception $e) {
            // 🧨 Tangani error jika terjadi
            return redirect()->back()->with('error', 'Gagal Input Judul Laporan. ' . $e->getMessage());
        }
    }
    

    public function uploadLaporanTA(Request $request)
    { 
        // dd($request);
        try {
             // Validasi file
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048', // Maksimum 2MB
            'id_kelompok_ta' => 'required|string',
            'id_kategori_ta' => 'required|string',
        ]);

        $idkelompok= $request->id_kelompok_ta;
        $idkategori= $request->id_kategori_ta;

        $kelompokTA = KelompokTA::where('id',$idkelompok)->first();
        $kategoriTA = KategoriTA::where('id',$idkategori)->first();
        // Proses upload file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'LAPORAN' . '-' . $kelompokTA->nama_kelompok . '-' . $kategoriTA->nama_kategori . '.' . $file->getClientOriginalExtension();
            // Simpan file ke folder "public"
            $path = $file->storeAs('uploads/laporan', $fileName, 'public');
            // Berikan response sukses
            return redirect()->back()->with('success', 'Laporan berhasil diupload!')->with('file', $path);
        }

        // Jika file tidak ditemukan
        return redirect()->back()->with('error', 'Gagal mengupload file. Silakan coba lagi.');

        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Dokumen gagal diupload: ' . $e->getMessage());
        }

    }

    public function uploadRevisiTA(Request $request)
    { 
        // dd($request);
        try {
             // Validasi file
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048', // Maksimum 2MB
            'id_kelompok_ta' => 'required|string',
            'id_kategori_ta' => 'required|string',
            'id_dosen' => 'required|string',
            'nama_dosen' => 'required|string',
        ]);

        $idkelompok= $request->id_kelompok_ta;
        $idkategori= $request->id_kategori_ta;
        $iddosen= $request->id_dosen;
        $namadosen= $request->nama_dosen;

        $kelompokTA = KelompokTA::where('id',$idkelompok)->first();
        $kategoriTA = KategoriTA::where('id',$idkategori)->first();

        // dd($kelompokTA);
        // Proses upload file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $iddosen . '-' .'REV' . '-' . $kelompokTA->nama_kelompok . '-' . $kategoriTA->nama_kategori .  '-' . $namadosen . '.' . $file->getClientOriginalExtension();
            // Simpan file ke folder "public"
            $path = $file->storeAs('uploads/laporan', $fileName, 'public');
            // Berikan response sukses
            return redirect()->back()->with('success', 'Laporan berhasil diupload!')->with('file', $path);
        }

        // Jika file tidak ditemukan
        return redirect()->back()->with('error', 'Gagal mengupload file. Silakan coba lagi.');

        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Dokumen gagal diupload: ' . $e->getMessage());
        }

    }


    public function formTAmhs($id)
    { 
        try {
            // dd($id);

            $kategorita =KategoriTA::where('id', $id)->first();
            // dd($kategorita);
            $user = Auth::user();
            $pesertatamhs = PesertaTA::with('usermahasiswaTA')
            ->where('id_mahasiswa', $user->id)
            ->first();
            $dosenta = DataPengujiTa::with('KelompokTA','userdosenTA','statusdosenTA')->where('id_kelompok_ta', $pesertatamhs->id_kelompok_ta)->get();
           

            $jadwalta = JadwalTA::with(['kelompokTA','kategoriTA'])
            ->where('id_kelompok_ta',$pesertatamhs->id_kelompok_ta)
            ->where('id_kategori_ta', $id)
            ->first();
            // dd($jadwalta);

            $verifikasi = VerifikasiKelompokTA::where('id_kelompok_ta', $pesertatamhs->id_kelompok_ta)
            ->where('id_kategori_ta', $id) // semua tahapan sebelum ini
            ->first();
            

            $ditolakSebelumnya = VerifikasiKelompokTA::where('id_kelompok_ta', $pesertatamhs->id_kelompok_ta)
                ->where('id_kategori_ta', '<', $id) // semua tahapan sebelum ini
                ->where('status', '3')             // status ditolak
                ->exists();

            if ($ditolakSebelumnya) {
                return redirect()->back()->with('error', 'Anda tidak dapat melanjutkan karena ada event sebelumnya yang ditolak.');
            }
           


            return view("main.formTAmhs", compact('dosenta','jadwalta','kategorita','verifikasi'));
        }
            catch (\Exception $e) {
        return redirect()->back()->with('error', 'Data Tidak Ditemukan');
        }

    }


    
}

<?php

namespace App\Http\Controllers;

use App\Models\DataPengujiMagang;
use App\Models\DataPengujiTa;
use App\Models\JadwalTA;
use App\Models\PesertaMagang;
use App\Models\PesertaTA;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use App\Models\AuditLog;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function index()
    {
        return view("auth.login");
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $dataUser = User::where('email', $user->email)->first();

            $emailDomain = explode("@", $user->email)[1];

            if ($emailDomain !== 'student.atmi.ac.id' && $emailDomain !== 'atmi.ac.id') {
                AuditLog::catat([
                    'jenis'      => 'keamanan',
                    'aksi'       => 'login_google_ditolak',
                    'user_id'    => null,
                    'user_email' => $user->email,
                    'deskripsi'  => 'Login Google ditolak: domain tidak diizinkan',
                    'data_baru'  => ['alasan' => 'domain_salah', 'domain' => $emailDomain],
                ]);
                return redirect(route('login'))->withErrors('Email tidak terdaftar.');
            }

            if (empty($dataUser)) {
                AuditLog::catat([
                    'jenis'      => 'keamanan',
                    'aksi'       => 'login_google_ditolak',
                    'user_id'    => null,
                    'user_email' => $user->email,
                    'deskripsi'  => 'Login Google ditolak: email tidak terdaftar',
                    'data_baru'  => ['alasan' => 'email_tidak_terdaftar'],
                ]);
                return redirect(route('login'))->withErrors('Email tidak terdaftar.');
            }
            auth()->login($dataUser);
            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            return redirect(route('login'))->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    // public function authenticate(Request $request)
    // {
    //     try {
    //         $credentials = $request->validate([
    //             'email' => 'required|email',
    //             'password' => 'required',
    //         ]);
    //         // dd($credentials);
    //         if (Auth::attempt($credentials)) {
    //             User::find(Auth::user()->id_user);
    //             return redirect()->intended(route('dashboard'));
    //         } else {
    //             throw ValidationException::withMessages([
    //                 'email' => ['Kombinasi email dan password tidak valid.'],
    //             ]);
    //         }
    //     } catch (ValidationException $e) {
    //         return redirect()->route('login')->withErrors($e->errors())->withInput();
    //     } catch (\Exception $e) {
    //         return redirect()->route('login')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

        private function manualDiizinkan(Request $request): bool
    {
        $ips = config('login.ip_diizinkan', []);

        return empty($ips) || in_array($request->ip(), $ips, true);
    }

    public function manualIndex(Request $request)
    {
        abort_unless($this->manualDiizinkan($request), 404);

        return view('auth.login_manual');
    }

    public function manualAuthenticate(Request $request)
    {
        abort_unless($this->manualDiizinkan($request), 404);

        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $kunci = 'login-manual|' . Str::lower($credentials['email']) . '|' . $request->ip();
        $maks  = (int) config('login.maks_percobaan', 5);

        if (RateLimiter::tooManyAttempts($kunci, $maks)) {
            $detik = RateLimiter::availableIn($kunci);

            AuditLog::catat([
                'jenis'      => 'keamanan',
                'aksi'       => 'login_manual_diblokir',
                'user_id'    => null,
                'user_email' => $credentials['email'],
                'deskripsi'  => 'Login manual diblokir: terlalu banyak percobaan',
            ]);

            return back()
                ->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik."])
                ->onlyInput('email');
        }

        // Event Failed / Login tetap tercatat oleh subscriber di Tahap 3
        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($kunci, 60);

            return back()
                ->withErrors(['email' => 'Kombinasi email dan password tidak valid.'])
                ->onlyInput('email');
        }

        // Password benar, tapi jalur ini khusus Admin
        if (! Auth::user()->hasRole('Admin')) {
            $email = Auth::user()->email;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            RateLimiter::hit($kunci, 60);

            AuditLog::catat([
                'jenis'      => 'keamanan',
                'aksi'       => 'login_manual_ditolak',
                'user_id'    => null,
                'user_email' => $email,
                'deskripsi'  => 'Login manual ditolak: akun bukan Admin',
            ]);

            // Pesan sengaja sama, supaya tidak membocorkan bahwa password-nya benar
            return back()
                ->withErrors(['email' => 'Kombinasi email dan password tidak valid.'])
                ->onlyInput('email');
        }

        RateLimiter::clear($kunci);
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    public function logout()
    {
        try {
            Auth::logout();
            $cookie = cookie()->forget('laravel_session');
            return redirect()->route('login');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function dashboard()
    {
        try {
            // Mengambil user terautentikasi
            $user = Auth::user();
            // dd($user->name);
            // Periksa apakah user ditemukan
            $dosen = User::whereHas('roles', function ($query) {
                $query->where('name', 'Dosen');   
            })->get();

    
            $dosenpenguji = DataPengujiMagang::whereHas('userdosen', function ($query) use ($user) {
                $query->where('id_mahasiswa', $user->id);
            })->get();


            $pesertamagang = PesertaMagang::with('usermahasiswa', 'perusahaanmagang')->get();   
            $datamagang = $pesertamagang->where('id_mahasiswa', $user->id)->first();
            
            $pesertabimbing = DataPengujiMagang::where('id_dosen', Auth::user()->id)->get(); 

            $pesertabimbingan = $pesertabimbing->map(function ($bimbingan) use ($pesertamagang) {
                // Cari data magang yang sesuai dengan id_mahasiswa
                $dataMagang = $pesertamagang->firstWhere('id_mahasiswa', $bimbingan->id_mahasiswa);
                // Tambahkan tanggal_presentasi jika data ditemukan
                $bimbingan->tanggal_presentasi = $dataMagang ? $dataMagang->tanggal_presentasi : null;
                $bimbingan->jam_presentasi = $dataMagang ? $dataMagang->jam_presentasi : null;
                $bimbingan->lokasi = $dataMagang ? $dataMagang->lokasi : null;
            
                return $bimbingan;
            });

            // dd($pesertamagang);
            // ------------------------data TA dosen------------------------
            $pengujita = DataPengujiTa::with('KelompokTA','userdosenTA','statusdosenTA')->where('id_dosen', $user->id)->get();
            // dd($pengujita);
            $pesertata = PesertaTA::with('usermahasiswaTA')->get();
            $jadwalta = JadwalTA::with(['kelompokTA','kategoriTA'])->get();


            // ------------------------data TA dMahasiswa------------------------
            $pesertatamhs = PesertaTA::with(['usermahasiswaTA','kelompokTA'])
            ->where('id_mahasiswa', $user->id)
            ->first();

            // deklarasilan null karena jika login diluar user mahasiswa maka $peserta mahasiswa bernilai null,, karena menggunakan auth user
            $dosenta = null;
            if ($pesertatamhs && $pesertatamhs->id_kelompok_ta) {
                $dosenta = DataPengujiTa::with('KelompokTA', 'userdosenTA', 'statusdosenTA')
                    ->where('id_kelompok_ta', $pesertatamhs->id_kelompok_ta)
                    ->get()
                    ->unique('id_dosen')   // <-- tambahan ini, dedup berdasarkan id_dosen
                    ->values();            // <-- reset index array setelah unique()
            }

            if (!$user) {
                abort(403, 'Profile Not Found');
            }
                // dd($pesertatamhs);
            
            return view("main.dashboard", compact('user', 'dosen', 'dosenpenguji','pesertamagang','pesertabimbingan','datamagang','pengujita','pesertata','jadwalta','dosenta','pesertatamhs' ));
        } 
        catch (\Exception $e) {
            // Tanggapi kesalahan dan kirim pesan ke view
            return redirect()->back()->with('error', 'Profile tidak ditemukan');
        }
    }
}

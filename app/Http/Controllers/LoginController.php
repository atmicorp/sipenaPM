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
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

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
                return redirect(route('login'))->withErrors('Email tidak terdaftar.');
            }

            if (empty($dataUser)) {
                return redirect(route('login'))->withErrors('Email tidak terdaftar.');
            }
            auth()->login($dataUser);
            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            return redirect(route('login'))->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function authenticate(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            // dd($credentials);
            if (Auth::attempt($credentials)) {
                User::find(Auth::user()->id_user);
                return redirect()->intended(route('dashboard'));
            } else {
                throw ValidationException::withMessages([
                    'email' => ['Kombinasi email dan password tidak valid.'],
                ]);
            }
        } catch (ValidationException $e) {
            return redirect()->route('login')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
                    ->get();
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

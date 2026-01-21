<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\UserImportController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// coba push

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'authenticate'])->name('loginprocess');
    Route::get('google', [LoginController::class, 'redirectToGoogle']);
    Route::get('google/callback', [LoginController::class, 'handleGoogleCallback']);
});

Route::redirect('/', '/auth/login'); //akan otomatis diarahkan ke halaman login (/auth/login)

Route::prefix('main')->middleware(['auth'])->group(function () {
    Route::get('dashboard', [LoginController::class, 'dashboard'])->name('dashboard'); //penamaan route
    Route::get('myprofile', [MainController::class, 'myprofile'])->name('myprofile'); //penamaan route
    Route::post('uploadphoto', [MainController::class, 'uploadphoto'])->name('uploadphoto'); //penamaan route
     Route::get('dokumenmagang', [MainController::class, 'dokumenmagang'])->name('dokumenmagang'); //penamaan route
   
});

Route::prefix('dokumen')->middleware(['auth', 'role:Mahasiswa'])->group(function () {
     Route::get('dokumenmagang', [MainController::class, 'dokumenmagang'])->name('dokumenmagang'); //penamaan route 
     Route::post('upload-laporan-magang', [MainController::class, 'uploadLaporanMagang'])->name('upload.laporan.magang');
     Route::post('upload-judul-magang', [MainController::class, 'uploadjudulMagang'])->name('upload.judul.magang');
     Route::post('upload-laporan-ta', [MainController::class, 'uploadLaporanTA'])->name('uploadLaporanTA');

     Route::get('form-ta/{id}', [MainController::class, 'formTAmhs'])->name('formTAmhs');

     //update judul TA
     Route::put('judul-ta/{id}', [ManageController::class, 'updatekjudulta'])->name('judulta.update');

     Route::get('docmagang', [MainController::class, 'view_dokumen_magang'])->name('view_dokumen_magang'); //penamaan route
     Route::get('docTA/{id}', [MainController::class, 'view_dokumen_ta'])->name('view_dokumen_ta'); //penamaan route  

});

Route::prefix('assessment')->middleware(['auth', 'role:Dosen'])->group(function () {
    Route::get('praktik-kerja', [AssessmentController::class, 'penilaianmagang'])->name('penilaianmagang'); //penamaan route  
    Route::get('penilaian-praktik-kerja/{id}', [AssessmentController::class, 'formpenilaianmagang'])->name('formpenilaianmagang'); //penamaan route 
    Route::post('store-penilaian-magang', [AssessmentController::class, 'penilaianmagangstore'])->name('penilaianmagangstore'); //penamaan route   
    Route::get('hasil-penilaian-magang', [AssessmentController::class, 'hasilpenilaianmaganguntukdosen'])->name('hasilpenilaianmaganguntukdosen'); //penamaan route  

    // Penilaian TA-----------------------------
    Route::get('tugas-akhir/{id}', [AssessmentController::class, 'penilaianTA'])->name('penilaianTA');
    Route::get('penilaian-ta/{id}', [AssessmentController::class, 'formpenilaianTA'])->name('formpenilaianTA');
    Route::post('store-penilaian-ta', [AssessmentController::class, 'penilaianTAstore'])->name('penilaianTAstore'); //penamaan route  
    Route::post('store-penilaian-individu', [AssessmentController::class, 'penilaianTAstoreIndividu'])->name('penilaianTAstoreIndividu'); //penamaan route   

    Route::post('store-penilaian-gabungan', [AssessmentController::class, 'penilaianTAstoreGabungan'])->name('penilaianTAstoreGabungan'); //penamaan route 
   
    Route::get('hasil-penilaian-ta/{id}', [AssessmentController::class, 'hasilpenilaianTAuntukdosen'])->name('hasilpenilaianTAuntukdosen'); //penamaan route  
    

    // Tolak Presentasi
    Route::post('tolak-penilaian-ta', [AssessmentController::class, 'tolakPenilaian'])->name('tolakPenilaian'); //penamaan route  
    Route::post('lanjut-Penilaian', [AssessmentController::class, 'lanjutPenilaian'])->name('lanjutPenilaian'); //penamaan route  


    // uupload
    Route::post('uploadRevisiTA', [MainController::class, 'uploadRevisiTA'])->name('uploadRevisiTA');

    
});


Route::prefix('manage')->middleware(['auth', 'role:Admin'])->group(function () {
    // manageMAgang
    Route::get('penempatan-praktik-kerja', [ManageController::class, 'penempatanmagang'])->name('penempatanmagang'); //penamaan route  
    Route::get('view-penempatan', [ManageController::class, 'viewpenempatanmagang'])->name('viewpenempatanmagang'); //penamaan route  
    Route::post('tambah-data-penempatan-praktik-kerja', [ManageController::class, 'storepenempatanmagang'])->name('storepenempatanmagang'); //penamaan route  
    Route::put('update-peserta/{id}', [ManageController::class, 'pesertaupdate'])->name('pesertaupdate'); //penamaan route  
    Route::get('setupdatamagang/{id}', [ManageController::class, 'setupdatamagang'])->name('setupdatamagang'); //penamaan route  
    Route::get('aspekpenilaian', [ManageController::class, 'aspekpenilaian'])->name('aspekpenilaian'); //penamaan route  
    Route::post('tambah-aspek-penilaian', [ManageController::class, 'storeaspekdata'])->name('storeaspekdata'); //penamaan route  
    Route::post('tambah-data-pembimbing', [ManageController::class, 'storedatapembimbing'])->name('storedatapembimbing'); //penamaan route  
    Route::get('hasilpenilaianmagang', [AssessmentController::class, 'hasilpenilaianmagang'])->name('hasilpenilaianmagang'); //penamaan route 
    Route::get('deletedatamagang/{id}', [ManageController::class, 'deletedatamagang'])->name('deletedatamagang'); //penamaan route 
    Route::delete('aspek/{id}', [ManageController::class, 'deleteaspek'])->name('deleteAspek');
    Route::get('reset-penilaian-magang', [ManageController::class, 'resetPenilaian'])->name('resetpenilaianmagang');
    Route::post('delete-penilaian-magang', [ManageController::class, 'deletePenilaian'])->name('deletepenilaianmagang');
    Route::post('change-password/{id}', [ManageController::class, 'changePassword'])->name('change.password');

    // manageTA
    Route::get('manage-ta', [ManageController::class, 'manageTA'])->name('manageTA'); //penamaan route  
  
    Route::get('pesertata/{id}', [ManageController::class, 'destroypesertata'])->name('pesertata.destroy'); // DELETE
    Route::get('vupdatepesertata/{id}', [ManageController::class, 'vpesertataupdate'])->name('vpesertata.update');
    Route::put('updatepesertata/{id}', [ManageController::class, 'pesertataupdate'])->name('pesertata.update'); // Ganti POST ke PUT
    
    Route::put('updatedosenta/{id}', [ManageController::class, 'dosentaupdate'])->name('dosenta.update');
    Route::get('pengujita/{id}', [ManageController::class, 'destroypengujita'])->name('pengujita.destroy'); // DELETE
    
    Route::put('kelompokta/{id}', [ManageController::class, 'kelompoktaupdate'])->name('kelompokta.update');
    Route::put('editkelompokta/{id}', [ManageController::class, 'updatekta'])->name('kta.update');
   
    Route::get('setupjadwalta/{id}', [ManageController::class, 'setupjadwalta'])->name('setupjadwalta'); 
    Route::put('updatejadwalta/{id}', [ManageController::class, 'updatejadwalta'])->name('updatejadwalta'); 
    

    Route::get('aspekpenilaian-ta/{id}', [ManageController::class, 'aspekpenilaianta'])->name('aspekpenilaianta'); //penamaan route 
    Route::post('tambah-aspek-penilaian-TA', [ManageController::class, 'storeaspekdatata'])->name('storeaspekdatata'); //penamaan route   
    Route::delete('delete-aspek-ta/{id}', [ManageController::class, 'deleteaspekta'])->name('deleteaspekta');

    Route::get('aspekpenilaian-individu', [ManageController::class, 'aspekpenilaianindividu'])->name('aspekpenilaianindividu'); //penamaan route 
    Route::post('tambah-aspek-penilaian-individu', [ManageController::class, 'storeaspekdatataindividu'])->name('storeaspekdatataindividu'); //penamaan route   
    Route::delete('delete-aspek-individu/{id}', [MainController::class, 'deleteaspektaindividu'])->name('deleteaspektaindividu');

    Route::get('hasil-penilaian-ta/{id}', [AssessmentController::class, 'hasilpenilaianta'])->name('hasilpenilaianta'); //penamaan route  

    Route::get('berita-acara', [MainController::class, 'beritaacara'])->name('berita_acara'); //penamaan route
    Route::get('berita-acara-magang', [MainController::class, 'beritaacaramagang'])->name('berita_acara_magang'); //penamaan route
    
    // manage user   
    Route::get('data-user', [ManageController::class, 'datauser'])->name('datauser'); //penamaan route  
    Route::get('edit-user/{id}', [ManageController::class, 'edituser'])->name('edituser'); //penamaan route  
    Route::post('update-user/{id}', [ManageController::class, 'updateuser'])->name('update.user');
    Route::post(' uploadphotoeditadmin/{id}', [ManageController::class, 'uploadphotoeditadmin'])->name('uploadphotoeditadmin'); //penamaan route
    Route::post('/import', [UserImportController::class, 'import'])->name('users.import');
    //reset data
    Route::post('reset-database', [ManageController::class, 'resetDatabase'])->name('reset.database');
    Route::get('reset-data', [ManageController::class, 'viewresetdatabase'])->name('reset.view');

    // adminedit nilai
    Route::get('edit-nilai-ta-kelompok/{id_penguji}/{id_kategoriTA}', [AssessmentController::class, 'editnilaitakelompok'])->name('editnilaitakelompok');  
    Route::post('update-nilai-ta-kelompok/{id_penguji}/{id_kategoriTA}', [AssessmentController::class, 'updateNilaiKelompok'])->name('updateNilaiKelompok');

    Route::get('edit-nilai-ta-individu/{id_penguji}/{id_kategoriTA}/{id_dosen}/{id_mahasiswa}', [AssessmentController::class, 'editnilaitaIndividu'])->name('editnilaitaIndividu'); 
    Route::post('update-nilai-ta-individu/{id_penguji}/{id_kategoriTA}/{id_dosen}/{id_mahasiswa}',[AssessmentController::class, 'updateNilaiIndividu'])->name('updateNilaiIndividu');

    Route::get('edit-nilai-ta-magang/{id_penguji}/{id_mahasiswa}', [AssessmentController::class, 'editnilaitamagang'])->name('editnilaitamagang'); 
    Route::post('update-nilai-ta-magang/{id_penguji}/{id_mahasiswa}/{id_dosen}',[AssessmentController::class, 'updateNilaimagang'])->name('updateNilaimagang');


    
   
});


Route::get('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');


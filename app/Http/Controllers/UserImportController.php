<?php

namespace App\Http\Controllers;

use App\Imports\MultipleSheetImport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class UserImportController extends Controller
{
   
    //
    public function import(Request $request)
    {
        
        // Validasi file sebelum proses import
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048', // Maksimal 2MB
        ]);

        try {
            // Jalankan proses import menggunakan MultipleSheetImport
            Excel::import(new MultipleSheetImport, $request->file('file'));

            return back()->with('success', 'Users imported successfully!');
        } catch (ValidationException $e) {
            // Menangkap error validasi (misalnya NIM atau NIK tidak ditemukan)
            // dd($e->errors()); // Letakkan di sini!
            return back()->withErrors($e->errors());
        } catch (Throwable $e) {
            // Menangkap error umum lainnya
            return back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}

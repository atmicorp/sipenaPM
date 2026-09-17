<?php

namespace App\Http\Controllers;

use App\Services\PesertaMagangImportService;
use Illuminate\Http\Request;

class PesertaMagangImportController extends Controller
{
    protected const SESSION_KEY = 'import_peserta_magang_preview';

    public function __construct(protected PesertaMagangImportService $service)
    {
    }

    /**
     * Halaman upload awal.
     */
    public function create()
    {
        return view('main.importpesertamagang');
    }

    /**
     * Baca & validasi file yang diupload, TANPA menyimpan ke database.
     * Hasilnya disimpan di session lalu ditampilkan sebagai preview.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ], [
            'file.mimes' => 'File harus berformat Excel (.xlsx), sesuai template yang disediakan.',
        ]);

        try {
            $result = $this->service->preview($request->file('file'));
        } catch (\Throwable $e) {
            return redirect()->route('importpesertamagang')
                ->with('error', 'Gagal membaca file: '.$e->getMessage());
        }

        session([self::SESSION_KEY => $result]);

        return view('main.previewpesertamagang', ['result' => $result]);
    }

    /**
     * Dipanggil setelah admin klik "Simpan" di halaman preview.
     * Baris valid disimpan, baris error/duplikat dilewati dan dilaporkan.
     */
    public function store(Request $request)
    {
        $data = session(self::SESSION_KEY);

        if (!$data || empty($data['temp_path'])) {
            return redirect()->route('importpesertamagang')
                ->with('error', 'Sesi preview sudah tidak ada (kemungkinan kadaluarsa). Silakan upload ulang file-nya.');
        }

        try {
            $result = $this->service->commit($data['temp_path']);
        } catch (\Throwable $e) {
            return redirect()->route('importpesertamagang')
                ->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }

        session()->forget(self::SESSION_KEY);

        return view('main.hasilimportpesertamagang', ['result' => $result]);
    }

    /**
     * Admin klik "Batal" di halaman preview -> file sementara dihapus,
     * tidak ada apa pun yang tersimpan ke database.
     */
    public function cancel(Request $request)
    {
        $data = session(self::SESSION_KEY);

        if ($data && !empty($data['temp_path'])) {
            $this->service->cancel($data['temp_path']);
        }

        session()->forget(self::SESSION_KEY);

        return redirect()->route('importpesertamagang')
            ->with('success', 'Import dibatalkan, tidak ada data yang disimpan.');
    }

    /**
     * Download template Excel yang sudah dikoreksi.
     */
    public function downloadTemplate()
    {
        $path = public_path('template/Template_Upload_Data_Mahasiswa.xlsx');

        if (!file_exists($path)) {
            return back()->with('error', 'File template belum tersedia di server.');
        }

        return response()->download($path, 'Template_Upload_Data_Mahasiswa.xlsx');
    }
}
<?php

namespace App\Http\Controllers;

use App\Services\LogbookImportService;
use Illuminate\Http\Request;

/**
 * Import data mahasiswa magang dari LOGBOOK:
 * upload -> preview (belum menyimpan) -> simpan / batal.
 */
class LogbookImportController extends Controller
{
    protected const SESSION_KEY = 'import_logbook_temp_path';

    public function __construct(protected LogbookImportService $service)
    {
    }

    /**
     * Halaman upload. Syarat data kosong dicek di sini supaya admin
     * langsung tahu kalau harus reset data dulu.
     */
    public function create()
    {
        $syarat = $this->service->cekSyaratKosong();

        return view('main.importlogbook', ['syarat' => $syarat]);
    }

    /**
     * Baca dan periksa file, TANPA menyimpan ke database.
     * Hanya path file sementara yang disimpan di session.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:5120',
        ], [
            'file.required' => 'Pilih file export dari logbook terlebih dahulu.',
            'file.mimes' => 'File harus berformat Excel (.xlsx) hasil export logbook.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        // Jaminan data kosong: tolak sebelum file sempat diproses.
        $syarat = $this->service->cekSyaratKosong();
        if (!$syarat['kosong']) {
            return redirect()->route('importlogbook')
                ->with('error', 'Import dibatalkan karena data mahasiswa belum kosong. Reset data terlebih dahulu.');
        }

        // Kalau sebelumnya ada preview yang belum disimpan, bersihkan dulu.
        $this->service->batal(session(self::SESSION_KEY));
        session()->forget(self::SESSION_KEY);

        try {
            $result = $this->service->preview($request->file('file'));
        } catch (\Throwable $e) {
            return redirect()->route('importlogbook')
                ->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        if ($result['temp_path']) {
            session([self::SESSION_KEY => $result['temp_path']]);
        }

        return view('main.previewlogbook', ['result' => $result]);
    }

    /**
     * Dipanggil saat admin klik "Simpan". Semua diperiksa ulang oleh service;
     * kalau ada yang tidak cocok, tidak ada yang tersimpan.
     */
    public function store(Request $request)
    {
        $tempPath = session(self::SESSION_KEY);

        if (!$tempPath) {
            return redirect()->route('importlogbook')
                ->with('error', 'Sesi preview sudah tidak ada (kemungkinan kadaluarsa). Silakan upload ulang file-nya.');
        }

        try {
            $result = $this->service->commit($tempPath);
        } catch (\Throwable $e) {
            $this->service->batal($tempPath);
            session()->forget(self::SESSION_KEY);

            return redirect()->route('importlogbook')
                ->with('error', 'Data tidak disimpan. ' . $e->getMessage());
        }

        session()->forget(self::SESSION_KEY);

        return view('main.hasilimportlogbook', ['result' => $result]);
    }

    /**
     * Admin klik "Batal": file sementara dihapus, tidak ada yang tersimpan.
     */
    public function cancel(Request $request)
    {
        $this->service->batal(session(self::SESSION_KEY));
        session()->forget(self::SESSION_KEY);

        return redirect()->route('importlogbook')
            ->with('success', 'Import dibatalkan, tidak ada data yang disimpan.');
    }
}
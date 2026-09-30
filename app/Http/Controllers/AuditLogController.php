<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // Hanya baca. Tidak ada method untuk mengubah atau menghapus log.
    public function index(Request $request)
    {
        $request->validate([
            'jenis'  => 'nullable|in:keamanan,data,nilai',
            'aksi'   => 'nullable|string|max:100',
            'akun'   => 'nullable|string|max:100',
            'dari'   => 'nullable|date',
            'sampai' => 'nullable|date',
        ]);

        $query = AuditLog::query();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }
        if ($request->filled('akun')) {
            $query->where('user_email', 'like', '%' . $request->akun . '%');
        }
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $logs = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $daftarAksi = AuditLog::query()->select('aksi')->distinct()->orderBy('aksi')->pluck('aksi');

        return view('main.logaktivitas', compact('logs', 'daftarAksi'));
    }
}
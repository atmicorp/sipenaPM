<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Support\Arr;

class AuditAksi
{
    // Route yang targetnya adalah user lain (id ada di URL)
    private const RUTE_TARGET_USER = [
        'uploadphotoeditadmin',
        'change.password',
        'update.user',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    // Dijalankan SETELAH respons dikirim, jadi tidak memperlambat dan tidak bisa
    // menggagalkan aksi utama.
    public function terminate(Request $request, Response $response): void
    {
        try {
            $this->catat($request, $response);
        } catch (Throwable $e) {
            Log::error('Audit middleware gagal: ' . $e->getMessage());
        }
    }

    private function catat(Request $request, Response $response): void
    {
        $route = $request->route();
        if (!$route) {
            return;
        }

        $nama   = $route->getName();
        $metode = $request->method();

        $mengubah   = in_array($metode, ['POST', 'PUT', 'PATCH', 'DELETE'], true);
        $destruktif = $nama && in_array($nama, config('audit.get_destruktif', []), true);

        // GET biasa (membuka halaman, download) tidak dicatat
        if (!$mengubah && !$destruktif) {
            return;
        }

        // Jangan pakai config("audit.rute.$nama"): nama route memuat titik.
        $peta = config('audit.rute', [])[$nama] ?? null;

        [$jenis, $aksi, $deskripsi] = $peta ?? [
            'data',
            'permintaan_' . Str::limit((string) ($nama ?? $request->path()), 80, ''),
            'Permintaan ' . $metode . ' ' . ($nama ?? $request->path()),
        ];

        $user = $request->user();

        // Target
        $targetTipe = null;
        $targetId   = null;
        if (in_array($nama, self::RUTE_TARGET_USER, true)) {
            $id = $route->parameter('id');
            $targetTipe = 'User';
            $targetId   = is_numeric($id) ? (int) $id : null;
        } elseif ($nama === 'uploadphoto') {
            $targetTipe = 'User';
            $targetId   = $user?->id;
        }

        // Perkiraan hasil, dibaca dari pesan flash yang diset controller
        $hasil = null;
        $pesan = null;
        if ($request->hasSession()) {
            $sesi = $request->session();
            if ($sesi->has('success')) {
                $hasil = 'berhasil';
                $pesan = $sesi->get('success');
            } elseif ($sesi->has('error')) {
                $hasil = 'gagal';
                $pesan = $sesi->get('error');
            } elseif ($sesi->has('errors')) {
                $hasil = 'gagal_validasi';
            }
        }

        // Input tanpa file (file diringkas terpisah). Penyaringan password dan foto
        // dilakukan oleh AuditLog::bersihkan().
        $kunciFile = array_keys($request->allFiles());
        $input = $request->except(array_merge(['_token', '_method'], $kunciFile));

        $parameter = array_filter(
            $route->parameters(),
            fn ($v) => is_scalar($v) || $v === null
        );

        $status = $response->getStatusCode();

        AuditLog::catat([
            'jenis'       => $jenis,
            'aksi'        => $aksi,
            'user_id'     => $user?->id,
            'user_email'  => $user?->email,
            'target_tipe' => $targetTipe,
            'target_id'   => $targetId,
            'deskripsi'   => $deskripsi . ' [HTTP ' . $status . ($hasil ? ', ' . $hasil : '') . ']',
            'data_baru'   => [
                'hasil'     => $hasil,
                'pesan'     => is_string($pesan) ? $pesan : null,
                'parameter' => $parameter ?: null,
                'input'     => $input ?: null,
                'file'      => $this->ringkasFile($request) ?: null,
            ],
            'status_code' => $status,
        ]);
    }

    private function ringkasFile(Request $request): array
    {
        $hasil = [];

        foreach ($request->allFiles() as $kunci => $berkas) {
            // Arr::wrap membungkus objek file tunggal menjadi array,
            // dan flatten meratakan kalau inputnya berisi banyak file.
            foreach (Arr::flatten(Arr::wrap($berkas)) as $f) {
                try {
                    $hasil[$kunci][] = [
                        'nama'   => $f->getClientOriginalName(),
                        'ukuran' => $f->getSize(),
                    ];
                } catch (Throwable) {
                    $hasil[$kunci][] = ['nama' => '[tidak terbaca]', 'ukuran' => null];
                }
            }
        }

        return $hasil;
    }
}
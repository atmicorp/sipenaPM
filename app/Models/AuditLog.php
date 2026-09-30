<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AuditLog extends Model
{
    // Tabel hanya punya created_at
    public const UPDATED_AT = null;

    protected $table = 'audit_logs';
    protected $guarded = ['id'];

    protected $casts = [
        'data_lama'  => 'array',
        'data_baru'  => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Log tidak boleh diubah atau dihapus lewat model.
        // (Penghapusan terjadwal nanti memakai query langsung, jadi tidak terhalang.)
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    /**
     * Satu pintu pencatatan. Dipakai semua sumber (listener, middleware, observer).
     * Tidak pernah melempar error: kalau gagal, hanya ditulis ke laravel.log.
     */
    public static function catat(array $data): void
    {
        try {
            $request = app()->runningInConsole() ? null : request();

            // Pelaku: kalau tidak diberikan eksplisit, ambil dari user yang sedang login.
            if (!array_key_exists('user_id', $data)) {
                $user = Auth::user();
                $data['user_id'] = $user?->id;
                $data['user_email'] ??= $user?->email;
            }

            $data['ip_address'] ??= $request?->ip();
            $data['user_agent'] ??= $request?->userAgent();
            $data['metode']     ??= $request?->method();
            // url() tanpa query string, supaya kode/token di callback Google tidak ikut tersimpan
            $data['url']        ??= $request?->url();

            $data['deskripsi']  = Str::limit((string) ($data['deskripsi'] ?? ''), 500, '...');
            $data['user_agent'] = isset($data['user_agent']) ? Str::limit($data['user_agent'], 500, '') : null;
            $data['url']        = isset($data['url']) ? Str::limit($data['url'], 1000, '') : null;

            $data['data_lama'] = static::bersihkan($data['data_lama'] ?? null);
            $data['data_baru'] = static::bersihkan($data['data_baru'] ?? null);

            static::create($data);
        } catch (Throwable $e) {
            try {
                Log::error('Audit log gagal ditulis: ' . $e->getMessage(), [
                    'aksi' => $data['aksi'] ?? null,
                ]);
            } catch (Throwable) {
                // Pencatatan tidak boleh mengganggu aksi utama.
            }
        }
    }

    /**
     * Buang isi kunci terlarang (password, foto, token), potong teks panjang,
     * dan ganti objek (misalnya file upload) dengan nama tipenya.
     */
    public static function bersihkan(?array $data): ?array
    {
        if (empty($data)) {
            return null;
        }

        $terlarang = array_map('strtolower', config('audit.kunci_terlarang', []));
        $maks = (int) config('audit.maks_panjang_teks', 200);
        $hasil = [];

        foreach ($data as $kunci => $nilai) {
            if (in_array(strtolower((string) $kunci), $terlarang, true)) {
                $hasil[$kunci] = '[DISEMBUNYIKAN]';
            } elseif (is_array($nilai)) {
                $hasil[$kunci] = static::bersihkan($nilai) ?? [];
            } elseif (is_string($nilai) && mb_strlen($nilai) > $maks) {
                $hasil[$kunci] = mb_substr($nilai, 0, $maks) . '... [dipotong, ' . mb_strlen($nilai) . ' karakter]';
            } elseif (is_scalar($nilai) || $nilai === null) {
                $hasil[$kunci] = $nilai;
            } else {
                $hasil[$kunci] = '[' . get_debug_type($nilai) . ']';
            }
        }

        return $hasil;
    }

    /**
     * Ringkasan foto base64 untuk log: cukup ada/tidak, ukuran, dan sidik jari singkat.
     * Dengan hash, foto lama dan baru bisa dibandingkan tanpa menyimpan gambarnya.
     */
    public static function ringkasFoto(?string $foto): array
    {
        if ($foto === null || $foto === '') {
            return ['ada' => false];
        }

        return [
            'ada'             => true,
            'ukuran_karakter' => strlen($foto),
            'hash'            => substr(sha1($foto), 0, 12),
        ];
    }
}
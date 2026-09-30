<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserObserver
{
    // Perubahan pada kolom-kolom ini saja (mis. saat logout) tidak dicatat
    private const ABAIKAN = ['updated_at', 'created_at', 'remember_token'];

    public function created(User $user): void
    {
        $this->aman(function () use ($user) {
            AuditLog::catat([
                'jenis'       => 'keamanan',
                'aksi'        => 'tambah_user',
                'target_tipe' => 'User',
                'target_id'   => $user->id,
                'deskripsi'   => "User baru dibuat: {$user->email}",
                'data_baru'   => ['name' => $user->name, 'email' => $user->email],
            ]);
        });
    }

    public function updated(User $user): void
    {
        $this->aman(function () use ($user) {
            $baru = array_diff_key($user->getChanges(), array_flip(self::ABAIKAN));

            if (empty($baru)) {
                return;
            }

            $lama = [];
            foreach (array_keys($baru) as $kolom) {
                $lama[$kolom] = $user->getRawOriginal($kolom);
            }

            // Nilai kolom 'password' otomatis diganti [DISEMBUNYIKAN] oleh AuditLog.
            AuditLog::catat([
                'jenis'       => 'keamanan',
                'aksi'        => 'ubah_user',
                'target_tipe' => 'User',
                'target_id'   => $user->id,
                'deskripsi'   => "Data user #{$user->id} ({$user->email}) berubah: " . implode(', ', array_keys($baru)),
                'data_lama'   => $lama,
                'data_baru'   => $baru,
            ]);
        });
    }

    public function deleted(User $user): void
    {
        $this->aman(function () use ($user) {
            AuditLog::catat([
                'jenis'       => 'keamanan',
                'aksi'        => 'hapus_user',
                'target_tipe' => 'User',
                'target_id'   => $user->id,
                'deskripsi'   => "User dihapus: {$user->email}",
                'data_lama'   => ['name' => $user->name, 'email' => $user->email],
            ]);
        });
    }

    private function aman(callable $fn): void
    {
        try {
            $fn();
        } catch (Throwable $e) {
            Log::error('Audit observer User gagal: ' . $e->getMessage());
        }
    }
}
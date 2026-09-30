<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserDetailObserver
{
    // Kolom yang tidak ikut dibandingkan sebagai "data biasa".
    // Foto ditangani terpisah (hanya ringkasan, bukan isinya).
    private const ABAIKAN = ['id', 'created_at', 'updated_at', 'photo'];

    public function created(UserDetail $detail): void
    {
        $this->aman(function () use ($detail) {
            $atribut = $detail->getAttributes();
            $data = array_diff_key($atribut, array_flip(self::ABAIKAN));

            AuditLog::catat([
                'jenis'       => 'keamanan',
                'aksi'        => 'tambah_detail_user',
                'target_tipe' => 'User',
                'target_id'   => $detail->user_id,
                'deskripsi'   => "Detail user #{$detail->user_id} dibuat",
                'data_baru'   => $data,
            ]);

            if (!empty($atribut['photo'])) {
                $this->catatFoto($detail, null, $atribut['photo']);
            }
        });
    }

    public function updated(UserDetail $detail): void
    {
        $this->aman(function () use ($detail) {
            $perubahan = $detail->getChanges();

            $lainnya = array_diff_key($perubahan, array_flip(self::ABAIKAN));

            // "" dan null dianggap sama: ConvertEmptyStringsToNull mengubah "" menjadi null,
            // jadi menyimpan form tanpa perubahan tidak boleh tercatat sebagai perubahan.
            $lainnya = array_filter($lainnya, function ($baru, $kolom) use ($detail) {
                $lama = $detail->getRawOriginal($kolom);
                $lamaKosong = ($lama === null || $lama === '');
                $baruKosong = ($baru === null || $baru === '');

                return !($lamaKosong && $baruKosong);
            }, ARRAY_FILTER_USE_BOTH);
            if (!empty($lainnya)) {
                $lama = [];
                foreach (array_keys($lainnya) as $kolom) {
                    $lama[$kolom] = $detail->getRawOriginal($kolom);
                }

                AuditLog::catat([
                    'jenis'       => 'keamanan',
                    'aksi'        => 'ubah_detail_user',
                    'target_tipe' => 'User',
                    'target_id'   => $detail->user_id,
                    'deskripsi'   => "Detail user #{$detail->user_id} berubah: " . implode(', ', array_keys($lainnya)),
                    'data_lama'   => $lama,
                    'data_baru'   => $lainnya,
                ]);
            }

            if (array_key_exists('photo', $perubahan)) {
                $this->catatFoto($detail, $detail->getRawOriginal('photo'), $perubahan['photo']);
            }
        });
    }

    public function deleted(UserDetail $detail): void
    {
        $this->aman(function () use ($detail) {
            $data = array_diff_key($detail->getAttributes(), array_flip(self::ABAIKAN));

            AuditLog::catat([
                'jenis'       => 'keamanan',
                'aksi'        => 'hapus_detail_user',
                'target_tipe' => 'User',
                'target_id'   => $detail->user_id,
                'deskripsi'   => "Detail user #{$detail->user_id} dihapus",
                'data_lama'   => $data,
            ]);
        });
    }

    private function catatFoto(UserDetail $detail, ?string $lama, ?string $baru): void
    {
        AuditLog::catat([
            'jenis'       => 'keamanan',
            'aksi'        => 'foto_profil_berubah',
            'target_tipe' => 'User',
            'target_id'   => $detail->user_id,
            'deskripsi'   => "Foto profil user #{$detail->user_id} berubah",
            'data_lama'   => ['foto' => AuditLog::ringkasFoto($lama)],
            'data_baru'   => ['foto' => AuditLog::ringkasFoto($baru)],
        ]);
    }

    private function aman(callable $fn): void
    {
        try {
            $fn();
        } catch (Throwable $e) {
            Log::error('Audit observer UserDetail gagal: ' . $e->getMessage());
        }
    }
}
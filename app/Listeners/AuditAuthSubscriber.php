<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class AuditAuthSubscriber
{
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class  => 'handleLogin',
            Failed::class => 'handleFailed',
            Logout::class => 'handleLogout',
        ];
    }

    public function handleLogin(Login $event): void
    {
        $request = request();

        $cara = 'lainnya';
        if ($request->routeIs('loginprocess')) {
            $cara = 'form';
        } elseif ($request->is('auth/google/callback')) {
            $cara = 'google';
        }

        $id = $event->user->getAuthIdentifier();

        AuditLog::catat([
            'jenis'       => 'keamanan',
            'aksi'        => 'login_berhasil',
            'user_id'     => $id,
            'user_email'  => $event->user->email ?? null,
            'target_tipe' => 'User',
            'target_id'   => $id,
            'deskripsi'   => "Login berhasil ({$cara})",
            'data_baru'   => ['cara' => $cara],
        ]);
    }

    public function handleFailed(Failed $event): void
    {
        // Hanya email yang dicatat. Password di $event->credentials TIDAK disentuh.
        $email = $event->credentials['email'] ?? null;

        AuditLog::catat([
            'jenis'       => 'keamanan',
            'aksi'        => 'login_gagal',
            'user_id'     => null, // pelaku belum terbukti; jangan ambil dari sesi
            'user_email'  => is_string($email) ? $email : null,
            'deskripsi'   => 'Login gagal (form)',
            'data_baru'   => [
                'email_terdaftar' => $event->user !== null,
            ],
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        $user = $event->user;
        $id = $user?->getAuthIdentifier();

        AuditLog::catat([
            'jenis'       => 'keamanan',
            'aksi'        => 'logout',
            'user_id'     => $id,
            'user_email'  => $user->email ?? null,
            'target_tipe' => $user ? 'User' : null,
            'target_id'   => $id,
            'deskripsi'   => 'Logout',
        ]);
    }
}
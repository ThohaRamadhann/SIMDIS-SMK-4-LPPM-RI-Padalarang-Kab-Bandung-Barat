<?php

namespace App\Listeners;

use App\Events\NotifikasiBaru;
use Pusher\PushNotifications\PushNotifications;
use Illuminate\Support\Facades\Log;

class SendBeamsPushNotification
{
    public function handle(NotifikasiBaru $event): void
    {
        $notif = $event->notifikasi;

        try {
            $beamsClient = new PushNotifications([
                "instanceId" => config('services.pusher_beams.instance_id'),
                "secretKey"  => config('services.pusher_beams.secret_key'),
            ]);

            // Target user spesifik via interest 'user-{id_pengguna}'
            // (sesuai yang kita subscribe di frontend)
            $interest = 'user-' . $notif->id_pengguna;

            $title = $notif->isi_pesan && str_contains($notif->isi_pesan, 'PEMANGGILAN')
                ? '🚨 Pemanggilan Siswa'
                : '⚠️ Peringatan Pelanggaran';

            $beamsClient->publishToInterests([$interest], [
                "web" => [
                    "notification" => [
                        "title" => $title,
                        "body"  => $notif->isi_pesan,
                        "icon"  => url('/images/logo_simdis.png'),
                        "deep_link" => url('/pelanggaran'),
                    ],
                    "data" => [
                        "url" => url('/pelanggaran'),
                    ],
                ],
                "fcm" => [
                    "notification" => [
                        "title" => $title,
                        "body"  => $notif->isi_pesan,
                    ],
                    "android" => [
                        "priority" => "high",
                        "notification" => [
                            "channel_id" => "simdis_notif",
                            "priority" => "max",
                            "default_vibrate_timings" => true,
                            "default_sound" => true,
                        ],
                    ],
                ],
                "apns" => [
                    "aps" => [
                        "alert" => [
                            "title" => $title,
                            "body"  => $notif->isi_pesan,
                        ],
                        "sound" => "default",
                        "content-available" => 1,
                    ],
                ],
            ]);

        } catch (\Throwable $e) {
            // Jangan sampai gagal kirim push bikin proses utama error
            Log::error('Gagal kirim Beams push notification: ' . $e->getMessage());
    }
    }
}

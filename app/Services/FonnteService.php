<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private string $token;
    private string $baseUrl = 'https://api.fonnte.com';

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Kirim pesan WhatsApp ke satu nomor
     *
     * @param string $noTelpon  Nomor HP penerima (contoh: 08123456789 atau 628123456789)
     * @param string $pesan     Isi pesan
     * @return bool
     */
    public function kirim(string $noTelpon, string $pesan): bool
    {
        // Normalisasi nomor — Fonnte butuh format 628xxx
        $nomor = $this->normalisiNomor($noTelpon);

        if (! $nomor) {
            Log::warning('FonnteService: nomor tidak valid', ['noTelpon' => $noTelpon]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post("{$this->baseUrl}/send", [
                'target'  => $nomor,
                'message' => $pesan,
            ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                Log::info('FonnteService: pesan terkirim', [
                    'nomor' => $nomor,
                    'response' => $body,
                ]);
                return true;
            }

            Log::warning('FonnteService: gagal kirim', [
                'nomor'    => $nomor,
                'response' => $body,
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('FonnteService: exception', [
                'nomor'   => $nomor,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Normalisasi nomor HP ke format internasional 628xxx
     */
    private function normalisiNomor(string $nomor): ?string
    {
        // Hapus spasi, strip, tanda plus
        $nomor = preg_replace('/[\s\-\+]/', '', $nomor);

        // Kosong atau terlalu pendek
        if (strlen($nomor) < 8) {
            return null;
        }

        // Sudah format 62xxx
        if (str_starts_with($nomor, '62')) {
            return $nomor;
        }

        // Format 08xxx → 628xxx
        if (str_starts_with($nomor, '0')) {
            return '62' . substr($nomor, 1);
        }

        // Format 8xxx (tanpa 0) → 628xxx
        if (str_starts_with($nomor, '8')) {
            return '62' . $nomor;
        }

        return $nomor;
    }
}
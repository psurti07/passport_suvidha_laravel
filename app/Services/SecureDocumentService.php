<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SecureDocumentService
{
    /**
     * Generate encrypted token for a document.
     */
    public function generateToken(
        string $type,
        int $id
    ): string {
        return Crypt::encryptString(
            json_encode([
                'type' => $type,
                'id' => $id,
            ])
        );
    }

    /**
     * Generate customer-facing document URL.
     */
    // public function generateUrl(
    //     string $type,
    //     int $id
    // ): string {
    //     $token = $this->generateToken($type, $id);

    //     Log::info("frontend_url: ", [config('app.frontend_url')]);

    //     return config('services.app.frontend_url')
    //         . '/api/application-files/'
    //         . urlencode($token);
    // }

    public function generateUrl(string $type, int $id): string
    {
        $token = $this->generateToken($type, $id);
        $dynamicUrl = '/api/application-files/' . urlencode($token);
        if ($type === 'appointment_letters') {
            return $dynamicUrl;
        }
        return rtrim(config('services.app.frontend_url'), '/') . $dynamicUrl;
    }

    // public function generateUrl(string $type, int $id): string
    // {
    //     return  'api/application-files/' . urlencode($this->generateToken($type, $id));
    // }

    /**
     * Decode and validate token.
     */
    public function decryptToken(string $encryptedToken): ?array
    {
        try {
            $decrypted = Crypt::decryptString($encryptedToken);

            $payload = json_decode($decrypted, true);

            if (!is_array($payload)) {
                return null;
            }

            $type = $payload['type'] ?? null;
            $id = $payload['id'] ?? null;
            Log::info("Decrypt id: ", [$payload['id']]);

            if (
                !in_array($type, [
                    'final_details',
                    'appointment_letters',
                ], true)
            ) {
                return null;
            }

            if (!ctype_digit((string) $id)) {
                return null;
            }

            return [
                'type' => $type,
                'id' => (int) $id,
            ];
        } catch (\Throwable $e) {

            Log::warning('Secure document token failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get file record based on token payload.
     */
    public function getFileRecord(array $payload)
    {
        return match ($payload['type']) {

            'final_details' =>
            \App\Models\FinalDetail::find($payload['id']),

            'appointment_letters' =>
            \App\Models\AppointmentLetter::find($payload['id']),

            default => null,
        };
    }

    /**
     * Check whether physical document exists.
     */
    public function fileExists($fileRecord): bool
    {
        if (
            !$fileRecord ||
            empty($fileRecord->file_path)
        ) {
            return false;
        }

        return Storage::disk('public')
            ->exists($fileRecord->file_path);
    }
}

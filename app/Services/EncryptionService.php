<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EncryptionService
{
    private string $algorithm;

    private string $key;

    public function __construct()
    {
        $this->algorithm = 'AES-256-CBC';
        $this->key = config('app.encryption_key') ?? env('APP_KEY');
    }

    public function encrypt(mixed $data): string
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }

        try {
            $encrypted = Crypt::encrypt($data);
            Log::debug('Data encrypted successfully');

            return $encrypted;
        } catch (\Exception $e) {
            Log::error('Encryption failed: '.$e->getMessage());
            throw $e;
        }
    }

    public function decrypt(string $encrypted, bool $asArray = false): mixed
    {
        try {
            $decrypted = Crypt::decrypt($encrypted);

            if ($asArray || str_starts_with($decrypted, '{') || str_starts_with($decrypted, '[')) {
                return json_decode($decrypted, true);
            }

            return $decrypted;
        } catch (\Exception $e) {
            Log::error('Decryption failed: '.$e->getMessage());
            throw $e;
        }
    }

    public function encryptField(string $value, string $fieldName): string
    {
        try {
            $encrypted = $this->encrypt($value);
            Log::info("Field '{$fieldName}' encrypted", ['field' => $fieldName]);

            return $encrypted;
        } catch (\Exception $e) {
            Log::warning("Failed to encrypt field '{$fieldName}': ".$e->getMessage());

            return $value;
        }
    }

    public function decryptField(string $encrypted, string $fieldName): string
    {
        try {
            return $this->decrypt($encrypted);
        } catch (\Exception $e) {
            Log::warning("Failed to decrypt field '{$fieldName}': ".$e->getMessage());

            return $encrypted;
        }
    }

    public function hashData(string $data): string
    {
        return hash('sha256', $data);
    }

    public function verifyHash(string $data, string $hash): bool
    {
        return hash_equals($this->hashData($data), $hash);
    }

    public function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    public function maskSensitiveData(string $data, int $visibleChars = 4): string
    {
        if (strlen($data) <= $visibleChars * 2) {
            return str_repeat('*', strlen($data));
        }

        $visibleStart = substr($data, 0, $visibleChars);
        $visibleEnd = substr($data, -$visibleChars);
        $masked = str_repeat('*', strlen($data) - ($visibleChars * 2));

        return $visibleStart.$masked.$visibleEnd;
    }

    public function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $this->maskSensitiveData($email);
        }

        $username = $parts[0];
        $domain = $parts[1];

        if (strlen($username) <= 2) {
            return '**@'.$domain;
        }

        return substr($username, 0, 2).str_repeat('*', strlen($username) - 2).'@'.$domain;
    }

    public function maskPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) <= 4) {
            return str_repeat('*', strlen($phone));
        }

        return str_repeat('*', strlen($phone) - 4).substr($phone, -4);
    }

    public function anonymize(string $data, string $type = 'text'): string
    {
        return match ($type) {
            'email' => $this->maskEmail($data),
            'phone' => $this->maskPhone($data),
            'name' => $this->anonymizeName($data),
            default => $this->maskSensitiveData($data),
        };
    }

    private function anonymizeName(string $name): string
    {
        $parts = explode(' ', $name);
        $anonymized = array_map(fn ($part) => str_repeat('*', strlen($part)), $parts);

        return implode(' ', $anonymized);
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;

class NikCrypto
{
    public static function hashNik(string $nik): string
    {
        $pepper = config('security.nik_pepper');

        return hash('sha256', $pepper . '|' . trim($nik));
    }

    public static function encryptNik(string $nik): string
    {
        return Crypt::encryptString(trim($nik));
    }

    public static function decryptNik(?string $cipherText): ?string
    {
        if (! $cipherText) {
            return null;
        }

        return Crypt::decryptString($cipherText);
    }
}

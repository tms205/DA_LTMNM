<?php

namespace App\Services;

class GoogleAuthenticatorService
{
    /**
     * Base32 alphabet according to RFC 4648.
     */
    protected const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random Base32 secret key.
     */
    public function generateSecret(int $length = 16): string
    {
        $secret = '';
        $base32Chars = self::BASE32_CHARS;
        $maxIndex = strlen($base32Chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $secret .= $base32Chars[random_int(0, $maxIndex)];
        }

        return $secret;
    }

    /**
     * Calculate the 6-digit TOTP code for the given secret at a specific time slice.
     */
    public function getCode(string $secret, ?int $timeSlice = null): string
    {
        if ($timeSlice === null) {
            $timeSlice = (int) floor(time() / 30);
        }

        $secretKey = $this->base32Decode($secret);

        // Pack time slice into 64-bit big-endian binary string
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);

        // Calculate HMAC-SHA1
        $hmac = hash_hmac('sha1', $time, $secretKey, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $truncatedHash = substr($hmac, $offset, 4);

        // Unpack into 32-bit unsigned integer
        $value = unpack('N', $truncatedHash)[1];
        $value = $value & 0x7FFFFFFF;

        $modulo = 10 ** 6;
        $code = $value % $modulo;

        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify user submitted code against expected code within a tolerance window.
     */
    public function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $cleanCode = trim($code);
        if (strlen($cleanCode) !== 6 || ! ctype_digit($cleanCode)) {
            return false;
        }

        $currentTimeSlice = (int) floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $cleanCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate standard otpauth:// URL for Google Authenticator app.
     */
    public function getOtpAuthUrl(string $name, string $secret, string $issuer = 'Thư viện HUIT'): string
    {
        return 'otpauth://totp/'.rawurlencode($issuer).':'.rawurlencode($name)
            .'?secret='.rawurlencode($secret)
            .'&issuer='.rawurlencode($issuer)
            .'&algorithm=SHA1&digits=6&period=30';
    }

    /**
     * Generate a QR Code image URL via secure QR service.
     */
    public function getQrCodeUrl(string $name, string $secret, string $issuer = 'Thư viện HUIT'): string
    {
        $otpAuthUrl = $this->getOtpAuthUrl($name, $secret, $issuer);

        return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data='.urlencode($otpAuthUrl);
    }

    /**
     * Helper to decode Base32 string to binary.
     */
    protected function base32Decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        $buffer = 0;
        $bufferBits = 0;
        $binary = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            if ($char === '=' || $char === ' ' || $char === '-') {
                continue;
            }

            $pos = strpos(self::BASE32_CHARS, $char);
            if ($pos === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $pos;
            $bufferBits += 5;

            if ($bufferBits >= 8) {
                $bufferBits -= 8;
                $binary .= chr(($buffer >> $bufferBits) & 0xFF);
            }
        }

        return $binary;
    }
}

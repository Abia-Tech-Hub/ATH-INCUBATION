<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * RFC 6238 time-based one-time passwords (Google Authenticator, 1Password, Authy).
 * Ported from lib/totp.ts; the tests check the RFC 4226 vectors.
 */
class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    private const STEP = 30;

    private const DIGITS = 6;

    public static function newSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    public static function base32Encode(string $binary): string
    {
        $bits = '';
        foreach (str_split($binary) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }
        $bits = str_pad($bits, (int) ceil(strlen($bits) / 5) * 5, '0', STR_PAD_RIGHT);

        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            if ($chunk === '') {
                continue;
            }
            $out .= self::ALPHABET[bindec($chunk)];
        }

        return $out;
    }

    public static function base32Decode(string $text): string
    {
        $clean = strtoupper(preg_replace('/[\s=]+/', '', $text) ?? '');
        $bits = '';
        foreach (str_split($clean) as $char) {
            $index = strpos(self::ALPHABET, $char);
            if ($index === false) {
                throw new InvalidArgumentException('Invalid base32.');
            }
            $bits .= str_pad(decbin($index), 5, '0', STR_PAD_LEFT);
        }

        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $out .= chr(bindec($byte));
            }
        }

        return $out;
    }

    public static function hotp(string $secretBinary, int $counter): string
    {
        $hash = hash_hmac('sha1', pack('J', $counter), $secretBinary, true);
        $offset = ord($hash[19]) & 0x0F;
        $code = ((ord($hash[$offset]) & 0x7F) << 24)
            | (ord($hash[$offset + 1]) << 16)
            | (ord($hash[$offset + 2]) << 8)
            | ord($hash[$offset + 3]);

        return str_pad((string) ($code % (10 ** self::DIGITS)), self::DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * Checks a code against the current 30-second step and one step either side.
     * Returns the matched step so callers can refuse a replayed code, or null.
     */
    public static function verify(string $secretBase32, string $code, int $lastUsedStep = 0, ?int $now = null): ?int
    {
        if (! preg_match('/^\d{6}$/', $code)) {
            return null;
        }

        $secret = self::base32Decode($secretBase32);
        $step = intdiv($now ?? time(), self::STEP);

        foreach ([$step - 1, $step, $step + 1] as $candidate) {
            if ($candidate > $lastUsedStep && hash_equals(self::hotp($secret, $candidate), $code)) {
                return $candidate;
            }
        }

        return null;
    }

    public static function otpauthUri(string $secretBase32, string $account, string $issuer): string
    {
        $label = rawurlencode($issuer.':'.$account);

        return 'otpauth://totp/'.$label.'?secret='.$secretBase32
            .'&issuer='.rawurlencode($issuer)
            .'&algorithm=SHA1&digits='.self::DIGITS.'&period='.self::STEP;
    }
}

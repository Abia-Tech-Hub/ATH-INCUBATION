<?php

namespace App\Support;

/**
 * Server-side data masking: staff see enough to do the job, never full contact details.
 * Masking happens before data leaves the server, replacing the old Postgres RLS + forRole() pair.
 */
class Masking
{
    public static function email(string $email): string
    {
        if (! str_contains($email, '@')) {
            return '•••';
        }

        [$user, $domain] = explode('@', $email, 2);
        $bullets = str_repeat('•', max(2, min(6, mb_strlen($user) - 1)));

        return mb_substr($user, 0, 1).$bullets.'@'.$domain;
    }

    public static function phone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        return strlen($digits) < 4 ? '•••' : '••• ••• '.substr($digits, -3);
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function forRole(string $role, array $row): array
    {
        if (in_array($role, ['OWNER', 'ADMIN'], true)) {
            return $row;
        }
        if (array_key_exists('email', $row)) {
            $row['email'] = self::email((string) $row['email']);
        }
        if (array_key_exists('phone', $row) && $row['phone'] !== null) {
            $row['phone'] = self::phone((string) $row['phone']);
        }

        return $row;
    }
}

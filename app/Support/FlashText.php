<?php

namespace App\Support;

/** Every status a redirect can carry. Only these fixed strings are shown; the URL can't inject text. */
class FlashText
{
    public const ERRORS = [
        'invalid' => "Something in the form wasn't right. Check it and try again.",
        'rate_limited' => 'Too many attempts. Please wait a few minutes and try again.',
        'server' => 'Something went wrong on our side. Please try again.',
        'credentials' => "That email and password don't match.",
        'code' => "That code didn't work. Check your authenticator app and try again.",
        'expired' => 'That link has expired or was already used.',
        'use_password' => 'Team accounts sign in with a password and two-factor code.',
        'exists' => 'That person is already on the team.',
        'self' => "You can't remove yourself.",
        'not_found' => "That doesn't exist any more.",
        'forbidden' => "You don't have access to that.",
        'signin' => 'Please sign in.',
        'mfa' => 'Finish two-factor sign-in first.',
    ];

    public const SENT = [
        'message' => "Thanks, your message is in. We'll reply by email.",
        'link' => 'If that address can sign in, a link is on its way. It works once and expires in 15 minutes.',
        'application' => "Application received. We'll be in touch by email.",
    ];

    public const DONE = [
        'mfa' => 'Two-factor sign-in is on.',
        'invited' => 'Invite sent.',
        'removed' => 'Removed from the team.',
        'updated' => 'Application updated.',
        'password' => 'Your password is set.',
    ];

    public static function error(?string $key): ?string
    {
        return $key !== null ? (self::ERRORS[$key] ?? null) : null;
    }

    public static function success(?string $sent, ?string $done): ?string
    {
        return ($sent !== null ? (self::SENT[$sent] ?? null) : null)
            ?? ($done !== null ? (self::DONE[$done] ?? null) : null);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin-area gate: signed in, not a customer, second factor done (or being set up), and at least
 * the required role. Usage: staff, staff:OWNER, staff:STAFF,1 (the last parameter allows 2FA setup).
 */
class EnsureStaff
{
    public function handle(Request $request, Closure $next, string $min = 'STAFF', string $allowSetup = '0'): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isStaff()) {
            return redirect()->route('admin.login')->with('error', 'signin');
        }

        if ($user->totp_enabled && ! $request->session()->get('mfa_passed')) {
            return redirect()->route('admin.mfa');
        }

        if (! $user->totp_enabled && $user->requiresMfa() && ! (bool) $allowSetup) {
            return redirect()->route('admin.security');
        }

        if (! $user->atLeast($min)) {
            return redirect()->route('admin.dashboard')->with('error', 'forbidden');
        }

        return $next($request);
    }
}

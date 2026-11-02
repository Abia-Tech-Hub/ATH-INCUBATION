<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Audit;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /** A real bcrypt hash, checked when the email doesn't exist, so timing can't reveal accounts. */
    private const DUMMY_HASH = '$2y$12$94JrsAXrJphPi0kQsdSLmOM4Xgk2zErThnpFgjUW.jaRxN3GB6uqy';

    public function showLogin(Request $request)
    {
        if ($user = $request->user()) {
            if ($user->isStaff()) {
                return redirect($this->afterLoginTarget($user, $request));
            }
            Auth::logout();
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required', 'string', 'max:128'],
        ]);

        $email = strtolower($data['email']);
        $throttleKey = 'admin-login:'.$email;

        if (RateLimiter::tooManyAttempts($throttleKey, 8)) {
            return back()->with('error', 'rate_limited');
        }
        RateLimiter::hit($throttleKey, 900);

        $user = User::where('email', $email)->first();
        $candidate = $user && $user->role !== 'CUSTOMER' ? $user : null;

        $ok = $candidate && $candidate->password
            ? Hash::check($data['password'], $candidate->password)
            : Hash::check($data['password'], self::DUMMY_HASH);

        if (! $candidate || ! $ok) {
            Audit::log($user?->id, 'admin.login_failed', $email);

            return back()->with('error', 'credentials');
        }

        RateLimiter::clear($throttleKey);
        Auth::login($candidate);
        $request->session()->regenerate();
        $request->session()->put('mfa_passed', false);
        Audit::log($candidate->id, 'admin.password_ok');

        return redirect($this->afterLoginTarget($candidate, $request));
    }

    public function showMfa(Request $request)
    {
        $user = $request->user();

        if (! $user || ! $user->isStaff() || ! $user->totp_enabled) {
            return redirect()->route('admin.dashboard');
        }
        if ($request->session()->get('mfa_passed')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.mfa');
    }

    public function verifyMfa(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->isStaff() || ! $user->totp_enabled || ! $user->totp_secret) {
            return redirect()->route('admin.login');
        }

        $request->validate(['code' => ['required', 'regex:/^\d{6}$/']]);

        $step = Totp::verify($user->totp_secret, (string) $request->input('code'), $user->totp_last_step);

        if ($step === null) {
            Audit::log($user->id, 'admin.mfa_failed');

            return back()->with('error', 'code');
        }

        // record the step so the same code can't be replayed within its 30-second window
        $user->forceFill(['totp_last_step' => $step])->save();

        $request->session()->put('mfa_passed', true);
        $request->session()->regenerate();
        Audit::log($user->id, 'admin.signed_in');

        return redirect()->route('admin.dashboard');
    }

    private function afterLoginTarget(User $user, Request $request): string
    {
        if ($user->totp_enabled && ! $request->session()->get('mfa_passed')) {
            return route('admin.mfa');
        }
        if (! $user->totp_enabled && $user->requiresMfa()) {
            return route('admin.security');
        }

        return route('admin.dashboard');
    }
}

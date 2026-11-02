<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Audit;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $pending = ! $user->totp_enabled ? $user->totp_pending_secret : null;

        return view('admin.security', [
            'user' => $user,
            'pending' => $pending,
            'otpauth' => $pending ? Totp::otpauthUri($pending, $user->email, config('app.name')) : null,
        ]);
    }

    /** Starts two-factor setup: a new secret is stored encrypted as "pending" until a code proves it works. */
    public function start(Request $request)
    {
        $user = $request->user();

        if ($user->totp_enabled) {
            return redirect()->route('admin.security');
        }

        $user->forceFill(['totp_pending_secret' => Totp::newSecret()])->save();

        return redirect()->route('admin.security');
    }

    public function confirm(Request $request)
    {
        $user = $request->user();

        if ($user->totp_enabled || ! $user->totp_pending_secret) {
            return redirect()->route('admin.security');
        }

        $request->validate(['code' => ['required', 'regex:/^\d{6}$/']]);

        $secret = $user->totp_pending_secret;
        $step = Totp::verify($secret, (string) $request->input('code'), 0);

        if ($step === null) {
            return redirect()->route('admin.security')->with('error', 'code');
        }

        $user->forceFill([
            'totp_secret' => $secret,
            'totp_pending_secret' => null,
            'totp_enabled' => true,
            'totp_last_step' => $step,
        ])->save();

        // other sessions for this account predate 2FA, so they're signed out
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        $request->session()->put('mfa_passed', true);
        Audit::log($user->id, 'admin.mfa_enabled');

        return redirect()->route('admin.dashboard')->with('done', 'mfa');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifyController extends Controller
{
    /**
     * Opening the emailed link only shows a page; signing in takes a deliberate POST,
     * so link-preview bots and mail scanners can't use up someone's link.
     */
    public function show(Request $request)
    {
        $token = $this->validToken($request->query('token'));

        return view('auth.verify', ['token' => $token]);
    }

    public function store(Request $request)
    {
        $request->validate(['token' => ['required', 'regex:/^[A-Za-z0-9]{30,100}$/']]);

        $row = DB::transaction(function () use ($request) {
            $row = LoginToken::where('token_hash', hash('sha256', (string) $request->input('token')))
                ->where('purpose', 'signin')
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $row) {
                return null;
            }

            $row->forceFill(['used_at' => now()])->save();

            return $row;
        });

        if (! $row) {
            return redirect()->route('auth.verify')->with('error', 'expired');
        }

        $user = User::firstOrCreate(['email' => $row->email], ['role' => 'CUSTOMER']);

        // team accounts sign in with a password and two-factor code, never by email link
        if ($user->role !== 'CUSTOMER') {
            return redirect()->route('admin.login')->with('error', 'use_password');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account');
    }

    private function validToken(mixed $token): string
    {
        return is_string($token) && preg_match('/^[A-Za-z0-9]{30,100}$/', $token) ? $token : '';
    }
}

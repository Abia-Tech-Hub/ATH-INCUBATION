<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    public function show(Request $request): View
    {
        $token = $request->query('token');
        $valid = is_string($token) && preg_match('/^[A-Za-z0-9]{30,100}$/', $token) ? $token : '';

        return view('admin.setup', ['token' => $valid]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'regex:/^[A-Za-z0-9]{30,100}$/'],
            'password' => ['required', 'string', 'min:12', 'max:128'],
        ]);

        $user = DB::transaction(function () use ($data): ?User {
            $row = LoginToken::where('token_hash', hash('sha256', $data['token']))
                ->where('purpose', 'invite')
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $row) {
                return null;
            }

            $row->forceFill(['used_at' => now()])->save();

            $user = User::where('email', $row->email)->lockForUpdate()->first();
            if (! $user) {
                return null;
            }

            if ($user->role === 'CUSTOMER') {
                if (! in_array($user->pending_role, ['ADMIN', 'STAFF'], true)) {
                    return null;
                }

                $user->forceFill(['role' => $user->pending_role, 'pending_role' => null])->save();
            }

            DB::table('sessions')->where('user_id', $user->id)->delete();
            $user->forceFill(['password' => Hash::make($data['password'])])->save();

            return $user;
        });

        if (! $user) {
            return redirect()->route('admin.setup')->with('error', 'expired');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('mfa_passed', false);
        Audit::log($user->id, 'team.password_set');

        if ($user->totp_enabled) {
            return redirect()->route('admin.mfa');
        }

        return redirect($user->requiresMfa() ? route('admin.security') : route('admin.dashboard'));
    }
}

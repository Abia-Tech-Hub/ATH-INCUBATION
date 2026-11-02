<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $team = User::where('role', '!=', 'CUSTOMER')->orderBy('created_at')->get();

        return view('admin.team', ['user' => $request->user(), 'team' => $team]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'role' => ['required', 'in:ADMIN,STAFF'],
        ]);
        $email = strtolower($data['email']);
        $token = Str::random(64);

        $created = DB::transaction(function () use ($email, $data, $token): bool {
            $existing = User::where('email', $email)->lockForUpdate()->first();
            if ($existing && $existing->role !== 'CUSTOMER') {
                return false;
            }
            if ($existing) {
                $existing->forceFill(['pending_role' => $data['role']])->save();
            } else {
                User::create(['email' => $email, 'role' => $data['role']]);
            }

            LoginToken::where('purpose', 'invite')
                ->where('email', $email)
                ->whereNull('used_at')
                ->update(['used_at' => now()]);

            LoginToken::create([
                'token_hash' => hash('sha256', $token),
                'purpose' => 'invite',
                'email' => $email,
                'expires_at' => now()->addHours(48),
            ]);

            return true;
        });

        if (! $created) {
            return redirect()->route('admin.team')->with('error', 'exists');
        }

        try {
            Mail::raw(
                "You've been added to the ".config('app.name').' team as '.strtolower($data['role']).".\n\n"
                ."Choose a password to get started. The link expires in 48 hours.\n\n"
                .route('admin.setup', ['token' => $token]),
                fn ($message) => $message->to($email)->subject("You're invited to ".config('app.name')),
            );
        } catch (\Throwable $e) {
            report($e);
        }

        Audit::log($request->user()->id, 'team.invited', $email.' as '.$data['role']);

        return redirect()->route('admin.team')->with('done', 'invited');
    }

    public function remove(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        if ($user->id === $actor->id) {
            return redirect()->route('admin.team')->with('error', 'self');
        }
        // owners can't be removed from here, and customers aren't team members
        if ($user->role === 'OWNER' || $user->role === 'CUSTOMER') {
            return redirect()->route('admin.team')->with('error', 'not_found');
        }

        $user->forceFill([
            'role' => 'CUSTOMER',
            'password' => null,
            'totp_enabled' => false,
            'totp_secret' => null,
            'totp_pending_secret' => null,
            'pending_role' => null,
        ])->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();
        Audit::log($actor->id, 'team.removed', $user->email);

        return redirect()->route('admin.team')->with('done', 'removed');
    }
}

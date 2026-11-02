<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MagicLinkController extends Controller
{
    /** Customer sign-in by emailed link. The reply never says whether an account exists. */
    public function store(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:254']]);
        $email = strtolower($data['email']);

        $token = Str::random(64);
        LoginToken::create([
            'token_hash' => hash('sha256', $token),
            'purpose' => 'signin',
            'email' => $email,
            'expires_at' => now()->addMinutes(15),
        ]);

        $url = route('auth.verify', ['token' => $token]);

        try {
            Mail::raw(
                "Here's your link to sign in. It works once and expires in 15 minutes.\n\n"
                ."If you didn't ask for it, ignore this email.\n\n".$url,
                fn ($message) => $message->to($email)->subject('Your sign-in link · '.config('app.name')),
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('account')->with('sent', 'link');
    }
}

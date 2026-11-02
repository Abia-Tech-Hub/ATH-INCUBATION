@extends('layouts.admin')

@section('title', 'Security · '.config('app.name'))

@section('content')
    @include('partials.flash')
    @if ($user->totp_enabled || ! $user->requiresMfa())
        @include('partials.admin-nav', ['user' => $user])
    @endif
    <main class="adm-main">
        <h1>Security</h1>
        @if ($user->totp_enabled)
            <p>Two-factor sign-in is on for {{ $user->email }}.</p>
        @elseif ($pending)
            <section class="adm-auth" style="margin: 0">
                <h2>Add {{ config('app.name') }} to your authenticator app</h2>
                <p class="adm-note">In 1Password, Google Authenticator or Authy, add an account by setup key and enter this key:</p>
                <p class="adm-code">{{ implode(' ', str_split($pending, 4)) }}</p>
                <p class="adm-note">
                    Or open this setup link on the device with your authenticator:
                    <a href="{{ $otpauth }}">add to authenticator</a>
                </p>
                <form method="post" action="{{ route('admin.security.confirm') }}">
                    @csrf
                    <label>
                        Then enter the 6-digit code it shows
                        <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
                    </label>
                    <button class="btn" type="submit">Turn on two-factor sign-in</button>
                </form>
            </section>
        @else
            <section>
                <p>{{ $user->requiresMfa() ? 'Your role needs two-factor sign-in before you can use the admin area.' : 'Protect your account with a code from an authenticator app.' }}</p>
                <form method="post" action="{{ route('admin.security.start') }}">
                    @csrf
                    <button class="btn" type="submit">Set up two-factor sign-in</button>
                </form>
            </section>
        @endif
    </main>
@endsection

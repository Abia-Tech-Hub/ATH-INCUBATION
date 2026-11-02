@extends('layouts.admin')

@section('content')
    <main class="adm-auth">
        @include('partials.flash')
        <h1>Two-factor sign-in</h1>
        <p class="adm-note">Enter the 6-digit code from your authenticator app.</p>
        <form method="post" action="{{ route('admin.mfa.verify') }}">
            @csrf
            <label>
                Code
                <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus>
            </label>
            <button class="btn" type="submit">Verify</button>
        </form>
    </main>
@endsection

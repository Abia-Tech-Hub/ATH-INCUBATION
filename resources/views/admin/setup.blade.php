@extends('layouts.admin')

@section('content')
    <main class="adm-auth">
        @include('partials.flash')
        <h1>Join {{ config('app.name') }}</h1>
        @if ($token)
            <form method="post" action="{{ route('admin.setup.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <label>
                    Choose a password
                    <input type="password" name="password" autocomplete="new-password" minlength="12" maxlength="128" required>
                </label>
                <p class="adm-note">At least 12 characters. A passphrase of a few unrelated words works well.</p>
                <button class="btn" type="submit">Set password</button>
            </form>
        @else
            <p>This invite link isn&apos;t valid. Ask the owner to send a new one.</p>
        @endif
    </main>
@endsection

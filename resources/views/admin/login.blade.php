@extends('layouts.admin')

@section('content')
    <main class="adm-auth">
        @include('partials.flash')
        <h1>{{ config('app.name') }} admin</h1>
        <form method="post" action="{{ route('admin.login.store') }}">
            @csrf
            <label>
                Email
                <input type="email" name="email" autocomplete="username" required maxlength="254" value="{{ old('email') }}">
            </label>
            <label>
                Password
                <input type="password" name="password" autocomplete="current-password" required maxlength="128">
            </label>
            <button class="btn" type="submit">Sign in</button>
        </form>
        <p class="adm-note">Owners and admins confirm every sign-in with a code from their authenticator app.</p>
    </main>
@endsection

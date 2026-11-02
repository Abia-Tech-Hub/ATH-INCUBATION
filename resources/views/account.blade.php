@extends('layouts.site')

@section('title', 'Your account · '.config('app.name'))

@section('content')
    <main class="ac-narrow">
        <h1>Your account</h1>
        @include('partials.flash')

        @if ($user)
            <p>Signed in as {{ $user->email }}.</p>
            <h2>Your applications</h2>
            @if ($applications->isNotEmpty())
                <ul class="ac-rows">
                    @foreach ($applications as $application)
                        <li>
                            <b>{{ $application->startup_name }}</b>
                            <span>{{ $application->created_at->format('j M Y') }}</span>
                            <em>{{ $application->status }}</em>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No applications yet. <a href="{{ route('apply') }}">Apply to the programme</a>.</p>
            @endif
            @if ($user->isStaff())
                <p><a class="btn btn-forest" href="{{ route('admin.dashboard') }}">Open the admin area</a></p>
            @endif
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn" type="submit">Sign out</button>
            </form>
        @else
            <p>Enter your email and we&apos;ll send you a link to sign in. It works once and expires in 15 minutes.</p>
            <form method="post" action="{{ route('account.link') }}" class="f">
                @csrf
                <label>
                    Email
                    <input class="input" type="email" name="email" autocomplete="email" required maxlength="254">
                </label>
                <button class="btn" type="submit">Email me a sign-in link</button>
            </form>
        @endif
    </main>
@endsection

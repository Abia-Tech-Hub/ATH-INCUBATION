@extends('layouts.site')

@section('title', 'Sign in · '.config('app.name'))

@section('content')
    <main class="ac-narrow">
        <h1>Sign in to {{ config('app.name') }}</h1>
        @include('partials.flash')
        @if ($token)
            <form method="post" action="{{ route('auth.verify.store') }}" class="f">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <button class="btn" type="submit">Continue</button>
            </form>
        @else
            <p>This link isn&apos;t complete. <a href="{{ route('account') }}">Request a new one</a>.</p>
        @endif
    </main>
@endsection

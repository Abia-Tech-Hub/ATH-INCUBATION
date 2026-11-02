<header class="adm-top">
    <b>{{ config('app.name') }}</b>
    <nav aria-label="Admin">
        <a href="{{ route('admin.dashboard') }}" aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : '' }}">Overview</a>
        @if ($user->role === 'OWNER')
            <a href="{{ route('admin.team') }}" aria-current="{{ request()->routeIs('admin.team') ? 'page' : '' }}">Team</a>
            <a href="{{ route('admin.audit') }}" aria-current="{{ request()->routeIs('admin.audit') ? 'page' : '' }}">Audit log</a>
        @endif
        <a href="{{ route('admin.security') }}" aria-current="{{ request()->routeIs('admin.security') ? 'page' : '' }}">Security</a>
    </nav>
    <form method="post" action="{{ route('logout') }}">
        @csrf
        <button class="btn-quiet" type="submit" title="{{ $user->email }} · {{ strtolower($user->role) }}">Sign out</button>
    </form>
</header>

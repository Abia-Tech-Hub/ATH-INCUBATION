@extends('layouts.admin')

@section('title', 'Audit log · '.config('app.name'))

@section('content')
    @include('partials.flash')
    @include('partials.admin-nav', ['user' => $user])
    <main class="adm-main">
        <h1>Audit log</h1>
        <table>
            <thead><tr><th>When (UTC)</th><th>Who</th><th>What</th><th>Detail</th></tr></thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->utc()->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $log->actor?->email ?? ($log->actor_id ? 'removed user' : '—') }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->target ?? '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="adm-note">Nothing recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </main>
@endsection

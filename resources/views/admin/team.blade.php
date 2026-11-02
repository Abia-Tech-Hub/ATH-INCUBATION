@extends('layouts.admin')

@section('title', 'Team · '.config('app.name'))

@section('content')
    @include('partials.flash')
    @include('partials.admin-nav', ['user' => $user])
    <main class="adm-main">
        <h1>Team</h1>
        <table>
            <thead><tr><th>Email</th><th>Role</th><th>Two-factor</th><th></th></tr></thead>
            <tbody>
                @foreach ($team as $member)
                    <tr>
                        <td>{{ $member->email }}@unless ($member->password)<span class="adm-note"> · invite pending</span>@endunless</td>
                        <td>{{ strtolower($member->role) }}</td>
                        <td>{{ $member->totp_enabled ? 'On' : 'Off' }}</td>
                        <td>
                            @if ($member->role !== 'OWNER' && $member->id !== $user->id)
                                <form method="post" action="{{ route('admin.team.remove', $member) }}">
                                    @csrf
                                    <button class="btn-quiet" type="submit">Remove</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <section aria-labelledby="inv-h">
            <h2 id="inv-h">Invite someone</h2>
            <form method="post" action="{{ route('admin.team.store') }}" style="display: flex; gap: 8px; flex-wrap: wrap">
                @csrf
                <input type="email" name="email" placeholder="name@example.com" aria-label="Email" required maxlength="254">
                <select name="role" aria-label="Role">
                    <option value="STAFF">Staff · sees applications, contact details hidden</option>
                    <option value="ADMIN">Admin · full details, can change applications</option>
                </select>
                <button class="btn" type="submit">Send invite</button>
            </form>
        </section>
    </main>
@endsection

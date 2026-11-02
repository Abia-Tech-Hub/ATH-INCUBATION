@extends('layouts.admin')

@section('title', 'Overview · '.config('app.name'))

@section('content')
    @include('partials.flash')
    @include('partials.admin-nav', ['user' => $user, 'current' => 'admin.dashboard'])
    <main class="adm-main">
        <h1>Overview</h1>
        <div class="adm-cards">
            <div class="adm-card"><span>Applications</span><b>{{ $applications->count() }}</b></div>
            <div class="adm-card"><span>Messages</span><b>{{ $messageCount }}</b></div>
            <div class="adm-card"><span>Bookings</span><b>{{ $bookings->count() }}</b></div>
            <div class="adm-card"><span>Subscribers</span><b>{{ $subscribers }}</b></div>
        </div>
        @if (! $user->canSeeContacts())
            <p class="adm-note">Contact details are partly hidden for your role.</p>
        @endif

        <section id="applications" aria-labelledby="apps-h">
            <h2 id="apps-h">Applications</h2>
            @if ($applications->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Received</th>
                            <th>Applicant</th>
                            <th>Startup</th>
                            <th>Application</th>
                            <th>Contact</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($application['created_at'])->format('Y-m-d H:i') }}</td>
                                <td>
                                    {{ $application['full_name'] }}<br>
                                    <span class="adm-note">{{ $application['institution'] }}</span><br>
                                    <span class="adm-note">{{ $application['course'] }} · {{ $application['year_of_study'] }}</span>
                                </td>
                                <td>
                                    <b>{{ $application['startup_name'] }}</b><br>
                                    <span class="adm-note">{{ $application['sector'] }}</span><br>
                                    <span class="adm-note">{{ $application['stage'] }} · {{ $application['team_size'] }}</span>
                                </td>
                                <td>
                                    <details>
                                        <summary>View answers</summary>
                                        <p><strong>Problem:</strong> {{ $application['problem'] }}</p>
                                        <p><strong>Solution:</strong> {{ $application['solution'] }}</p>
                                        <p><strong>Goal:</strong> {{ $application['goal'] }}</p>
                                        <p><strong>Mentorship:</strong> {{ implode(', ', $application['mentor_areas'] ?? []) }}</p>
                                        <p><strong>Availability:</strong> {{ $application['commitment'] }}</p>
                                        <p><strong>Heard about it:</strong> {{ $application['heard_about'] ?? 'Not provided' }}</p>
                                        <p><strong>Link:</strong> {{ $application['link'] ?? 'Not provided' }}</p>
                                    </details>
                                </td>
                                <td>{{ $application['email'] }}<br>{{ $application['phone'] }}</td>
                                <td>
                                    @if ($canEdit)
                                        <form method="post" action="{{ route('admin.applications.status', $application['id']) }}">
                                            @csrf
                                            <select name="status" aria-label="Status for {{ $application['full_name'] }}">
                                                @foreach (['new', 'reviewing', 'accepted', 'declined'] as $status)
                                                    <option value="{{ $status }}" @selected($application['status'] === $status)>{{ ucfirst($status) }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn-quiet" type="submit">Save</button>
                                        </form>
                                    @else
                                        {{ $application['status'] }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="adm-note">No applications yet.</p>
            @endif
        </section>

        <section aria-labelledby="bk-h">
            <h2 id="bk-h">Bookings</h2>
            @if ($bookings->isNotEmpty())
                <table>
                    <thead><tr><th>When</th><th>Service</th><th>Name</th><th>Contact</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td>{{ $booking->day }} {{ $booking->time }}</td>
                                <td>{{ $booking->service }}</td>
                                <td>{{ $booking->name }}</td>
                                <td>{{ $user->canSeeContacts() ? $booking->email : \App\Support\Masking::email($booking->email) }}<br>
                                    {{ $user->canSeeContacts() ? $booking->phone : \App\Support\Masking::phone($booking->phone) }}
                                </td>
                                <td>{{ $booking->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="adm-note">No bookings yet.</p>
            @endif
        </section>
    </main>
@endsection

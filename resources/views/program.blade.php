@extends('layouts.site', ['solid' => true])

@section('title', 'The programme · ' . config('app.name'))

@section('content')
    <section class="section program-intro" aria-labelledby="program-title">
        <div class="wrap">
            <h1 class="h2" id="program-title">Six weeks to move your idea forward</h1>
            <p class="lead">A focused sequence of practical training and mentorship to help you understand a problem, test an opportunity and plan what to build next.</p>
        </div>
    </section>
    @include('partials.benefits')
    @include('partials.how')
@endsection

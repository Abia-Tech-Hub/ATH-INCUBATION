@extends('layouts.site', ['solid' => true])

@section('title', 'The Programme · ' . config('app.name'))
@section('description', 'Explore the 6-week curriculum, milestones, mentor guidance, and Demo Day of the Abia Tech Hub Student Startup Incubation Programme.')

@section('content')
    <section class="section program-intro on-light" aria-labelledby="program-title">
        <div class="wrap">
            <div class="section-head text-center max-w-750 reveal">
                <p class="section-label">SIX-WEEK SPRINT</p>
                <h1 class="h2" id="program-title">A focused journey from raw idea to validated business.</h1>
                <p class="lead">A practical sequence of founder training, weekly mentorship office hours, and milestones designed to help student founders make tangible, evidence-backed progress.</p>
            </div>
        </div>
    </section>
    @include('partials.benefits')
    @include('partials.how')
    @include('partials.next-step')
@endsection

@extends('layouts.site', ['solid' => true])

@section('title', 'Frequently Asked Questions · ' . config('app.name'))
@section('description', 'Frequently asked questions about eligibility, schedule, curriculum, and application for the Abia Tech Hub Student Startup Incubation Programme.')

@section('content')
    @include('partials.faq')
    @include('partials.next-step')
@endsection

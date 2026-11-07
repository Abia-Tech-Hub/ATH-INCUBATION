@extends('layouts.site', ['solid' => true])

@section('title', 'Who can apply · ' . config('app.name'))
@section('description', 'Check eligibility criteria, supported Abia institutions, and startup stages for the Abia Tech Hub Student Startup Incubation Programme.')

@section('content')
    @include('partials.eligibility')
    @include('partials.next-step')
@endsection

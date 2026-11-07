@extends('layouts.site', ['solid' => true])

@section('title', 'Partners & Mentors · ' . config('app.name'))
@section('description', 'Partner with Abia Tech Hub to mentor, support, and invest in the next generation of student entrepreneurs in Abia State.')

@section('content')
    @include('partials.partners')
    @include('partials.next-step')
@endsection

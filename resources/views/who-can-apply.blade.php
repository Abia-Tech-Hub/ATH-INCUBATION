@extends('layouts.site', ['solid' => true])

@section('title', 'Who can apply · ' . config('app.name'))

@section('content')
    @include('partials.eligibility')
@endsection

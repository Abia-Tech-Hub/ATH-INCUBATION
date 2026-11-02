@extends('layouts.site', ['solid' => true])

@section('title', 'About · ' . config('app.name'))

@section('content')
    @include('partials.why')
@endsection

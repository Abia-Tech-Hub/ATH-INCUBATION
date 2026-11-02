@extends('layouts.site', ['solid' => true])

@section('title', 'Apply · ' . config('app.name'))

@section('content')
    @include('partials.apply')
@endsection

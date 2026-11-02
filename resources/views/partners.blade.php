@extends('layouts.site', ['solid' => true])

@section('title', 'Partners · ' . config('app.name'))

@section('content')
    @include('partials.partners')
@endsection

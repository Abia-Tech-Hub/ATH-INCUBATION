@extends('layouts.site', ['solid' => true])

@section('title', 'FAQs · ' . config('app.name'))

@section('content')
    @include('partials.faq')
@endsection

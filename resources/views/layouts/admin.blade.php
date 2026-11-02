<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin · '.config('app.name'))</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" sizes="256x256">
    <link rel="stylesheet" href="{{ asset('css/globals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/template.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="adm">
        @yield('content')
    </div>
</body>
</html>

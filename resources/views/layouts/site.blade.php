<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" sizes="256x256">
    <meta name="description" content="@yield('description', 'The Abia Tech Hub Student Startup Incubation Programme: 6 weeks of mentorship and training for student founders at any Abia institution. Learn how it works and apply online.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/globals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/template.css') }}">
</head>
<body>
    @include('partials.skip-link')
    @include('partials.header', ['solid' => $solid ?? false])
    <main id="main">
        @yield('content')
    </main>
    @include('partials.footer')
    @include('partials.sticky-apply')

    <script nonce="{{ request()->attributes->get('csp_nonce') }}">
        // The app's form code calls window.anticode.send(). Laravel receives the complete application payload as JSON.
        window.anticode = {
            content: {},
            live: true,
            send: async function (form, fields) {
                var entries = fields instanceof HTMLFormElement
                    ? Object.entries(Object.fromEntries(new FormData(fields)))
                    : Object.entries(fields || {});
                var body = {};
                entries.forEach(function (entry) {
                    var key = entry[0], value = entry[1];
                    if (Array.isArray(value)) body[key] = value;
                    else if (typeof value === "string" || typeof value === "number" || typeof value === "boolean") body[key] = String(value);
                });
                try {
                    var res = await fetch(@json(route('apply.store')), {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                        credentials: "same-origin",
                        body: JSON.stringify(body),
                    });
                    var data = await res.json().catch(function () { return {}; });
                    if (res.ok) return { ok: true, title: "Application sent", message: "Thanks, your application has reached the team." };
                    return { ok: false, title: "Not sent", message: data.message || "That didn't go through. Please try again." };
                } catch (e) {
                    return { ok: false, title: "Not sent", message: "That couldn't be sent. Check your connection and try again." };
                }
            },
        };
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>

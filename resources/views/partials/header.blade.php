<header class="site-header{{ ($solid ?? false) ? ' is-solid' : '' }}" id="site-header" data-solid="{{ ($solid ?? false) ? 'true' : 'false' }}">
    <div class="wrap header-inner">
        <a class="brand" href="/" aria-label="Abia Tech Hub Student Startup Incubation Programme, home">
            @include('partials.logo')
        </a>
        <nav class="nav" aria-label="Main">
            <a href="/about">About</a>
            <a href="/who-can-apply">Who can apply</a>
            <a href="/program">The programme</a>
            <a href="/faqs">FAQs</a>
            <a href="/partners">Partners</a>
        </nav>
        <div class="header-actions">
            <a class="btn btn-lime btn-sm" href="/apply" id="header-apply">Apply now</a>
            <button class="menu-btn" id="menu-btn" type="button" aria-expanded="false" aria-controls="mobile-nav" aria-label="Open menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </div>
    <nav class="mobile-nav" id="mobile-nav" aria-label="Mobile">
        <a href="/about">About</a>
        <a href="/who-can-apply">Who can apply</a>
        <a href="/program">The programme</a>
        <a href="/faqs">FAQs</a>
        <a href="/partners">Partners</a>
        <a href="/apply">Apply</a>
    </nav>
</header>

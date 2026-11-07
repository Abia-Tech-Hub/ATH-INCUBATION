@php
    $pages = [
        ['href' => route('about'), 'number' => '01', 'title' => 'About the Programme', 'description' => 'Why we created a dedicated launchpad for student innovators in Abia.'],
        ['href' => route('who-can-apply'), 'number' => '02', 'title' => 'Who Can Apply', 'description' => 'Review eligibility criteria, supported institutions, and what to prepare.'],
        ['href' => route('program'), 'number' => '03', 'title' => 'Six-Week Journey', 'description' => 'Explore the weekly curriculum, milestone sprints, and final Demo Day.'],
        ['href' => route('partners'), 'number' => '04', 'title' => 'Partners & Mentors', 'description' => 'See how mentors, institutions, and sponsors collaborate with ATH founders.'],
    ];
@endphp
<section class="section home-explore on-light" aria-labelledby="home-explore-title">
    <div class="wrap">
        <div class="home-explore-head reveal">
            <div>
                <p class="section-label">START HERE</p>
                <h2 class="h2" id="home-explore-title">Turn a good question into a validated venture.</h2>
            </div>
            <p class="lead">Get to know the mentors, practical learning sprint, and community designed to help emerging student founders build with focus and momentum.</p>
        </div>
        <div class="home-feature reveal">
            <figure class="home-event-photo">
                <img src="{{ asset('images/IMG_2913.jpg') }}" alt="Students taking part in an Abia Tech Hub community workshop in Umuahia" width="800" height="500">
            </figure>
            <div class="home-feature-copy">
                <p class="section-label">COMMUNITY & COLLABORATION</p>
                <h3>Build with people who want to see you succeed.</h3>
                <p>Great businesses are built by asking sharper questions, testing with real customers, and working alongside a supportive community that holds you accountable.</p>
                <div class="feature-action-link">
                    <a class="btn btn-quiet" href="{{ route('about') }}">Learn more about our mission →</a>
                </div>
            </div>
        </div>
        <nav class="home-route-list" aria-label="Explore Abia Tech Hub">
            @foreach ($pages as $i => $page)
                <a href="{{ $page['href'] }}" class="reveal d{{ $i + 1 }}">
                    <span class="home-route-number">{{ $page['number'] }}</span>
                    <strong class="home-route-title">{{ $page['title'] }}</strong>
                    <span class="home-route-description">{{ $page['description'] }}</span>
                    <span class="home-route-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 18 18 6"></path>
                            <path d="M9 6h9v9"></path>
                        </svg>
                    </span>
                </a>
            @endforeach
        </nav>
        <div class="home-explore-footer">
            <a class="home-faq-link" href="{{ route('faqs') }}">Have specific questions? Browse our comprehensive FAQs <span aria-hidden="true">→</span></a>
        </div>
    </div>
</section>

@php
    $pages = [
        ['href' => '/about', 'number' => '01', 'title' => 'About ATH', 'description' => 'Why we created a practical starting point for student founders in Abia.'],
        ['href' => '/who-can-apply', 'number' => '02', 'title' => 'Who can apply', 'description' => 'Check the eligibility criteria and what you will need to apply.'],
        ['href' => '/program', 'number' => '03', 'title' => 'Six-week programme', 'description' => 'Explore the support, training and milestones across the programme.'],
        ['href' => '/partners', 'number' => '04', 'title' => 'Partners', 'description' => 'See how people and organisations can contribute to founder support.'],
    ];
@endphp
<section class="section home-explore" aria-labelledby="home-explore-title">
    <div class="wrap">
        <div class="home-explore-head">
            <div>
                <p class="section-label">START HERE</p>
                <h2 class="h2" id="home-explore-title">Turn a good question into a better opportunity.</h2>
            </div>
            <p class="lead">Get to know the people, practical support and six-week journey designed to help student founders make progress with purpose.</p>
        </div>
        <div class="home-feature">
            <figure class="home-event-photo">
                <img src="{{ asset('images/IMG_2913.jpg') }}" alt="Students taking part in an Abia Tech Hub community event">
            </figure>
            <div class="home-feature-copy">
                <p class="section-label">IDEAS GROW IN COMMUNITY</p>
                <h3>Build with people who want to see you get further.</h3>
                <p>Good ventures grow through useful questions, honest feedback and the discipline to test an idea before betting everything on it.</p>
            </div>
        </div>
        <nav class="home-route-list" aria-label="Explore Abia Tech Hub">
            @foreach ($pages as $page)
                <a href="{{ $page['href'] }}">
                    <span class="home-route-number">{{ $page['number'] }}</span>
                    <strong>{{ $page['title'] }}</strong>
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
        <a class="home-faq-link" href="/faqs">Have questions? Browse the FAQs <span aria-hidden="true">→</span></a>
    </div>
</section>

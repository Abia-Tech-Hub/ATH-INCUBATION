<footer class="site-footer">
    <div class="wrap footer-wrap">
        <div class="footer-grid">
            <div class="footer-brand">
                <a class="brand" href="/" aria-label="Abia Tech Hub, home">
                    @include('partials.logo')
                </a>
                <p>Empowering the next generation of tech leaders through innovation, education, and entrepreneurship.</p>
                <section class="footer-newsletter" aria-labelledby="footer-newsletter-title">
                    <h2 id="footer-newsletter-title">Subscribe to our newsletter</h2>
                    <form class="footer-newsletter-form" method="post" action="{{ route('newsletter.store') }}">
                        @csrf
                        <div class="footer-newsletter-controls">
                            <input id="footer-newsletter-email" type="email" name="email" autocomplete="email" aria-label="Email address" placeholder="Email address" required>
                            <button type="submit" aria-label="Subscribe to our newsletter">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                            </button>
                        </div>
                        @error('email')
                            <p class="footer-form-error" role="alert">{{ $message }}</p>
                        @enderror
                        @if (session('newsletter_subscribed'))
                            <p class="footer-form-success" role="status">You’re on the list. Thanks for subscribing.</p>
                        @endif
                    </form>
                </section>
            </div>

            <nav class="footer-column" aria-label="Company">
                <h2 class="footer-heading">Company</h2>
                <ul class="footer-links">
                    <li><a href="/about">About Us</a></li>
                    <li><a href="https://cretehost.com/">Web Hosting</a></li>
                    <li><a href="https://abiatechhub.com/contact">Careers</a></li>
                    <li><a href="https://abiatechhub.com/refund_policy">Refund Policy</a></li>
                    <li><a href="https://blog.abiatechhub.com/">Blog</a></li>
                </ul>
            </nav>

            <div class="footer-column footer-contact">
                <h2 class="footer-heading">Contact</h2>
                <address>6 Warri Street, Umuahia, Abia State</address>
                <a href="mailto:hello@abiatechhub.com">hello@abiatechhub.com</a>
                <a href="tel:+2348052749600">+234 805 274 9600</a>
            </div>

            <div class="footer-column footer-follow">
                <h2 class="footer-heading">Follow Us</h2>
                <p>Stay connected with our community.</p>
                <nav class="footer-social-links" aria-label="Social media">
                    <a href="https://x.com/abiatechhub/" aria-label="X">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" /></svg>
                    </a>
                    <a href="https://www.instagram.com/abiatechhub/" aria-label="Instagram">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" /></svg>
                    </a>
                    <a href="https://web.facebook.com/AbiaTechHub/" aria-label="Facebook">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" /></svg>
                    </a>
                    <a href="https://www.linkedin.com/company/abiatechhub/" aria-label="LinkedIn">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z" /></svg>
                    </a>
                </nav>
            </div>
        </div>

        <nav class="footer-additional" aria-label="Additional links">
            <a href="/">Home</a>
            <a href="/about">About</a>
            <a href="/program">The programme</a>
            <a href="/who-can-apply">Who can apply</a>
            <a href="/faqs">FAQs</a>
            <a href="/partners">Partners</a>
            <a href="/apply">Apply</a>
            <a href="https://abiatechhub.com/services">Services</a>
            <a href="https://abiatechhub.com/products">Products</a>
            <a href="https://academy.abiatechhub.com/">Academy</a>
            <a href="https://workspace.abiatechhub.com/">Workspace</a>
            <a href="https://internet.abiatechhub.com/">Internet access</a>
            <a href="https://techflock.ng/">Techflock</a>
            <a href="https://blog.abiatechhub.com/">Blog</a>
            <a href="https://abiatechhub.com/contact">Contact</a>
        </nav>

        <div class="footer-bottom">
            <p>&copy; {{ now()->year }} Abia Tech Hub. All rights reserved.</p>
            <nav class="footer-legal" aria-label="Legal">
                <a href="https://abiatechhub.com/privacy">Privacy Policy</a>
                <a href="https://abiatechhub.com/terms">Terms of Service</a>
            </nav>
        </div>
    </div>
</footer>

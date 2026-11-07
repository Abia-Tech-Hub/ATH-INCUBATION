<section class="section apply" id="apply" aria-labelledby="apply-title">
    <svg class="apply-bg" id="apply-bg" aria-hidden="true" width="100%" height="100%">
        <defs>
            <pattern id="apply-pat" width="56" height="56" patternUnits="userSpaceOnUse">
                <rect x="18" y="18" width="20" height="20" transform="rotate(45 28 28)" fill="none" stroke="#FFFFFF" stroke-width="1.2"></rect>
            </pattern>
            <linearGradient id="apply-fade" x1="0" x2="1" y1="0" y2="0">
                <stop offset="0" stop-color="#fff" stop-opacity="1"></stop>
                <stop offset=".55" stop-color="#fff" stop-opacity="0"></stop>
            </linearGradient>
            <mask id="apply-mask">
                <rect width="100%" height="100%" fill="url(#apply-fade)"></rect>
            </mask>
        </defs>
        <rect width="100%" height="100%" fill="url(#apply-pat)" mask="url(#apply-mask)"></rect>
    </svg>

    <div class="wrap apply-grid">
        <div class="apply-side">
            <p class="section-label apply-eyebrow">SPRING 2026 COHORT</p>
            <h1 class="h2" id="apply-title">Apply in four straightforward steps.</h1>
            <p class="lead">Tell us who you are, what problem you are tackling, and where you need mentorship. Applications are reviewed directly by the Abia Tech Hub team.</p>
            
            <ul class="apply-points">
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                    <div>
                        <strong>Takes ~10 minutes</strong>
                        <span>Answer concise, practical questions about you and your startup idea.</span>
                    </div>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 4h11l3 3v13H5z"></path>
                        <path d="M8 4v5h7V4M8 20v-6h8v6"></path>
                    </svg>
                    <div>
                        <strong>Automatic local draft save</strong>
                        <span>Your progress is preserved on this device. Return anytime without losing answers.</span>
                    </div>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m5 12 4.5 4.5L19 7"></path>
                    </svg>
                    <div>
                        <strong>Review before sending</strong>
                        <span>Step 4 lets you review every section and edit freely before final submission.</span>
                    </div>
                </li>
            </ul>

            <div class="apply-trust-badge">
                <div class="trust-shield-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div>
                    <strong>100% Free</strong>
                    <span>There are no application fees or tuition costs.</span>
                </div>
            </div>
        </div>

        <div class="form-card on-light" id="form-card">
            <div id="form-wrap">
                <ol class="stepper" id="stepper" aria-label="Application progress"></ol>
                
                <div class="restore" id="restore" hidden="">
                    <span class="restore-message">We restored the draft you previously started on this device.</span>
                    <button type="button" id="clear-draft" class="clear-draft-btn">Start fresh</button>
                </div>

                <div class="form-head">
                    <h3 id="step-title" tabindex="-1">About you</h3>
                    <span class="draft-note" id="draft-note" aria-live="polite"></span>
                </div>
                <p class="step-desc" id="step-desc">Start with your details so the team knows who you are and how to reach you.</p>

                <form id="apply-form" data-step="1" noValidate="">
                    {{-- Step 1: About You --}}
                    <fieldset class="step-panel" id="step-1">
                        <legend class="sr-only">About you</legend>
                        <div class="fields">
                            <div class="field full">
                                <label for="f-full_name">Full name <span class="req">*</span></label>
                                <input class="input" id="f-full_name" type="text" autoComplete="name" maxLength="100" aria-describedby="err-full_name" name="full_name" placeholder="e.g. Chinedu Okoro" />
                                <p class="err" id="err-full_name" role="alert"></p>
                            </div>

                            <div class="field">
                                <label for="f-email">Email address <span class="req">*</span></label>
                                <input class="input" id="f-email" type="email" autoComplete="email" inputMode="email" maxLength="120" aria-describedby="err-email" name="email" placeholder="e.g. chinedu@example.com" />
                                <p class="err" id="err-email" role="alert"></p>
                            </div>

                            <div class="field">
                                <label for="f-phone">Phone number <span class="req">*</span></label>
                                <input class="input" id="f-phone" type="tel" autoComplete="tel" inputMode="tel" maxLength="20" aria-describedby="err-phone" name="phone" placeholder="e.g. +234 801 234 5678" />
                                <p class="err" id="err-phone" role="alert"></p>
                            </div>

                            <div class="field">
                                <label for="f-institution">Institution <span class="req">*</span></label>
                                <select class="input" id="f-institution" aria-describedby="hint-institution err-institution" name="institution">
                                    <option value="" selected disabled>Choose your institution</option>
                                    @foreach (array_merge(...array_values(\App\Http\Controllers\ApplyController::INSTITUTIONS)) as $institution)
                                        <option value="{{ $institution }}">{{ $institution }}</option>
                                    @endforeach
                                </select>
                                <p class="hint" id="hint-institution">The tertiary institution in Abia where you study</p>
                                <p class="err" id="err-institution" role="alert"></p>
                            </div>

                            <div class="field">
                                <label for="f-course">Course or department <span class="req">*</span></label>
                                <input class="input" id="f-course" type="text" maxLength="120" aria-describedby="err-course" name="course" placeholder="e.g. Computer Science, Agri-Business" />
                                <p class="err" id="err-course" role="alert"></p>
                            </div>

                            <fieldset class="group field full" id="grp-year" aria-describedby="err-year">
                                <legend class="group-label">Year of study <span class="req">*</span></legend>
                                <div class="chips">
                                    <div class="chip"><input type="radio" id="yr-1" name="year" value="First year"/><label for="yr-1">First year</label></div>
                                    <div class="chip"><input type="radio" id="yr-2" name="year" value="Second year"/><label for="yr-2">Second year</label></div>
                                    <div class="chip"><input type="radio" id="yr-3" name="year" value="Third year"/><label for="yr-3">Third year</label></div>
                                    <div class="chip"><input type="radio" id="yr-4" name="year" value="Fourth year"/><label for="yr-4">Fourth year</label></div>
                                    <div class="chip"><input type="radio" id="yr-5" name="year" value="Fifth year or above"/><label for="yr-5">Fifth year or above</label></div>
                                    <div class="chip"><input type="radio" id="yr-6" name="year" value="Postgraduate"/><label for="yr-6">Postgraduate</label></div>
                                </div>
                                <p class="err" id="err-year" role="alert"></p>
                            </fieldset>
                        </div>
                    </fieldset>

                    {{-- Step 2: Your Startup --}}
                    <fieldset class="step-panel" id="step-2" hidden="">
                        <legend class="sr-only">Your startup</legend>
                        <div class="fields">
                            <div class="field">
                                <label for="f-startup_name">Startup name <span class="req">*</span></label>
                                <input class="input" id="f-startup_name" type="text" maxLength="80" aria-describedby="err-startup_name" name="startup_name" placeholder="Working name is fine" />
                                <p class="err" id="err-startup_name" role="alert"></p>
                            </div>

                            <div class="field">
                                <label for="f-sector">Sector <span class="req">*</span></label>
                                <select class="input" id="f-sector" name="sector" aria-describedby="err-sector">
                                    <option value="">Choose a sector</option>
                                    <option>Agriculture and food</option>
                                    <option>Education</option>
                                    <option>Health</option>
                                    <option>Finance and payments</option>
                                    <option>Commerce and retail</option>
                                    <option>Manufacturing and trade</option>
                                    <option>Logistics and mobility</option>
                                    <option>Fashion and creative</option>
                                    <option>Energy and climate</option>
                                    <option>Media and entertainment</option>
                                    <option>Other</option>
                                </select>
                                <p class="err" id="err-sector" role="alert"></p>
                            </div>

                            <fieldset class="group field full" id="grp-stage" aria-describedby="err-stage">
                                <legend class="group-label">How far along are you? <span class="req">*</span></legend>
                                <div class="cards">
                                    <div class="card-opt">
                                        <input type="radio" id="st-1" name="stage" value="Just an idea"/>
                                        <label for="st-1"><span class="dot"></span><span><strong>Just an idea</strong><span>I understand the problem and have a solution concept</span></span></label>
                                    </div>
                                    <div class="card-opt">
                                        <input type="radio" id="st-2" name="stage" value="Building a prototype"/>
                                        <label for="st-2"><span class="dot"></span><span><strong>Building a prototype</strong><span>I am building the first functional version</span></span></label>
                                    </div>
                                    <div class="card-opt">
                                        <input type="radio" id="st-3" name="stage" value="Early users or customers"/>
                                        <label for="st-3"><span class="dot"></span><span><strong>Early users</strong><span>Real people are already testing or using it</span></span></label>
                                    </div>
                                    <div class="card-opt">
                                        <input type="radio" id="st-4" name="stage" value="Making revenue"/>
                                        <label for="st-4"><span class="dot"></span><span><strong>Making revenue</strong><span>Customers are already paying for the solution</span></span></label>
                                    </div>
                                </div>
                                <p class="err" id="err-stage" role="alert"></p>
                            </fieldset>

                            <div class="field full">
                                <label for="f-problem">What problem are you solving, and for whom? <span class="req">*</span></label>
                                <textarea class="input" id="f-problem" name="problem" maxLength="600" aria-describedby="err-problem cnt-problem" placeholder="Describe the specific pain point your target audience faces and how they cope today (at least 20 characters)..."></textarea>
                                <div class="field-meta">
                                    <p class="err" id="err-problem" role="alert"></p>
                                    <p class="counter" id="cnt-problem">0 / 600</p>
                                </div>
                            </div>

                            <div class="field full">
                                <label for="f-solution">What is your proposed solution? <span class="req">*</span></label>
                                <textarea class="input" id="f-solution" name="solution" maxLength="600" aria-describedby="err-solution cnt-solution" placeholder="Describe how your product or service solves the problem better than existing alternatives (at least 20 characters)..."></textarea>
                                <div class="field-meta">
                                    <p class="err" id="err-solution" role="alert"></p>
                                    <p class="counter" id="cnt-solution">0 / 600</p>
                                </div>
                            </div>

                            <fieldset class="group field" id="grp-team_size" aria-describedby="err-team_size">
                                <legend class="group-label">Team size <span class="req">*</span></legend>
                                <div class="chips">
                                    <div class="chip"><input type="radio" id="tm-1" name="team_size" value="Just me"/><label for="tm-1">Just me</label></div>
                                    <div class="chip"><input type="radio" id="tm-2" name="team_size" value="2 people"/><label for="tm-2">2 people</label></div>
                                    <div class="chip"><input type="radio" id="tm-3" name="team_size" value="3 to 4 people"/><label for="tm-3">3 to 4 people</label></div>
                                    <div class="chip"><input type="radio" id="tm-4" name="team_size" value="5 or more"/><label for="tm-4">5+ people</label></div>
                                </div>
                                <p class="err" id="err-team_size" role="alert"></p>
                            </fieldset>

                            <div class="field">
                                <label for="f-link">Website, demo, or pitch link <span class="opt">(optional)</span></label>
                                <input class="input" id="f-link" type="text" inputMode="url" maxLength="200" aria-describedby="err-link" name="link" placeholder="https://..." />
                                <p class="err" id="err-link" role="alert"></p>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Step 3: Mentorship Needs --}}
                    <fieldset class="step-panel" id="step-3" hidden="">
                        <legend class="sr-only">Mentorship needs</legend>
                        <div class="fields">
                            <fieldset class="group field full" id="grp-mentor_areas" aria-describedby="hint-areas err-mentor_areas">
                                <legend class="group-label">Where would mentorship help most? <span class="req">*</span></legend>
                                <p class="hint" id="hint-areas">Choose all that apply to your current stage</p>
                                <div class="chips" id="area-chips"></div>
                                <p class="err" id="err-mentor_areas" role="alert"></p>
                            </fieldset>

                            <div class="field full">
                                <label for="f-goal">What do you hope to achieve in the 6 weeks? <span class="req">*</span></label>
                                <textarea class="input" id="f-goal" name="goal" maxLength="600" aria-describedby="err-goal cnt-goal" placeholder="E.g. Validate customer willingness to pay, launch an MVP with 50 users, or build an investor pitch deck..."></textarea>
                                <div class="field-meta">
                                    <p class="err" id="err-goal" role="alert"></p>
                                    <p class="counter" id="cnt-goal">0 / 600</p>
                                </div>
                            </div>

                            <fieldset class="group field full" id="grp-commitment" aria-describedby="err-commitment">
                                <legend class="group-label">Can you commit to the 6-week cohort? <span class="req">*</span></legend>
                                <div class="cards">
                                    <div class="card-opt">
                                        <input type="radio" id="cm-1" name="commitment" value="Yes, for all 6 weeks"/>
                                        <label for="cm-1"><span class="dot"></span><span><strong>Yes, all 6 weeks</strong><span>I can attend weekly workshops and mentor sessions</span></span></label>
                                    </div>
                                    <div class="card-opt">
                                        <input type="radio" id="cm-2" name="commitment" value="Would need some flexibility"/>
                                        <label for="cm-2"><span class="dot"></span><span><strong>Need some flexibility</strong><span>May need flexibility around exams or faculty lectures</span></span></label>
                                    </div>
                                </div>
                                <p class="err" id="err-commitment" role="alert"></p>
                            </fieldset>

                            <div class="field full">
                                <label for="f-heard">How did you hear about the programme? <span class="opt">(optional)</span></label>
                                <select class="input" id="f-heard" name="heard">
                                    <option value="">Choose one</option>
                                    <option>A friend or classmate</option>
                                    <option>My university / faculty notice</option>
                                    <option>Social media (X, Instagram, LinkedIn)</option>
                                    <option>An Abia Tech Hub event</option>
                                    <option>Other</option>
                                </select>
                            </div>

                            <div class="field full">
                                <label class="consent" for="f-consent">
                                    <input type="checkbox" id="f-consent" aria-describedby="err-consent" name="consent" value="yes"/>
                                    <span>I confirm that the details provided are accurate and I consent to Abia Tech Hub contacting me regarding this application.</span>
                                </label>
                                <p class="err" id="err-consent" role="alert"></p>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Step 4: Review --}}
                    <div class="step-panel" id="step-4" hidden="">
                        <div class="review-intro">
                            <p>Please review your information below before submitting. You can click <strong>Edit</strong> on any section to make changes.</p>
                        </div>
                        <div class="review" id="review"></div>
                    </div>

                    <p class="form-status" id="form-status" role="alert" hidden=""></p>

                    <div class="form-nav">
                        <button class="btn btn-quiet" type="button" id="back-btn" hidden="">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"></path></svg>
                            Back
                        </button>
                        <span class="spacer"></span>
                        <button class="btn btn-forest" type="submit" id="next-btn">
                            <span id="next-label">Continue</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

            <div class="success" id="apply-success" hidden="" tabindex="-1">
                <div class="success-icon-wrap" aria-hidden="true">
                    <svg class="success-mark" viewBox="0 0 100 100">
                        <rect x="18" y="18" width="64" height="64" rx="10" transform="rotate(45 50 50)" fill="#543C9E"></rect>
                        <path d="M34 51 l11 11 l22 -24" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <h3>Application Successfully Submitted!</h3>
                <p id="success-text">Your application has reached the Abia Tech Hub team.</p>
                <div class="success-steps-box">
                    <h4>What happens next?</h4>
                    <ol>
                        <li>Our evaluation committee reviews your submission against cohort capacity.</li>
                        <li>You will receive an email confirmation and outcome update within 5 business days.</li>
                        <li>Shortlisted founders will be invited for a 15-minute introductory video or hub chat.</li>
                    </ol>
                </div>
                <div class="success-actions">
                    <a class="btn btn-lime" href="/">Back to home page</a>
                </div>
            </div>
        </div>
    </div>
</section>

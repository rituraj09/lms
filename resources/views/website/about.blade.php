    @include('website.layouts.header')

    <header class="page-head">
        <div class="container">
            <span class="eyebrow" style="color:#67e8f9;background:rgba(6,182,212,.18)">🌱 Our Story</span>
            <h1>Building Smarter Humans</h1>
            <p>MindShiksha exists to make every Indian learner future-ready — by blending cognitive science, life skills
                and
                AI.</p>
        </div>
    </header>

    <section>
        <div class="container grid grid-2" style="align-items:center">
            <div class="reveal"><span class="eyebrow">Philosophy</span>
                <h2>Where Science Meets Skills</h2>
                <p class="text-muted" style="margin:16px 0">We believe true learning goes beyond marks. Our approach is
                    rooted
                    in three principles:</p>
                <ul style="list-style:none;display:grid;gap:12px">
                    <li>🧠 <strong>Cognitive science + life skills + AI</strong> working together</li>
                    <li>📐 <strong>Mental age</strong>, not just chronological age</li>
                    <li>🇮🇳 <strong>Ethical, research-backed &amp; India-centric</strong> design</li>
                </ul>
            </div>
            <div class="card reveal" style="background:var(--grad);color:#fff">
                <h3 style="color:#fff">Our Mission</h3>
                <p style="color:#dbeafe;margin-top:10px">To turn research-backed cognitive and life-skill frameworks
                    into
                    personalized digital journeys that build smarter minds and stronger futures.</p>
            </div>
        </div>
    </section>

    <!-- TIMELINE -->
    <section style="background:#fff">
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Milestones</span>
                <h2>Our Journey</h2>
            </div>
            <div class="steps reveal" style="margin-top:48px;grid-template-columns:repeat(4,1fr)">
                <div class="step">
                    <div class="num">1</div>
                    <h3>Research</h3>
                    <p class="text-muted">Frameworks developed</p>
                </div>
                <div class="step">
                    <div class="num">2</div>
                    <h3>Build</h3>
                    <p class="text-muted">AI-powered LMS</p>
                </div>
                <div class="step">
                    <div class="num">3</div>
                    <h3>Pilot</h3>
                    <p class="text-muted">Indian classrooms</p>
                </div>
                <div class="step">
                    <div class="num">4</div>
                    <h3>Scale</h3>
                    <p class="text-muted">Nationwide rollout</p>
                </div>
            </div>
        </div>
    </section>

    <!-- LEADERSHIP -->
    <section>
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Leadership</span>
                <h2>Meet the Team</h2>
            </div>
            <div class="grid grid-3 reveal" style="margin-top:40px">
                <div class="card center">
                    <div class="icon" style="margin:0 auto 18px;width:64px;height:64px;border-radius:50%">👤</div>
                    <h3>Name Placeholder</h3>
                    <p class="text-muted">Founder &amp; CEO</p>
                </div>
                <div class="card center">
                    <div class="icon" style="margin:0 auto 18px;width:64px;height:64px;border-radius:50%">👤</div>
                    <h3>Name Placeholder</h3>
                    <p class="text-muted">Head of Learning Science</p>
                </div>
                <div class="card center">
                    <div class="icon" style="margin:0 auto 18px;width:64px;height:64px;border-radius:50%">👤</div>
                    <h3>Name Placeholder</h3>
                    <p class="text-muted">Head of Product</p>
                </div>
            </div>
        </div>
    </section>
    @include('website.layouts.footer')

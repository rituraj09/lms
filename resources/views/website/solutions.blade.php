    @include('website.layouts.header')
    <header class="page-head">
        <div class="container">
            <span class="eyebrow" style="color:#67e8f9;background:rgba(6,182,212,.18)">🧩 Modular LMS</span>
            <h1>Solutions &amp; Products</h1>
            <p>A modular platform — adopt the full suite or start with a single module. Every module ties directly back
                to our cognitive &amp; life-skill frameworks.</p>
        </div>
    </header>

    <section>
        <div class="container">
            <div class="grid grid-3 reveal" style="margin-top:10px">
                <div class="card">
                    <div class="icon">🧠</div>
                    <h3>Cognitive Skills Module</h3>
                    <p>Attention, memory, reasoning & metacognition exercises with mental-age-wise targeting.</p>
                </div>
                <div class="card">
                    <div class="icon">💬</div>
                    <h3>Life Skills &amp; SEL Module</h3>
                    <p>The 10 core life skills delivered through reflection, role-play & scenarios.</p>
                </div>
                <div class="card">
                    <div class="icon">📊</div>
                    <h3>Assessment &amp; Analytics</h3>
                    <p>Adaptive 1–7 tier assessments and growth dashboards for every learner.</p>
                </div>
                <div class="card">
                    <div class="icon">👩‍🏫</div>
                    <h3>Teacher Tools &amp; Lesson Plans</h3>
                    <p>Ready-made lesson plans, observation checklists & classroom strategies.</p>
                </div>
                <div class="card">
                    <div class="icon">👨‍👩‍👧</div>
                    <h3>Parent/Student Portals</h3>
                    <p>Self-paced journeys and progress visibility at home.</p>
                </div>
                <div class="card">
                    <div class="icon">🔗</div>
                    <h3>Integrations</h3>
                    <p>Fits into existing timetables, content and school systems.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- DASHBOARD MOCKUP -->
    <section style="background:#fff">
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Inside the Platform</span>
                <h2>Dashboards Built for Insight</h2>
            </div>
            <div class="hero-visual reveal"
                style="background:var(--secondary);margin-top:36px;max-width:820px;margin-inline:auto">
                <svg viewBox="0 0 400 200" width="100%">
                    <defs>
                        <linearGradient id="b" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0" stop-color="#2563EB" />
                            <stop offset="1" stop-color="#06B6D4" />
                        </linearGradient>
                    </defs>
                    <rect x="20" y="20" width="160" height="70" rx="8" fill="rgba(255,255,255,.06)" />
                    <text x="32" y="45" fill="#94a3b8" font-size="10">Cognitive Growth</text>
                    <polyline points="32,80 60,65 90,70 120,50 150,40" fill="none" stroke="url(#b)"
                        stroke-width="3" />
                    <rect x="200" y="20" width="180" height="70" rx="8" fill="rgba(255,255,255,.06)" />
                    <text x="212" y="45" fill="#94a3b8" font-size="10">Skill Distribution</text>
                    <rect x="212" y="60" width="20" height="20" fill="#2563EB" />
                    <rect x="240" y="52" width="20" height="28" fill="#06B6D4" />
                    <rect x="268" y="58" width="20" height="22" fill="#67e8f9" />
                    <rect x="20" y="105" width="360" height="75" rx="8" fill="rgba(255,255,255,.06)" />
                    <text x="32" y="128" fill="#94a3b8" font-size="10">Life Skill Progress by Class</text>
                    <rect x="32" y="140" width="300" height="8" rx="4" fill="rgba(255,255,255,.1)" />
                    <rect x="32" y="140" width="210" height="8" rx="4" fill="url(#b)" />
                    <rect x="32" y="158" width="300" height="8" rx="4" fill="rgba(255,255,255,.1)" />
                    <rect x="32" y="158" width="150" height="8" rx="4" fill="url(#b)" />
                </svg>
            </div>
        </div>
    </section>

    @include('website.layouts.footer')

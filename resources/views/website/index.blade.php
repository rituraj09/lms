    @include('website.layouts.header')


    <!-- HERO -->
    <header class="hero">
        <div class="container hero-grid">
            <div>
                <span class="eyebrow">⚡ Smarter Minds. Stronger Futures.</span>
                <h1>AI- Powered Learning Management System for Cognitive, Life & Leadership Skills Development</h1>
                <p>MindShiksha provides personalized learning & assessment framework for continuous development of Intelligent Quotient (IQ), Emotional Quotient (EQ) and Leadership Quotient (LQ).
                </p>
                <div class="hero-actions">
                    <a href="{{ route('contact') }}" class="btn btn-primary">Request a Demo →</a>
                    <a href="{{ route('cognitive') }}" class="btn btn-ghost">Explore the Framework</a>
                </div>
            </div>
            <div class="hero-visual">
                <!-- SVG brain-network visual (swap for image later) -->
                <svg viewBox="0 0 300 240" width="100%" aria-label="Cognitive skill network">
                    <defs>
                        <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#2563EB" />
                            <stop offset="1" stop-color="#06B6D4" />
                        </linearGradient>
                    </defs>
                    <circle cx="150" cy="120" r="46" fill="none" stroke="url(#g)" stroke-width="2" />
                    <circle cx="150" cy="120" r="8" fill="#06B6D4" />
                    <g stroke="rgba(255,255,255,.3)" stroke-width="1.2">
                        <line x1="150" y1="120" x2="60" y2="50" />
                        <line x1="150" y1="120" x2="250" y2="60" />
                        <line x1="150" y1="120" x2="40" y2="180" />
                        <line x1="150" y1="120" x2="260" y2="190" />
                        <line x1="150" y1="120" x2="150" y2="30" />
                        <line x1="150" y1="120" x2="150" y2="215" />
                    </g>
                    <g fill="#67e8f9">
                        <circle cx="60" cy="50" r="6" />
                        <circle cx="250" cy="60" r="6" />
                        <circle cx="40" cy="180" r="6" />
                        <circle cx="260" cy="190" r="6" />
                        <circle cx="150" cy="30" r="6" />
                        <circle cx="150" cy="215" r="6" />
                    </g>
                    <text x="150" y="125" text-anchor="middle" fill="#fff" font-size="9" font-family="Poppins">AI
                        Core</text>
                </svg>
                <p style="color:#94a3b8;font-size:.82rem;text-align:center;margin-top:12px">Attention · Memory ·
                    Reasoning · Empathy · Decision-Making</p>
            </div>
        </div>
    </header>

    <!-- WHO WE SERVE -->
    <section>
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Who We Serve</span>
                <h2>One Platform, Every Learning Journey</h2>
            </div>
            <div class="grid grid-4" style="margin-top:40px">
                <div class="card reveal">
                    <div class="icon">🏫</div>
                    <h3>Schools &amp; K-12</h3>
                    <p>Build attention, memory and classroom readiness while embedding everyday life skills.</p>
                </div>
                <div class="card reveal">
                    <div class="icon">🎓</div>
                    <h3>Colleges &amp; Universities</h3>
                    <p>Strengthen critical thinking, decision-making and stress management for the workplace.</p>
                </div>
                <div class="card reveal">
                    <div class="icon">🚀</div>
                    <h3>Training &amp; EdTech</h3>
                    <p>Scalable, standardized, mental-age-wise frameworks ready to plug into your programs.</p>
                </div>
                <div class="card reveal">
                    <div class="icon">👨‍👩‍👧</div>
                    <h3>Parents &amp; Learners</h3>
                    <p>Self-paced cognitive and life-skill journeys learners can continue at home.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TWO PILLARS -->
    <section style="background:#fff">
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Two Core Pillars</span>
                <h2>Research-Backed Frameworks, Digitally Delivered</h2>
            </div>
            <div data-tabs style="margin-top:36px">
                <div class="tabs reveal">
                    <button class="tab active">🧠 Cognitive Skill Framework</button>
                    <button class="tab">💬 Life Skill Framework</button>
                </div>
                <div class="tab-panel active">
                    <div class="card reveal">
                        <h3>Cognitive Skill Development Framework</h3>
                        <p>A mental-age-wise model strengthening the core thinking systems behind learning.</p>
                        <div class="skill-pills">
                            <span class="pill">Attention</span><span class="pill">Memory</span><span
                                class="pill">Logical Thinking</span>
                            <span class="pill">Critical Thinking</span><span class="pill">Abstract
                                Thinking</span><span class="pill">Creative Thinking</span>
                            <span class="pill">Problem Solving</span><span class="pill">Decision
                                Making</span><span class="pill">Metacognition</span>
                            <span class="pill">Hypothetical Thinking</span>
                        </div>
                        <a href="{{ route('cognitive') }}" class="btn btn-secondary" style="margin-top:22px">View
                            Cognitive Framework →</a>
                    </div>
                </div>
                <div class="tab-panel">
                    <div class="card reveal">
                        <h3>Life Skill Development Framework</h3>
                        <p>Based on the UNICEF/WHO 10 Core Life Skills for holistic, resilient development.</p>
                        <div class="skill-pills">
                            <span class="pill">Self-Awareness</span><span class="pill">Empathy</span><span
                                class="pill">Critical Thinking</span>
                            <span class="pill">Creative Thinking</span><span class="pill">Decision
                                Making</span><span class="pill">Problem Solving</span>
                            <span class="pill">Communication</span><span class="pill">Interpersonal
                                Relationship</span>
                            <span class="pill">Coping with Stress</span><span class="pill">Managing
                                Emotions</span>
                        </div>
                        <a href="{{ route('life-skill') }}" class="btn btn-secondary" style="margin-top:22px">View
                            Life Skill Framework →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section>
        <div class="container">
            <div class="center reveal"><span class="eyebrow">How MindShiksha Works</span>
                <h2>From Framework to Personalized Learning</h2>
            </div>
            <div class="grid grid-3 reveal" style="margin-top:40px">
                <div class="card">
                    <div class="icon">📊</div>
                    <h3>AI-Driven Assessments</h3>
                    <p>Age-wise and mental-age-wise difficulty calibration for every learner.</p>
                </div>
                <div class="card">
                    <div class="icon">🛤️</div>
                    <h3>Personalized Paths</h3>
                    <p>1–7 difficulty tiers per skill, adapting to each student's pace.</p>
                </div>
                <div class="card">
                    <div class="icon">🎮</div>
                    <h3>Interactive Modules</h3>
                    <p>Videos, scenarios and exercises mapped to sub-skills and methodologies.</p>
                </div>
                <div class="card">
                    <div class="icon">📈</div>
                    <h3>Educator Analytics</h3>
                    <p>Visualize cognitive and life-skill growth across classes.</p>
                </div>
                <div class="card">
                    <div class="icon">🗓️</div>
                    <h3>Timetable Integration</h3>
                    <p>Fits seamlessly into existing school schedules and content.</p>
                </div>
                <div class="card">
                    <div class="icon">📝</div>
                    <h3>Observation Checklists</h3>
                    <p>Track behavioral indicators directly inside the LMS.</p>
                </div>
            </div>
            <!-- process steps -->
            <div class="steps reveal" style="margin-top:60px">
                <div class="step">
                    <div class="num">1</div>
                    <h3>Assess</h3>
                    <p class="text-muted">Baseline skill mapping</p>
                </div>
                <div class="step">
                    <div class="num">2</div>
                    <h3>Learn</h3>
                    <p class="text-muted">Targeted modules</p>
                </div>
                <div class="step">
                    <div class="num">3</div>
                    <h3>Practice</h3>
                    <p class="text-muted">Adaptive exercises</p>
                </div>
                <div class="step">
                    <div class="num">4</div>
                    <h3>Reflect</h3>
                    <p class="text-muted">Journaling & review</p>
                </div>
                <div class="step">
                    <div class="num">5</div>
                    <h3>Track</h3>
                    <p class="text-muted">Growth analytics</p>
                </div>
            </div>
        </div>
    </section>

    <!-- INDIAN FOCUS -->
    <section style="background:var(--secondary);color:#cbd5e1">
        <div class="container grid grid-2" style="align-items:center">
            <div class="reveal">
                <span class="eyebrow">Built for India</span>
                <h2 style="color:#fff">Aligned to NEP &amp; 21st-Century Skills</h2>
                <p style="margin:16px 0 20px">Contextualized for Indian learners and multilingual classrooms — from
                    CBSE and State boards to colleges and skilling centres.</p>
                <ul style="list-style:none;display:grid;gap:12px">
                    <li>✅ Supports CBSE / State / College environments</li>
                    <li>✅ Aligned to national skill development &amp; employability needs</li>
                    <li>✅ Built for Indian, multilingual classrooms</li>
                </ul>
            </div>
            <div class="hero-visual reveal">
                <svg viewBox="0 0 300 180" width="100%">
                    <defs>
                        <linearGradient id="b" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#2563EB" />
                            <stop offset="1" stop-color="#06B6D4" />
                        </linearGradient>
                    </defs>
                    <rect x="20" y="120" width="40" height="40" rx="6" fill="url(#b)" />
                    <rect x="80" y="90" width="40" height="70" rx="6" fill="url(#b)"
                        opacity=".8" />
                    <rect x="140" y="60" width="40" height="100" rx="6" fill="url(#b)"
                        opacity=".9" />
                    <rect x="200" y="40" width="40" height="120" rx="6" fill="#06B6D4" />
                    <text x="150" y="25" text-anchor="middle" fill="#fff" font-size="11"
                        font-family="Poppins">Skill Growth Over Time</text>
                </svg>
            </div>
        </div>
    </section>

    <!-- OUTCOMES -->
    <section>
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Outcomes &amp; Benefits</span>
                <h2>Value for Everyone</h2>
            </div>
            <div class="grid grid-3 reveal" style="margin-top:40px">
                <div class="card">
                    <div class="icon">🎯</div>
                    <h3>For Students</h3>
                    <p>Cognitive excellence, resilience, better decision-making and stronger life skills.</p>
                </div>
                <div class="card">
                    <div class="icon">👩‍🏫</div>
                    <h3>For Educators</h3>
                    <p>Clear visibility into each learner's growth with actionable strategies.</p>
                </div>
                <div class="card">
                    <div class="icon">🏛️</div>
                    <h3>For Institutions</h3>
                    <p>Future-ready learners, measurable outcomes and standardized frameworks.</p>
                </div>
            </div>
        </div>
    </section>


    @include('website.layouts.footer')

    @include('website.layouts.header')

    <header class="page-head">
        <div class="container">
            <span class="eyebrow" style="color:#67e8f9;background:rgba(6,182,212,.18)">👥 Use Cases</span>
            <h1>Built for Every Learning Environment</h1>
            <p>See which frameworks and age-bands matter most for your context.</p>
        </div>
    </header>

    <section>
        <div class="container grid grid-2">
            <div class="card reveal">
                <div class="icon">🏫</div>
                <h3>For Schools &amp; K-12</h3>
                <p>Strengthen attention, memory and classroom readiness while embedding everyday life skills.</p>
                <div class="skill-pills"><span class="pill">Cognitive Framework</span><span class="pill">Age 10+ ·
                        12+</span></div>
            </div>
            <div class="card reveal">
                <div class="icon">🎓</div>
                <h3>For Higher Ed &amp; Training</h3>
                <p>Develop critical thinking, decision-making, problem solving and stress management for the workplace.
                </p>
                <div class="skill-pills"><span class="pill">Both Frameworks</span><span class="pill">Age 14+</span>
                </div>
            </div>
            <div class="card reveal">
                <div class="icon">🚀</div>
                <h3>For EdTech &amp; NGOs</h3>
                <p>Scalable frameworks, standardized assessments and mental-age-wise difficulty for large programs.</p>
                <div class="skill-pills"><span class="pill">Standardized Assessments</span><span class="pill">All
                        Ages</span></div>
            </div>
            <div class="card reveal">
                <div class="icon">👨‍👩‍👧</div>
                <h3>For Parents &amp; Learners</h3>
                <p>Self-paced cognitive and life-skill journeys learners can continue at home.</p>
                <div class="skill-pills"><span class="pill">Life Skills</span><span class="pill">Self-Paced</span>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="cta-band reveal">
                <h2>Find the Right Fit for Your Learners</h2>
                <p>Tell us your context and we'll tailor a walkthrough.</p>
                <a href="{{ route('contact') }}" class="btn btn-secondary">Talk to Us →</a>
            </div>
        </div>
    </section>

    @include('website.layouts.footer')

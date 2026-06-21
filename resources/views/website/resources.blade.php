    @include('website.layouts.header')

    <header class="page-head">
        <div class="container">
            <span class="eyebrow" style="color:#67e8f9;background:rgba(6,182,212,.18)">📚 Insights</span>
            <h1>Resources &amp; Guides</h1>
            <p>Articles, framework one-pagers and downloadable checklists for educators and parents.</p>
        </div>
    </header>

    <section>
        <div class="container">
            <div class="center reveal"><span class="eyebrow">From the Blog</span>
                <h2>Latest Insights</h2>
            </div>
            <div class="grid grid-3 reveal" style="margin-top:40px">
                <div class="card">
                    <div class="icon">🎯</div>
                    <h3>Understanding Attention &amp; Executive Function in Teens</h3>
                    <p>How attention develops and practical ways to strengthen it. <em>[Add blurb]</em></p><a
                        href="#" style="margin-top:12px;display:inline-block">Read more →</a>
                </div>
                <div class="card">
                    <div class="icon">❤️</div>
                    <h3>Teaching Empathy &amp; Self-Awareness in Indian Classrooms</h3>
                    <p>Simple, culturally-relevant ways to build emotional intelligence.</p><a href="#"
                        style="margin-top:12px;display:inline-block">Read more →</a>
                </div>
                <div class="card">
                    <div class="icon">🧩</div>
                    <h3>Building Problem-Solving Skills, Age by Age</h3>
                    <p>A developmental view of how children learn to solve problems.</p><a href="#"
                        style="margin-top:12px;display:inline-block">Read more →</a>
                </div>
            </div>
        </div>
    </section>

    <section style="background:#fff">
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Downloads</span>
                <h2>Free Framework Resources</h2>
            </div>
            <div class="grid grid-2 reveal" style="margin-top:40px;max-width:760px;margin-inline:auto">
                <div class="card" style="display:flex;align-items:center;gap:16px">
                    <div class="icon" style="margin:0">📄</div>
                    <div>
                        <h3>Cognitive Framework One-Pager</h3>
                        <p>Quick reference of skills & age targets.</p><a href="#">Download PDF →</a>
                    </div>
                </div>
                <div class="card" style="display:flex;align-items:center;gap:16px">
                    <div class="icon" style="margin:0">✅</div>
                    <div>
                        <h3>Behavioral Indicators Checklist</h3>
                        <p>Observation checklist for educators.</p><a href="#">Download PDF →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="cta-band reveal">
                <h2>Want the Full Handbooks?</h2>
                <p>Request access to our complete framework documentation.</p>
                <a href="{{ route('contact') }}" class="btn btn-secondary">Request Access →</a>
            </div>
        </div>
    </section>
    @include('website.layouts.footer')

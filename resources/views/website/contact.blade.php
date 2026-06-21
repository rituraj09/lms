    @include('website.layouts.header')

    <header class="page-head">
        <div class="container">
            <span class="eyebrow" style="color:#67e8f9;background:rgba(6,182,212,.18)">📩 Get Started</span>
            <h1>Request a Demo</h1>
            <p>Tell us about your institution and we'll set up a personalized walkthrough.</p>
        </div>
    </header>

    <section>
        <div class="container grid grid-2" style="align-items:start;gap:48px">
            <!-- FORM -->
            <div class="card reveal">
                <div class="success-msg">✅ Thank you! Your request has been received. Our team will reach out within 1
                    business day.</div>
                <form id="demoForm" novalidate>
                    <div class="form-grid">
                        <div class="field"><label>Full Name <span class="req">*</span></label><input type="text"
                                required placeholder="Your name"></div>
                        <div class="field"><label>Institution <span class="req">*</span></label><input
                                type="text" required placeholder="School / College / Org"></div>
                        <div class="field"><label>Role <span class="req">*</span></label>
                            <select required>
                                <option value="">Select role</option>
                                <option>Principal / Head</option>
                                <option>Teacher / Educator</option>
                                <option>Administrator</option>
                                <option>Parent</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="field"><label>Email <span class="req">*</span></label><input type="email"
                                required placeholder="you@example.com"></div>
                        <div class="field"><label>Phone</label><input type="tel" placeholder="+91 ..."></div>
                        <div class="field"><label>Preferred Demo Slot</label><input type="datetime-local"></div>
                        <div class="field full"><label>Message</label>
                            <textarea rows="4" placeholder="Tell us what you're looking for..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"
                        style="margin-top:18px;width:100%;justify-content:center">Submit Request →</button>
                </form>
            </div>
            <!-- VALUE SIDE -->
            <div class="reveal">
                <h2>Why Schedule a Demo?</h2>
                <ul style="list-style:none;display:grid;gap:16px;margin-top:20px">
                    <li class="card" style="display:flex;gap:12px;align-items:center"><span class="icon"
                            style="margin:0">🎯</span>
                        <div><strong>Tailored Walkthrough</strong>
                            <p class="text-muted">See modules relevant to your learners.</p>
                        </div>
                    </li>
                    <li class="card" style="display:flex;gap:12px;align-items:center"><span class="icon"
                            style="margin:0">📊</span>
                        <div><strong>Live Analytics</strong>
                            <p class="text-muted">Explore real dashboards & reports.</p>
                        </div>
                    </li>
                    <li class="card" style="display:flex;gap:12px;align-items:center"><span class="icon"
                            style="margin:0">🔒</span>
                        <div><strong>Data Privacy First</strong>
                            <p class="text-muted">Educational focus only. Your data stays protected.</p>
                        </div>
                    </li>
                </ul>
                <div class="hero-visual reveal" style="background:var(--secondary);margin-top:24px">
                    <svg viewBox="0 0 300 140" width="100%">
                        <circle cx="150" cy="70" r="40" fill="none" stroke="#06B6D4" stroke-width="2" />
                        <circle cx="150" cy="70" r="6" fill="#2563EB" /><text x="150" y="125"
                            text-anchor="middle" fill="#94a3b8" font-size="11" font-family="Poppins">Serving learners
                            across India 🇮🇳</text>
                    </svg>
                </div>
            </div>
        </div>
    </section>
    @include('website.layouts.footer')

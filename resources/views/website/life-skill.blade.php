    @include('website.layouts.header')
    <header class="page-head">
        <div class="container">
            <span class="eyebrow" style="color:#67e8f9;background:rgba(6,182,212,.18)">💬 Holistic Development</span>
            <h1>Life Skill Development Framework</h1>
            <p>Built on the UNICEF/WHO 10 Core Life Skills — nurturing resilience, emotional intelligence and healthy
                relationships beyond academic knowledge.</p>
        </div>
    </header>

    <!-- 10 CORE LIFE SKILLS -->
    <section>
        <div class="container">
            <div class="center reveal"><span class="eyebrow">The 10 Core Life Skills</span>
                <h2>More Than Academics</h2>
                <p class="text-muted" style="max-width:620px;margin:14px auto 0">A holistic model developing the personal,
                    social and emotional capabilities every learner needs to thrive.</p>
            </div>
            <div class="grid grid-4 reveal" style="margin-top:40px">
                <div class="card">
                    <div class="icon">🪞</div>
                    <h3>Self-Awareness</h3>
                    <p>Understanding one's emotions, strengths and values.</p>
                </div>
                <div class="card">
                    <div class="icon">❤️</div>
                    <h3>Empathy</h3>
                    <p>Recognizing and sharing others' feelings.</p>
                </div>
                <div class="card">
                    <div class="icon">🔍</div>
                    <h3>Critical Thinking</h3>
                    <p>Analyzing information objectively.</p>
                </div>
                <div class="card">
                    <div class="icon">💡</div>
                    <h3>Creative Thinking</h3>
                    <p>Generating novel ideas and solutions.</p>
                </div>
                <div class="card">
                    <div class="icon">⚖️</div>
                    <h3>Decision Making</h3>
                    <p>Choosing responsibly among options.</p>
                </div>
                <div class="card">
                    <div class="icon">🧩</div>
                    <h3>Problem Solving</h3>
                    <p>Resolving challenges effectively.</p>
                </div>
                <div class="card">
                    <div class="icon">🗣️</div>
                    <h3>Communication</h3>
                    <p>Expressing clearly and listening well.</p>
                </div>
                <div class="card">
                    <div class="icon">🤝</div>
                    <h3>Interpersonal Relationship</h3>
                    <p>Building and sustaining connections.</p>
                </div>
                <div class="card">
                    <div class="icon">🧘</div>
                    <h3>Coping with Stress</h3>
                    <p>Managing pressure and challenges.</p>
                </div>
                <div class="card">
                    <div class="icon">🌊</div>
                    <h3>Managing Emotions</h3>
                    <p>Regulating feelings constructively.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- AGE-WISE TABLE -->
    <section style="background:#fff">
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Age-Wise Targets</span>
                <h2>Life Skill Development Map</h2>
            </div>
            <div class="tabs reveal" style="margin-top:28px">
                <button class="tab active" data-filter="all">All Ages</button>
                <button class="tab" data-filter="10+">Age 10+</button>
                <button class="tab" data-filter="12+">Age 12+</button>
                <button class="tab" data-filter="14+">Age 14+</button>
            </div>
            <div class="table-wrap reveal">
                <table>
                    <thead>
                        <tr>
                            <th>Life Skill</th>
                            <th>Age Group</th>
                            <th>Difficulty Levels</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-age="10+">
                            <td>Self-Awareness</td>
                            <td>10+</td>
                            <td><span class="level-chip">L1–L3</span></td>
                        </tr>
                        <tr data-age="10+">
                            <td>Empathy</td>
                            <td>10+</td>
                            <td><span class="level-chip">L1–L4</span></td>
                        </tr>
                        <tr data-age="12+">
                            <td>Communication</td>
                            <td>12+</td>
                            <td><span class="level-chip">L2–L5</span></td>
                        </tr>
                        <tr data-age="14+">
                            <td>Coping with Stress</td>
                            <td>14+</td>
                            <td><span class="level-chip">L3–L7</span></td>
                        </tr>
                        <!-- Add real rows from handbook later -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- SKILL ACCORDIONS -->
    <section>
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Skill by Skill</span>
                <h2>Explore Each Life Skill</h2>
            </div>
            <div style="margin-top:36px;max-width:860px;margin-inline:auto">
                <div class="accordion-item reveal">
                    <div class="accordion-head">Self-Awareness <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <div class="grid grid-2" style="margin-top:8px">
                            <div>
                                <h4 style="font-size:1rem;color:var(--primary)">Definition & Sub-Skills</h4>
                                <p class="text-muted">Understanding one's emotions, values and identity. <em>[Add
                                        handbook text]</em></p>
                            </div>
                            <div>
                                <h4 style="font-size:1rem;color:var(--primary)">Behavioral Indicators</h4>
                                <p class="text-muted"><strong>Positive:</strong> names emotions.
                                    <strong>Emerging:</strong> developing reflection.</p>
                            </div>
                        </div>
                        <h4 style="font-size:1rem;margin:18px 0 8px">Age-Wise Progression</h4>
                        <div class="skill-pills"><span class="pill">0–2: Mirror recognition</span><span
                                class="pill">3–5: Emotional labeling</span><span class="pill">6–10: Social
                                comparison</span><span class="pill">11+: Identity & values</span></div>
                        <h4 style="font-size:1rem;margin:18px 0 8px">Improving Methodologies</h4>
                        <div class="skill-pills"><span class="pill">For Students</span><span class="pill">For
                                Teachers</span><span class="pill">For Parents</span></div>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Empathy <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Critical Thinking <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Creative Thinking <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Decision Making <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Problem Solving <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Communication <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Interpersonal Relationship <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Coping with Stress <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <h4 style="font-size:1rem;margin:8px 0;color:var(--primary)">Four Coping Types</h4>
                        <div class="skill-pills"><span class="pill">Problem-Focused</span><span
                                class="pill">Emotion-Focused</span><span class="pill">Meaning-Making</span><span
                                class="pill">Social Support</span></div>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Managing Emotions <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LMS INTEGRATION -->
    <section style="background:var(--secondary);color:#cbd5e1">
        <div class="container">
            <div class="center reveal"><span class="eyebrow">In the Platform</span>
                <h2 style="color:#fff">Life Skills, Lived in the LMS</h2>
            </div>
            <div class="grid grid-3 reveal" style="margin-top:40px">
                <div class="card" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)">
                    <div class="icon">📔</div>
                    <h3 style="color:#fff">Reflection Journals</h3>
                    <p style="color:#94a3b8">Guided journaling & self-reflection prompts.</p>
                </div>
                <div class="card" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)">
                    <div class="icon">🎭</div>
                    <h3 style="color:#fff">Role-Plays & Discussions</h3>
                    <p style="color:#94a3b8">Group activities to practice empathy & communication.</p>
                </div>
                <div class="card" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)">
                    <div class="icon">🎯</div>
                    <h3 style="color:#fff">Scenario Assessments</h3>
                    <p style="color:#94a3b8">Decision-making & stress-management microskills.</p>
                </div>
            </div>
        </div>
    </section>

    @include('website.layouts.footer')

    @include('website.layouts.header')

    <!-- PAGE HEAD -->
    <header class="page-head">
        <div class="container">
            <span class="eyebrow" style="color:#67e8f9;background:rgba(6,182,212,.18)">🧠 Cognitive Science</span>
            <h1>Cognitive Skill Development Framework</h1>
            <p>A mental-age-wise model building the foundation of attention, memory, reasoning and higher-order thinking
                — turned into a clear, navigable digital learning experience.</p>
        </div>
    </header>

    <!-- OVERVIEW -->
    <section>
        <div class="container grid grid-2" style="align-items:center">
            <div class="reveal">
                <span class="eyebrow">Overview</span>
                <h2>Mental Age, Not Just Chronological Age</h2>
                <p class="text-muted" style="margin:16px 0">Traditional models target learners by age in years.
                    MindShiksha's framework targets the <strong>Mental Age-Wise Cognitive Development Model</strong> —
                    meeting each learner at their actual cognitive readiness.</p>
                <ul style="list-style:none;display:grid;gap:10px">
                    <li>✅ Attention &amp; concentration systems</li>
                    <li>✅ Working &amp; long-term memory</li>
                    <li>✅ Logical &amp; analytical reasoning</li>
                    <li>✅ Critical, abstract &amp; creative thinking</li>
                    <li>✅ Metacognition &amp; self-regulation</li>
                </ul>
            </div>
            <div class="hero-visual reveal" style="background:var(--secondary)">
                <svg viewBox="0 0 300 240" width="100%">
                    <defs>
                        <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#2563EB" />
                            <stop offset="1" stop-color="#06B6D4" />
                        </linearGradient>
                    </defs>
                    <circle cx="150" cy="120" r="46" fill="none" stroke="url(#g)" stroke-width="2" />
                    <circle cx="150" cy="120" r="8" fill="#06B6D4" />
                    <g stroke="rgba(255,255,255,.25)">
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
                </svg>
            </div>
        </div>
    </section>

    <!-- AGE-WISE TABLE -->
    <section style="background:#fff">
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Age-Wise Targets</span>
                <h2>Cognitive Skill Development Map</h2>
            </div>
            <!-- Filters -->
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
                            <th>Core Skill</th>
                            <th>Sub-Skill</th>
                            <th>Age Group</th>
                            <th>Difficulty Levels</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-age="10+">
                            <td>Attention</td>
                            <td>Sustained Attention</td>
                            <td>10+</td>
                            <td><span class="level-chip">L1–L3</span></td>
                        </tr>
                        <tr data-age="12+">
                            <td>Memory</td>
                            <td>Working Memory</td>
                            <td>12+</td>
                            <td><span class="level-chip">L1–L5</span></td>
                        </tr>
                        <tr data-age="12+">
                            <td>Logical Thinking</td>
                            <td>Pattern Recognition</td>
                            <td>12+</td>
                            <td><span class="level-chip">L2–L5</span></td>
                        </tr>
                        <tr data-age="14+">
                            <td>Critical Thinking</td>
                            <td>Argument Evaluation</td>
                            <td>14+</td>
                            <td><span class="level-chip">L3–L7</span></td>
                        </tr>
                        <tr data-age="14+">
                            <td>Abstract Thinking</td>
                            <td>Conceptual Reasoning</td>
                            <td>14+</td>
                            <td><span class="level-chip">L4–L7</span></td>
                        </tr>
                        <!-- Add real rows from handbook later -->
                    </tbody>
                </table>
            </div>
            <p class="text-muted center" style="margin-top:14px;font-size:.85rem">💡 Levels 1–7 represent progressive
                difficulty, from foundational recall to complex application.</p>
        </div>
    </section>

    <!-- SKILL ACCORDIONS -->
    <section>
        <div class="container">
            <div class="center reveal"><span class="eyebrow">Skill by Skill</span>
                <h2>The Skill We Access & Develops</h2>
            </div>
            <div style="margin-top:36px;max-width:860px;margin-inline:auto">
                <!-- Repeatable accordion block -->
                <div class="accordion-item reveal">
                    <div class="accordion-head">Attention <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <div class="grid grid-3" style="margin-top:8px">
                            <div>
                                <h4 style="font-size:1rem;color:var(--primary)">Definition</h4>
                                <p class="text-muted">The ability to focus and sustain concentration on relevant
                                    stimuli. <em>[Add handbook text]</em></p>
                            </div>
                            <div>
                                <h4 style="font-size:1rem;color:var(--primary)">Core Sub-Skills</h4>
                                <ul class="text-muted" style="padding-left:18px">
                                    <li>Selective attention</li>
                                    <li>Sustained attention</li>
                                    <li>Divided attention</li>
                                </ul>
                            </div>
                            <div>
                                <h4 style="font-size:1rem;color:var(--primary)">Behavioral Indicators</h4>
                                <p class="text-muted"><strong>Positive:</strong> stays on task.
                                    <strong>Deficit:</strong> easily distracted.</p>
                            </div>
                        </div>
                        <h4 style="font-size:1rem;margin:18px 0 8px">Improving Methodologies</h4>
                        <div class="skill-pills"><span class="pill">Classroom Strategies</span><span
                                class="pill">Daily Practice</span><span class="pill">Lifestyle Factors</span>
                        </div>
                    </div>
                </div>
                <!-- Duplicate this block for: Memory, Logical Thinking, Critical Thinking, Abstract Thinking, Creative Thinking, Problem Solving, Decision Making, Metacognition, Hypothetical Thinking -->
                <div class="accordion-item reveal">
                    <div class="accordion-head">Memory <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Add definition, sub-skills, behavioral
                            indicators, age-wise outcomes & methodologies.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Logical Thinking <span class="chev">⌄</span></div>
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
                    <div class="accordion-head">Abstract Thinking <span class="chev">⌄</span></div>
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
                    <div class="accordion-head">Problem Solving <span class="chev">⌄</span></div>
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
                    <div class="accordion-head">Metacognition <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
                <div class="accordion-item reveal">
                    <div class="accordion-head">Hypothetical Thinking <span class="chev">⌄</span></div>
                    <div class="accordion-body">
                        <p class="text-muted" style="margin-top:8px">Content placeholder.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- IMPLEMENTATION -->
    <section style="background:var(--secondary);color:#cbd5e1">
        <div class="container">
            <div class="center reveal"><span class="eyebrow">In the Platform</span>
                <h2 style="color:#fff">How MindShiksha Framework Works</h2>
            </div>
            <div class="steps reveal" style="margin-top:48px">
                <div class="step">
                    <div class="num">1</div>
                    <h3 style="color:#fff">Assess</h3>
                    <p>Items mapped to sub-skills</p>
                </div>
                <div class="step">
                    <div class="num">2</div>
                    <h3 style="color:#fff">Learn</h3>
                    <p>AI-recommended exercises</p>
                </div>
                <div class="step">
                    <div class="num">3</div>
                    <h3 style="color:#fff">Practice</h3>
                    <p>Attention & memory tasks</p>
                </div>
                <div class="step">
                    <div class="num">4</div>
                    <h3 style="color:#fff">Reflect</h3>
                    <p>Self-check prompts</p>
                </div>
                <div class="step">
                    <div class="num">5</div>
                    <h3 style="color:#fff">Track</h3>
                    <p>Observation checklists</p>
                </div>
            </div>
        </div>
    </section>

    @include('website.layouts.footer')

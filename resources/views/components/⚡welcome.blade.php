<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts.frontend')]
class extends Component
{
    //
};
?>

<div>
    <div id="progress-bar"></div>
    <nav id="mainNav">
        <a href="#" class="nav-logo">
            <div class="logo-mark">M</div>
            MindSiksha EDTech
        </a>
        <ul class="nav-links">
            <li><a href="#about">Framework</a></li>
            <li><a href="#life-skills">Life Skills</a></li>
            <li><a href="#cognitive-skills">Cognitive</a></li>
            <li><a href="#cta" class="nav-cta">Get Started</a></li>
        </ul>
    </nav>

    <section id="hero">
        <canvas class="hero-canvas" id="particleCanvas"></canvas>
        <div class="orb orb1"></div>
        <div class="orb orb2"></div>
        <div class="orb orb3"></div>
        <div class="hero-mesh"></div>
        <div class="hero-grid" id="heroGrid"></div>

        <div class="hero-content">
            <div class="hero-eyebrow" id="heroEyebrow">Guwahati, Assam &nbsp;·&nbsp; Adaptive Learning Institute</div>

            <h1 class="hero-title" id="heroTitle">
                <span class="line"><span class="word">Building</span>&nbsp;<span class="word"><em>Smarter</em></span></span>
                <span class="line"><span class="word">Humans</span></span>
            </h1>

            <p class="hero-sub" id="heroSub">
                Empowering individuals through the convergence of Emotional Intelligence and Cognitive Science — a holistic framework for lifelong performance.
            </p>

            <div class="hero-actions" id="heroActions">
                <a href="#about" class="btn-primary-hero">
                    Explore Framework
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="#life-skills" class="btn-ghost-hero">
                    <i class="fa-solid fa-play" style="font-size:.7rem;"></i>
                    View Skills
                </a>
            </div>
        </div>

        <div class="hero-scroll-hint" id="heroScroll">
            <span>Scroll</span>
            <div class="scroll-line"></div>
        </div>
    </section>

    <div class="marquee-strip">
        <div class="marquee-track" id="marqueeTrack">
        </div>
    </div>

    <section id="about" class="sec-pad">
        <div class="container">
            <div class="about-grid">
                <div class="about-left">
                    <div class="sec-label" id="aboutLabel">Our Philosophy</div>
                    <h2 class="about-heading" id="aboutHeading">
                        A holistic model for <strong>human development</strong>
                    </h2>
                    <p class="about-body" id="aboutBody1">
                        Our framework is built on a profound understanding: cognitive maturity differs significantly among individuals of the same physical age. The Mental Age Wise Cognitive Development Model recognises this truth and builds around it.
                    </p>
                    <p class="about-body" id="aboutBody2">
                        MindSiksha serves as a structured roadmap — nurturing the critical human capabilities of IQ and EQ in equal measure, preparing individuals not just for exams, but for life.
                    </p>
                    <div class="about-pills" id="aboutPills">
                        <span class="pill">Real-life Application</span>
                        <span class="pill">Emotional Learning</span>
                        <span class="pill">Age-Wise Development</span>
                        <span class="pill">Adaptive Curriculum</span>
                    </div>
                </div>
                <div class="about-visual" id="aboutVisual">
                    <img
                        src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80"
                        alt="Holistic Learning Environment"
                        class="about-img"
                    />
                    <div class="about-img-frame"></div>
                    <div class="about-stat-card" id="statCard">
                        <div class="stat-num" id="statNum">10</div>
                        <div class="stat-label">WHO / UNICEF Core Life Skills</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="numbers-bar">
        <div class="container">
            <div class="numbers-grid">
                <div class="num-item">
                    <span class="num-val" data-count="10">0</span>
                    <span class="num-desc">Core Life Skills</span>
                </div>
                <div class="num-item">
                    <span class="num-val" data-count="7">0</span>
                    <span class="num-desc">Cognitive Domains</span>
                </div>
                <div class="num-item">
                    <span class="num-val" data-count="100">0</span>
                    <span class="num-desc">% Adaptive Approach</span>
                </div>
                <div class="num-item">
                    <span class="num-val" data-count="1">0</span>
                    <span class="num-desc">City, Infinite Impact</span>
                </div>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <section id="life-skills" class="sec-pad">
        <div class="container">
            <div class="skills-header">
                <div class="sec-label" style="justify-content:center;">
                    <span style="width:30px;height:1px;background:var(--gold);display:inline-block;"></span>
                    Emotional Intelligence
                    <span style="width:30px;height:1px;background:var(--gold);display:inline-block;"></span>
                </div>
                <h2 class="skills-title">Life Skills <em>Framework</em></h2>
                <p class="skills-intro">
                    Life skills form the foundation of resilient, emotionally intelligent, and socially responsible individuals — rooted in the 10 Core Life Skills identified by UNICEF and the WHO.
                </p>
            </div>

            <div class="skills-grid" id="lifeSkillsGrid">
                <div class="skill-card"><span class="skill-num">01</span><i class="fa-solid fa-user-check skill-icon"></i><h3 class="skill-name">Self-Awareness</h3><p class="skill-desc">The conscious ability to recognise and understand your own thoughts, emotions, behaviours, strengths, and areas for growth.</p></div>
                <div class="skill-card"><span class="skill-num">02</span><i class="fa-solid fa-hands-holding-circle skill-icon"></i><h3 class="skill-name">Empathy</h3><p class="skill-desc">The ability to understand, share, and resonate with another person's emotions and perspective by imagining oneself in their situation.</p></div>
                <div class="skill-card"><span class="skill-num">03</span><i class="fa-solid fa-comments skill-icon"></i><h3 class="skill-name">Effective Communication</h3><p class="skill-desc">Conveying ideas and emotions with clarity, while also deeply understanding and responding to the messages of others.</p></div>
                <div class="skill-card"><span class="skill-num">04</span><i class="fa-solid fa-users skill-icon"></i><h3 class="skill-name">Interpersonal Relations</h3><p class="skill-desc">Building and maintaining meaningful social, emotional, and professional connections characterised by mutual trust and influence.</p></div>
                <div class="skill-card"><span class="skill-num">05</span><i class="fa-solid fa-heart-pulse skill-icon"></i><h3 class="skill-name">Managing Emotions</h3><p class="skill-desc">Recognising, understanding, and regulating personal feelings to maintain emotional balance, wellbeing, and happiness.</p></div>
                <div class="skill-card"><span class="skill-num">06</span><i class="fa-solid fa-spa skill-icon"></i><h3 class="skill-name">Coping with Stress</h3><p class="skill-desc">Adaptive thoughts and behaviours used to manage, reduce, or tolerate the psychological strain of challenging situations.</p></div>
            </div>
        </div>
    </section>

    <section id="cognitive-skills" class="sec-pad">
        <div class="container">
            <div class="skills-header">
                <div class="sec-label" style="justify-content:center;">
                    <span style="width:30px;height:1px;background:var(--gold);display:inline-block;"></span>
                    Intelligence Quotient
                    <span style="width:30px;height:1px;background:var(--gold);display:inline-block;"></span>
                </div>
                <h2 class="skills-title">Cognitive Skills <em>Framework</em></h2>
                <p class="skills-intro">
                    Core mental abilities that enable individuals to process information, adapt to environments, make decisions, and perform complex intellectual tasks.
                </p>
            </div>

            <div class="cog-grid" id="cogGrid">
                <div class="skill-card"><span class="skill-num">01</span><i class="fa-solid fa-bullseye skill-icon"></i><h3 class="skill-name">Attention</h3><p class="skill-desc">The ability to focus on relevant stimuli while filtering distractions and sustaining concentration.</p></div>
                <div class="skill-card"><span class="skill-num">02</span><i class="fa-solid fa-memory skill-icon"></i><h3 class="skill-name">Memory Systems</h3><p class="skill-desc">The learned ability to efficiently encode, store, and recall information through deliberate strategy and practice.</p></div>
                <div class="skill-card"><span class="skill-num">03</span><i class="fa-solid fa-diagram-project skill-icon"></i><h3 class="skill-name">Logical Thinking</h3><p class="skill-desc">Analysing situations and drawing rational conclusions based on facts rather than emotions or assumptions.</p></div>
                <div class="skill-card"><span class="skill-num">04</span><i class="fa-solid fa-magnifying-glass-chart skill-icon"></i><h3 class="skill-name">Critical Thinking</h3><p class="skill-desc">The disciplined process of actively analysing, interpreting, and synthesising information to form well-reasoned judgements.</p></div>
                <div class="skill-card"><span class="skill-num">05</span><i class="fa-solid fa-lightbulb skill-icon"></i><h3 class="skill-name">Creative Thinking</h3><p class="skill-desc">Approaching problems from new perspectives to generate innovative, original, and effective solutions.</p></div>
                <div class="skill-card"><span class="skill-num">06</span><i class="fa-solid fa-puzzle-piece skill-icon"></i><h3 class="skill-name">Problem Solving</h3><p class="skill-desc">Identifying complex issues, analysing root causes, and implementing effective, tested solutions.</p></div>
                <div class="skill-card"><span class="skill-num">07</span><i class="fa-solid fa-sitemap skill-icon"></i><h3 class="skill-name">Decision Making</h3><p class="skill-desc">Cognitive abilities that enable individuals to evaluate alternatives and select the optimal course of action.</p></div>
            </div>
        </div>
    </section>

    <section id="cta">
        <div class="container" style="position:relative;z-index:2;">
            <div class="sec-label" style="justify-content:center;margin-bottom:2rem;">
                <span style="width:30px;height:1px;background:var(--gold);display:inline-block;"></span>
                Join MindSiksha
                <span style="width:30px;height:1px;background:var(--gold);display:inline-block;"></span>
            </div>
            <p class="cta-quote" id="ctaQuote">
                "The future belongs to those who <em>learn more skills</em> and <em>combine them</em> in creative ways."
            </p>
            <p class="cta-sub">Begin your journey of holistic human development today.</p>
            <a href="#contact" class="cta-btn">
                Start Your Journey
                <i class="fa-solid fa-arrow-right arrow"></i>
            </a>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <div class="footer-inner">
                <div>
                    <div class="footer-brand">MindSiksha EDTech</div>
                    <p class="footer-tagline">Empowering minds through adaptive intelligence. Building smarter humans, one skill at a time.</p>
                    <div class="footer-social" style="margin-top:1.5rem;">
                        <a href="#" class="social-btn"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-x"></i></a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h5>Navigate</h5>
                    <ul>
                        <li><a href="#about">Our Framework</a></li>
                        <li><a href="#life-skills">Life Skills (EQ)</a></li>
                        <li><a href="#cognitive-skills">Cognitive (IQ)</a></li>
                        <li><a href="#cta">Get Started</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h5>Framework</h5>
                    <ul>
                        <li><a href="#">Mental Age Model</a></li>
                        <li><a href="#">WHO Core Skills</a></li>
                        <li><a href="#">Adaptive Learning</a></li>
                        <li><a href="#">Research Basis</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h5>Contact</h5>
                    <ul>
                        <li><a href="#">hello@mindsiksha.in</a></li>
                        <li><a href="#">+91 361 XXX XXXX</a></li>
                        <li><a href="#">Guwahati, Assam</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copy">© 2026 MindSiksha EDTech Pvt Ltd. All Rights Reserved.</p>
                <div class="footer-loc">
                    <i class="fa-solid fa-location-dot"></i>
                    Guwahati, Assam, India
                </div>
            </div>
        </div>
    </footer>
</div>

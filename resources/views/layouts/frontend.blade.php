<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindSiksha EDTech — Building Smarter Humans</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,600&family=DM+Sans:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/frontend.css')}}">
    @livewireStyles
</head>
<body>
{{$slot}}
@livewireScripts
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/TextPlugin.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollToPlugin.min.js"></script>
<script>
    // ─── REGISTER ───
    gsap.registerPlugin(ScrollTrigger, TextPlugin, ScrollToPlugin);


    // ─── PROGRESS BAR ───
    window.addEventListener('scroll', () => {
        const pct = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
        document.getElementById('progress-bar').style.width = pct + '%';
    });

    // ─── NAV SCROLL ───
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 80);
    });

    // ─── MARQUEE ───
    const items = ['Adaptive Learning','Emotional Intelligence','Cognitive Development','Life Skills','Self-Awareness','Critical Thinking','Empathy','Problem Solving','Memory Systems','Creative Thinking','Decision Making','Mental Age Model'];
    const track = document.getElementById('marqueeTrack');
    let html = '';
    for(let i=0;i<3;i++){
        items.forEach(t => {
            html += `<span class="marquee-item"><span class="marquee-dot"></span>${t}</span>`;
        });
    }
    track.innerHTML = html;

    // ─── PARTICLE CANVAS ───
    const canvas = document.getElementById('particleCanvas');
    const ctx = canvas.getContext('2d');
    let W, H, particles = [];
    function resize(){ W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
    resize();
    window.addEventListener('resize', resize);

    class Particle {
        constructor(){
            this.x = Math.random()*W;
            this.y = Math.random()*H;
            this.size = Math.random()*1.5+.3;
            this.vx = (Math.random()-.5)*.3;
            this.vy = (Math.random()-.5)*.3;
            this.alpha = Math.random()*.4+.1;
        }
        update(){
            this.x += this.vx; this.y += this.vy;
            if(this.x<0||this.x>W) this.vx*=-1;
            if(this.y<0||this.y>H) this.vy*=-1;
        }
        draw(){
            ctx.beginPath();
            ctx.arc(this.x,this.y,this.size,0,Math.PI*2);
            ctx.fillStyle = `rgba(201,153,58,${this.alpha})`;
            ctx.fill();
        }
    }
    for(let i=0;i<120;i++) particles.push(new Particle());

    // Draw connecting lines
    function drawLines(){
        for(let i=0;i<particles.length;i++){
            for(let j=i+1;j<particles.length;j++){
                const dx=particles[i].x-particles[j].x;
                const dy=particles[i].y-particles[j].y;
                const dist=Math.sqrt(dx*dx+dy*dy);
                if(dist<100){
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x,particles[i].y);
                    ctx.lineTo(particles[j].x,particles[j].y);
                    ctx.strokeStyle=`rgba(201,153,58,${.05*(1-dist/100)})`;
                    ctx.lineWidth=.5;
                    ctx.stroke();
                }
            }
        }
    }

    function animCanvas(){
        ctx.clearRect(0,0,W,H);
        particles.forEach(p=>{ p.update(); p.draw(); });
        drawLines();
        requestAnimationFrame(animCanvas);
    }
    animCanvas();

    // ─── HERO ENTRANCE ───
    const tl = gsap.timeline({ defaults:{ ease:'power3.out' } });

    tl.to('#heroGrid', { opacity:.5, duration:1.5 })
        .to('#heroEyebrow', { opacity:1, y:0, duration:1 }, '-=1')
        .to('.word', {
            opacity:1, y:0,
            duration:1.2,
            stagger:.12,
            ease:'expo.out'
        }, '-=0.5')
        .to('#heroSub', { opacity:1, y:0, duration:1 }, '-=.6')
        .to('#heroActions', { opacity:1, y:0, duration:.8 }, '-=.5')
        .to('#heroScroll', { opacity:1, duration:.8 }, '-=.3');

    // ─── ABOUT SECTION ───
    gsap.from('#aboutLabel', {
        scrollTrigger: { trigger:'#about', start:'top 80%' },
        opacity:0, x:-30, duration:.8
    });
    gsap.from('#aboutHeading', {
        scrollTrigger: { trigger:'#about', start:'top 75%' },
        opacity:0, y:40, duration:1, ease:'expo.out'
    });
    gsap.from(['#aboutBody1','#aboutBody2'], {
        scrollTrigger: { trigger:'#about', start:'top 70%' },
        opacity:0, y:20, duration:.9, stagger:.2
    });
    gsap.from('#aboutPills .pill', {
        scrollTrigger: { trigger:'#aboutPills', start:'top 85%' },
        opacity:0, y:15, stagger:.1, duration:.6
    });
    gsap.from('#aboutVisual', {
        scrollTrigger: { trigger:'#aboutVisual', start:'top 80%' },
        opacity:0, x:60, duration:1.2, ease:'expo.out'
    });
    gsap.from('#statCard', {
        scrollTrigger: { trigger:'#aboutVisual', start:'top 70%' },
        opacity:0, x:-40, y:20, duration:1, delay:.3, ease:'back.out(1.4)'
    });

    // ─── COUNTER ANIMATION ───
    document.querySelectorAll('.num-val').forEach(el => {
        const target = parseInt(el.dataset.count);
        ScrollTrigger.create({
            trigger: el,
            start: 'top 85%',
            onEnter: () => {
                gsap.to({val:0},{
                    val: target,
                    duration: 2,
                    ease: 'power2.out',
                    onUpdate: function(){
                        el.textContent = Math.floor(this.targets()[0].val);
                    }
                });
            },
            once: true
        });
    });

    // ─── SKILL CARD STAGGER ───
    function staggerCards(selector){
        gsap.from(selector + ' .skill-card', {
            scrollTrigger: { trigger: selector, start:'top 80%' },
            opacity:1, y:50, scale:.97,
            stagger:{ amount:.8, from:'start' },
            duration:.9, ease:'expo.out'
        });
    }
    staggerCards('#lifeSkillsGrid');
    staggerCards('#cogGrid');

    // ─── SKILLS HEADER ───
    gsap.utils.toArray('.skills-header').forEach(h => {
        gsap.from(h, {
            scrollTrigger:{ trigger:h, start:'top 85%' },
            opacity:0, y:30, duration:1
        });
    });

    // ─── CTA ───
    gsap.from('#ctaQuote', {
        scrollTrigger:{ trigger:'#cta', start:'top 80%' },
        opacity:0, y:40, duration:1.2, ease:'expo.out'
    });
    gsap.from('#cta .cta-sub, #cta .cta-btn', {
        scrollTrigger:{ trigger:'#cta', start:'top 75%' },
        opacity:0, y:20, stagger:.2, duration:.9
    });

    // ─── FOOTER ───
    gsap.from('footer .footer-inner > *', {
        scrollTrigger:{ trigger:'footer', start:'top 85%' },
        opacity:0, y:30, stagger:.15, duration:.8
    });

    // ─── ORB PARALLAX ───
    window.addEventListener('mousemove', e => {
        const x = (e.clientX/window.innerWidth - .5);
        const y = (e.clientY/window.innerHeight - .5);
        gsap.to('.orb1', { x: x*40, y: y*30, duration:2, ease:'power1.out' });
        gsap.to('.orb2', { x: -x*30, y: -y*20, duration:2, ease:'power1.out' });
        gsap.to('.orb3', { x: x*20, y: y*15, duration:2, ease:'power1.out' });
    });

    // ─── HERO TEXT PARALLAX ───
    window.addEventListener('scroll', () => {
        const s = window.scrollY;
        gsap.to('.hero-content', { y: s*.3, ease:'none', duration:0 });
        gsap.to('#heroScroll', { opacity: Math.max(0,1-s/300), duration:0 });
    });

    // ─── SECTION LINES SCROLL REVEAL ───
    gsap.utils.toArray('.sec-label').forEach(el => {
        gsap.from(el, {
            scrollTrigger:{ trigger:el, start:'top 88%' },
            opacity:0, x:-20, duration:.7
        });
    });

    // ─── DIVIDER WIPE ───
    gsap.from('.divider', {
        scrollTrigger:{ trigger:'.divider', start:'top 90%' },
        scaleX:0, transformOrigin:'left center', duration:1.5, ease:'expo.out'
    });

    // ─── CARD HOVER TILT ───
    document.querySelectorAll('.skill-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left)/rect.width - .5;
            const y = (e.clientY - rect.top)/rect.height - .5;
            gsap.to(card, { rotateX: -y*6, rotateY: x*6, duration:.4, ease:'power2.out', transformPerspective:800 });
        });
        card.addEventListener('mouseleave', () => {
            gsap.to(card, { rotateX:0, rotateY:0, duration:.6, ease:'elastic.out(1,.5)' });
        });
    });

    // ─── SMOOTH ANCHOR SCROLL ───
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if(target){ e.preventDefault(); gsap.to(window, { scrollTo:target, duration:1.2, ease:'power3.inOut' }); }
        });
    });

    // ─── NUMBERS BAR PARALLAX ───
    gsap.from('.numbers-bar', {
        scrollTrigger:{ trigger:'.numbers-bar', start:'top 85%' },
        opacity:0, duration:.8
    });
    gsap.from('.num-item', {
        scrollTrigger:{ trigger:'.numbers-bar', start:'top 80%' },
        opacity:0, y:30, stagger:.15, duration:.8
    });
</script>
</body>
</html>

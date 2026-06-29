<section>
    <div class="container">
        <div class="cta-band reveal">
            <h2>Partner With Us</h2>
            <p>Join us in building future-ready learners across India.</p>
            <a href="{{ route('contact') }}" class="btn btn-secondary">Get in Touch →</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="index" class="logo" style="color:#fff;margin-bottom:14px"><span
                        class="logo-mark">M</span>MindShiksha</a>
                <p style="font-size:.9rem">Smarter Minds. Stronger Futures. AI-powered learning for cognitive &amp; life
                    skills.</p>
                <div class="socials" style="margin-top:18px">
                    <a href="#">in</a><a href="#">𝕏</a><a href="#">f</a><a href="#">▶</a>
                </div>
            </div>
            <div>
                <h4>Frameworks</h4><a href="cognitive-framework">Cognitive Skills</a><a href="life-skill-framework">Life
                    Skills</a><a href="solutions">Solutions</a>
            </div>
            <div>
                <h4>Company</h4><a href="about">About</a><a href="use-cases">For Whom</a><a
                    href="resources">Resources</a>
            </div>
            <div>
                <h4>Get Started</h4><a href="contact">Request a Demo</a><a href="contact">Contact</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} MindShiksha. All rights reserved.</span>
        </div>
    </div>
</footer>

<script src="{{ asset('website/js/main.js') }}"></script>
<script>
    // ============================================================
    // MOBILE MENU TOGGLE
    // ============================================================

    const hamburger = document.querySelector('.hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const mainNav = document.getElementById('mainNav');

    // Toggle mobile menu open/closed
    function toggleMobileMenu() {
        const isOpen = mobileMenu.classList.contains('open');

        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    function openMobileMenu() {
        mobileMenu.classList.add('open');
        hamburger.classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent background scroll
        hamburger.setAttribute('aria-expanded', 'true');
    }

    function closeMobileMenu() {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('active');
        document.body.style.overflow = '';
        hamburger.setAttribute('aria-expanded', 'false');
    }

    // Hamburger button click
    hamburger.addEventListener('click', toggleMobileMenu);

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        const clickedOutsideMenu = !mobileMenu.contains(e.target);
        const clickedOutsideHamburger = !hamburger.contains(e.target);

        if (mobileMenu.classList.contains('open') && clickedOutsideMenu && clickedOutsideHamburger) {
            closeMobileMenu();
        }
    });

    // Close menu on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
            closeMobileMenu();
        }
    });

    // ============================================================
    // NAV SCROLL EFFECT
    // ============================================================
    window.addEventListener('scroll', function() {
        if (window.scrollY > 20) {
            mainNav.classList.add('scrolled');
        } else {
            mainNav.classList.remove('scrolled');
        }
    });
</script>
</body>

</html>

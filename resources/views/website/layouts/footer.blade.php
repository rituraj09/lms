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
                <a href="#" class="logo" style="color:#fff;margin-bottom:14px"><span
                        class="logo-mark"></span>MindShiksha EdTech Private Limited</a>
                <p>CIN: U62011AS2026PTC030649</p>
                <p style="font-size:.9rem">Smarter Minds, Stronger Future. AI Powered Learning Solution for Cognitive,
                    Life and Leadership Skills</p>
                <div class="socials" style="margin-top:18px">
                    <a href="#">in</a><a href="#">𝕏</a><a href="#">f</a><a href="#">▶</a>
                </div>
            </div>
            <div>
                <h4>Frameworks</h4><a href="{{ route('cognitive') }}">Cognitive Skills</a><a
                    href="{{ route('life-skill') }}">Life
                    Skills</a><a href="{{ route('solutions') }}">Leadership Skills</a>
            </div>
            <div>
                <h4>Company</h4><a href="{{ route('about') }}">About</a><a href="{{ route('use-cases') }}">Blogs</a><a
                    href="{{ route('resources') }}">Resources</a>
            </div>
            <div>
                <h4>Get Started</h4>
                {{-- <a href="{{ route('contact') }}">Request a Demo</a> --}}
                <a href="{{ route('contact') }}">Contact</a>
                <a href="https://hub.mindshiksha.com/admin/login" target="_blank">Official Login</a>
                <a href="https://app.mindshiksha.com/" target="_blank">Student Login</a>
            </div>




        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} MindShiksha EdTech Private Limited. All rights reserved.</span>
            <span>Powered by research-backed Cognitive &amp; Life Skill Development Frameworks.</span>
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

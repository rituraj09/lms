<!-- ============================================================
     NAVIGATION
============================================================ -->
<nav class="nav" id="mainNav">
    <div class="nav-inner">

        <a href="{{ route('home') }}" class="nav-logo" onclick="showPage('home')">
            <img src="{{ asset('website/assets/logo-full.png') }}" style="height:28px;width:auto" alt="MindShiksha">

        </a>
        <div class="nav-links">
            <a href="{{ route('home') }}" class="nav-link  {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>

            <div class="nav-dropdown">
                <a href="#cognitive"
                    class="nav-link  {{ request()->routeIs('cognitive', 'life-skill') ? 'active' : '' }}">
                    Frameworks <i class="fas fa-chevron-down" style="font-size:0.65rem;margin-left:3px;"></i>
                </a>
                <div class="nav-dropdown-menu">
                    <a href="{{ route('cognitive') }}"
                        class="nav-dropdown-item {{ request()->routeIs('cognitive') ? 'active' : '' }}">
                        <i class="fas fa-brain"></i> Cognitive Skill Framework
                    </a>
                    <a href="{{ route('life-skill') }}"
                        class="nav-dropdown-item  {{ request()->routeIs('life-skill') ? 'active' : '' }}">
                        <i class="fas fa-heart"></i> Life Skill Framework
                    </a>
                </div>
            </div>

            <a href="{{ route('solutions') }}"
                class="nav-link  {{ request()->routeIs('solutions') ? 'active' : '' }}">Solutions</a>
            <a href="{{ route('use-cases') }}"
                class="nav-link {{ request()->routeIs('use-cases') ? 'active' : '' }}">For Whom</a>
            <a href="{{ route('about') }}"
                class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
            <a href="{{ route('resources') }}"
                class="nav-link {{ request()->routeIs('resources') ? 'active' : '' }}">Resources</a>
            <a href="{{ route('contact') }}"
                class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
        </div>

        <div class="nav-actions">
            <a href="/admin/login" class="btn btn-secondary">
                <i class="fas fa-sign-in" style="margin-right:0.4rem;font-size:0.82rem;"></i>
                Login</a>
            <a href="{{ route('contact') }}" class="btn btn-primary">
                <i class="fas fa-calendar-alt" style="margin-right:0.4rem;font-size:0.82rem;"></i>
                Request a Demo
            </a>
            <button class="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
        </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('home') }}" class="mobile-nav-link" onclick="navigateTo('home');toggleMobileMenu()">🏠 Home</a>
    <a href="{{ route('cognitive') }}" class="mobile-nav-link" onclick="navigateTo('cognitive');toggleMobileMenu()">🧠
        Cognitive Framework</a>
    <a href="{{ route('life-skill') }}" class="mobile-nav-link" onclick="navigateTo('lifeskill');toggleMobileMenu()">💚
        Life Skill Framework</a>
    <a href="{{ route('solutions') }}" class="mobile-nav-link" onclick="navigateTo('solutions');toggleMobileMenu()">💡
        Solutions</a>
    <a href="{{ route('use-cases') }}" class="mobile-nav-link" onclick="navigateTo('usecases');toggleMobileMenu()">👥
        For Whom</a>
    <a href="{{ route('about') }}" class="mobile-nav-link" onclick="navigateTo('about');toggleMobileMenu()">ℹ️
        About</a>
    <a href="{{ route('resources') }}" class="mobile-nav-link" onclick="navigateTo('resources');toggleMobileMenu()">📚
        Resources</a>
    <a href="{{ route('contact') }}" class="mobile-nav-link" onclick="navigateTo('contact');toggleMobileMenu()">📞
        Contact</a>
    <div style="margin-top:1rem;">
        <button class="btn btn-primary" style="width:100%;justify-content:center;"
            onclick="navigateTo('contact');toggleMobileMenu()">
            <i class="fas fa-calendar-alt"></i> Request a Demo
        </button>
    </div>
</div>

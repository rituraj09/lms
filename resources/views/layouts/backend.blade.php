<!doctype html>
{{-- views/layouts/backend.blade.php --}}
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    x-data="{ theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', value => {
        document.documentElement.setAttribute('data-bs-theme', value);
        localStorage.setItem('theme', value);
    });
    document.documentElement.setAttribute('data-bs-theme', theme);
    window.addEventListener('theme-changed', event => {
        theme = event.detail.theme;
    });" :data-bs-theme="theme" data-assets-path="{{ asset('assets') }}/"
    data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="description" content="{{ config('app.name') }} - Learning Management System" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'LMS Platform'))</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@formvalidation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/notyf/notyf.css') }}" />

    {{-- Custom LMS Styles --}}
    <style>
        /* Sidebar section headers */
        .menu-header-text {
            font-size: 0.68rem !important;
            letter-spacing: 0.08em;
            font-weight: 700 !important;
            opacity: 0.55;
        }

        /* Smooth menu transitions */
        .menu-sub {
            transition: all 0.25s ease-in-out;
        }

        /* Active menu item indicator */
        .menu-item.active>.menu-link {
            font-weight: 600;
        }

        /* Navbar search */
        .navbar-search .form-control:focus {
            box-shadow: none;
        }

        /* Notification badge */
        .navbar-nav .badge {
            font-size: 0.6rem;
            min-width: 18px;
            height: 18px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Footer links */
        .footer-link:hover {
            color: var(--bs-primary) !important;
        }

        /* User dropdown polish */
        .dropdown-item {
            transition: background-color 0.15s ease;
        }

        /* Brand area */
        .app-brand {
            min-height: 64px;
        }

        /* Support box */
        .menu-inner>li:last-child {
            margin-bottom: 2rem;
        }
    </style>

    @stack('style')
    @livewireStyles

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            @if (Auth::guard('admin')->check())
                @livewire('skeleton.sidebar')
            @endif

            <div class="layout-page">

                @if (Auth::guard('admin')->check())
                    @livewire('skeleton.header')
                @endif

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        {{ $slot }}
                    </div>

                    @if (Auth::guard('admin')->check())
                        @livewire('skeleton.footer')
                    @endif
                </div>

            </div>
        </div>
    </div>

    @livewireScripts

    {{-- Core JS --}}
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@formvalidation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@formvalidation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/autofocus.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/notyf/notyf.js') }}"></script>

    @stack('script')

    <script src="{{ asset('assets/js/main.js') }}"></script>

    {{-- Notyf Notifications --}}
    <script>
        const notyf = new Notyf({
            duration: 4000,
            position: {
                x: 'right',
                y: 'bottom'
            },
            types: [{
                    type: 'success',
                    background: '#28a745',
                    icon: {
                        className: 'ri ri-checkbox-circle-fill',
                        tagName: 'i',
                        color: 'white'
                    }
                },
                {
                    type: 'error',
                    background: '#dc3545',
                    icon: {
                        className: 'ri ri-error-warning-fill',
                        tagName: 'i',
                        color: 'white'
                    }
                }
            ]
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                if (event.type === 'success') notyf.success(event.message);
                if (event.type === 'error') notyf.error(event.message);
                if (event.type === 'warning') notyf.open({
                    type: 'warning',
                    message: event.message
                });
            });
        });
    </script>

</body>

</html>

<!doctype html>
<html
    lang="en"
    class="layout-wide customizer-hide"
    dir="ltr"
    data-skin="default"
    data-bs-theme="light"
    data-assets-path="{{asset('assets')}}/"
    data-template="vertical-menu-template-no-customizer">
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>{{config('app.name','MindSiksha EDTech')}}</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon/favicon.png')}}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css')}}" />
    @stack('style')
    @livewireStyles
    <script src="{{ asset('assets/vendor/js/helpers.js')}}"></script>
    <script src="{{ asset('assets/js/config.js')}}"></script>
</head>

<body>
<div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
        <div class="py-6">
            {{$slot}}
            <img alt="mask" src="{{ asset('assets/img/illustrations/auth-basic-reset-password-mask-light.png')}}"
                 class="authentication-image d-none d-lg-block"
            />
        </div>
    </div>
</div>
@livewireScripts
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/@algolia/autocomplete-js.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/i18n/i18n.js')}}"></script>
<script src="{{ asset('assets/vendor/js/menu.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/popular.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js')}}"></script>
<script src="{{ asset('assets/js/main.js')}}"></script>
@stack('script')
</body>
</html>

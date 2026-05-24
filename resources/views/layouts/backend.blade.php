<!doctype html>
<html
    lang="en"
    class="layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr"
    data-skin="default"
    x-data="{ theme: localStorage.getItem('theme') || 'light' }"
    x-init="$watch('theme', value => {
        document.documentElement.setAttribute('data-bs-theme', value);
        localStorage.setItem('theme', value);
    });

    document.documentElement.setAttribute('data-bs-theme', theme);

    window.addEventListener('theme-changed', event => {
        theme = event.detail.theme;
    });
    "
    :data-bs-theme="theme"
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css')}}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/notyf/notyf.css') }}"/>
    @stack('style')
    @livewireStyles
    <script src="{{ asset('assets/vendor/js/helpers.js')}}"></script>
    <script src="{{ asset('assets/js/config.js')}}"></script>

</head>

<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        @if(Auth::guard('admin')->check())
            @livewire('skeleton.sidebar')
        @endif
        <div class="layout-page">
            @if(Auth::guard('admin')->check())
                @livewire('skeleton.header')
            @endif
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    {{$slot}}
                </div>
                @if(Auth::guard('admin')->check())
                    @livewire('skeleton.footer')
                @endif
            </div>
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
<script src="{{ asset('assets/vendor/libs/notyf/notyf.js') }}"></script>
@stack('script')
<script src="{{ asset('assets/js/main.js')}}"></script>
<script>
    const notyf=new Notyf({duration:3000,position:{x:'right',y:'bottom'}});document.addEventListener('livewire:init',()=>{Livewire.on('notify',(event)=>{if(event.type==='success'){notyf.success(event.message)}
        if(event.type==='error'){notyf.error(event.message)}})})
</script>
</body>
</html>

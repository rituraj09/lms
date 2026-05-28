<?php

use Livewire\Component;

new class extends Component {
    use \App\Traits\WithAdmin;
};
?>

<div>
    <aside id="layout-menu" class="layout-menu menu-vertical menu mt-2">
        <div class="app-brand demo">
            <a href="#" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <span class="text-primary">
                        <img class="w-75 rounded" src="{{ asset('assets/img/favicon/favicon.png') }}" alt="" />
                    </span>
                </span>
                <span class="app-brand-text demo menu-text fw-light ms-2 fs-5">{{ config('app.name') }}</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
                        fill-opacity="0.9" />
                    <path
                        d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
                        fill-opacity="0.4" />
                </svg>
            </a>
        </div>

        <div class="menu-inner-shadow"></div>
        <ul class="menu-inner pt-2 pb-10 ps ps--active-y">

            <li class="menu-item">
                <a href="{{ route('admin.home') }}" class="menu-link">
                    <i class="menu-icon icon-base ri ri-dashboard-line"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
            <li class="menu-header small mt-5">
                <span class="menu-header-text" data-i18n="Admin Corner">Admin Corner</span>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-info-card-line"></i>
                    <div data-i18n="Master">Master</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.organisation') }}" class="menu-link">
                            <div data-i18n="Organisation">Organisation</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.designation') }}" class="menu-link">
                            <div data-i18n="Designation">Designation</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.role') }}" class="menu-link">
                            <div data-i18n="Role">Role</div>
                        </a>
                    </li>
                </ul>
            </li>


        </ul>
    </aside>
</div>

@push('script')
    <script>
        $(document).ready(function() {

            let currentUrl = window.location.href;

            // Check all sidebar links
            $('.menu-link').each(function() {
                let linkUrl = $(this).attr('href');

                if (
                    linkUrl &&
                    linkUrl !== "#" &&
                    linkUrl !== "javascript:void(0);" &&
                    currentUrl.includes(linkUrl)
                ) {
                    let currentItem = $(this).closest('.menu-item');

                    // Add active class to current menu item
                    currentItem.addClass('active');

                    // Open all parent dropdowns
                    currentItem.parents('.menu-item').addClass('open active');

                    // Optional: show submenu if hidden
                    currentItem.parents('.menu-sub').show();
                }
            });

        });
    </script>
@endpush

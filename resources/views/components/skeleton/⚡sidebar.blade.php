<?php
//views/skeleton/sidebar.blade.php
use Livewire\Component;

new class extends Component {
    use \App\Traits\WithAdmin;
};
?>

<div>
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme shadow-sm" style="margin-top: 0;">

        {{-- Brand Logo --}}
        <div class="app-brand border-bottom px-4 py-3 d-flex align-items-center justify-content-between">

            <a href="{{ route('admin.home') }}" class="app-brand-link d-flex align-items-center gap-2">

                {{-- Logo Icon — always visible --}}
                <span class="app-brand-logo flex-shrink-0">
                    <img style="width: 36px; height: 36px; object-fit: contain;" class="rounded"
                        src="{{ asset('assets/img/favicon/favicon.png') }}" alt="{{ config('app.name') }}" />
                </span>

                {{-- Brand Text — hidden when collapsed --}}
                <div class="app-brand-text-group d-flex flex-column lh-sm">
                    <span class="app-brand-text fw-bold fs-6 text-primary text-nowrap">
                        {{ config('app.name') }}
                    </span>
                    <span class="app-brand-subtext text-muted text-nowrap"
                        style="font-size: 0.53rem; letter-spacing: 0.02em;">
                        Comprehensive Intelligence Framework
                    </span>
                </div>

            </a>

            {{-- Collapse Toggle Arrow --}}
            <a href="javascript:void(0);"
                class="layout-menu-toggle menu-link text-large ms-auto d-xl-flex d-none flex-shrink-0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"
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

        {{-- Navigation --}}
        <ul class="menu-inner pt-1 pb-10 ps ps--active-y">

            {{-- ==================== DASHBOARD ==================== --}}
            <li class="menu-item">
                <a href="{{ route('admin.home') }}" class="menu-link">
                    <i class="menu-icon icon-base ri ri-dashboard-line"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            {{-- ==================== LEARNING MANAGEMENT ==================== --}}
            <li class="menu-header small mt-4">
                <span class="menu-header-text text-uppercase"
                    style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                    Learning Management
                </span>
            </li>

            {{-- Courses --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-book-open-line"></i>
                    <div>Courses</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>All Courses</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Create Course</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Categories</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Archived Courses</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Modules & Lessons --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-layout-masonry-line"></i>
                    <div>Modules & Lessons</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>All Modules</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>All Lessons</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Enrollments --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-user-received-line"></i>
                    <div>Enrollments</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>All Enrollments</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Pending Approvals</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Completed</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ==================== ASSESSMENT & EVALUATION ==================== --}}
            <li class="menu-header small mt-4">
                <span class="menu-header-text text-uppercase"
                    style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                    Assessment & Evaluation
                </span>
            </li>

            {{-- Question Bank --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-question-answer-line"></i>
                    <div>Question Bank</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.question-groups') }}" class="menu-link">
                            <div>Question Groups</div>
                        </a>
                    </li>

                </ul>
            </li>

            {{-- Assessments --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                    <div>Assessments</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.assessments.index') }}" class="menu-link">
                            <div>All Assessments</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Results & Scores</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Certificates --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-medal-line"></i>
                    <div>Certificates</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>All Certificates</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Templates</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ==================== USER MANAGEMENT ==================== --}}
            <li class="menu-header small mt-4">
                <span class="menu-header-text text-uppercase"
                    style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                    User Management
                </span>
            </li>

            {{-- Students --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-graduation-cap-line"></i>
                    <div>Students</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>All Students</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Import Students</div>
                        </a>
                    </li>

                </ul>
            </li>


            {{-- Administrators --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-admin-line"></i>
                    <div>Administrators</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{#" class="menu-link">
                            <div>All Admins</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Roles & Permissions</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ==================== ORGANIZATION ==================== --}}
            <li class="menu-header small mt-4">
                <span class="menu-header-text text-uppercase"
                    style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                    Organization
                </span>
            </li>

            {{-- Master Data --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-building-2-line"></i>
                    <div>Organization Master</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.organisation') }}" class="menu-link">
                            <div>Organisations</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.designation') }}" class="menu-link">
                            <div>Designations</div>
                        </a>
                    </li>

                </ul>
            </li>

            {{-- ==================== REPORTS & ANALYTICS ==================== --}}
            <li class="menu-header small mt-4">
                <span class="menu-header-text text-uppercase"
                    style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                    Reports & Analytics
                </span>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-bar-chart-box-line"></i>
                    <div>Reports</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Overview</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Course Progress</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Assessment Reports</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>User Activity</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Export Reports</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ==================== COMMUNICATION ==================== --}}
            <li class="menu-header small mt-4">
                <span class="menu-header-text text-uppercase"
                    style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                    Communication
                </span>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-mail-send-line"></i>
                    <div>Announcements</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{#" class="menu-link">
                            <div>All Announcements</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Create Announcement</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-discuss-line"></i>
                    <div>Forums & Discussion</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>All Forums</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Moderation Queue</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ==================== SYSTEM SETTINGS ==================== --}}
            <li class="menu-header small mt-4">
                <span class="menu-header-text text-uppercase"
                    style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                    System Settings
                </span>
            </li>

            {{-- General Settings --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-settings-3-line"></i>
                    <div>General Settings</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{#" class="menu-link">
                            <div>Application Settings</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Branding & Logo</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Localization</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Maintenance Mode</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Email & Notifications --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-mail-settings-line"></i>
                    <div>Email & Notifications</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Email Configuration</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Email Templates</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Notification Rules</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Security --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-shield-keyhole-line"></i>
                    <div>Security</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Security Settings</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Password Policy</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Two-Factor Auth</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div>Audit Logs</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Payment & Billing --}}
            {{-- <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-bank-card-line"></i>
                    <div>Payment & Billing</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.payment-gateways') ?? '#' }}" class="menu-link">
                            <div>Payment Gateways</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.invoices') ?? '#' }}" class="menu-link">
                            <div>Invoice Settings</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.tax') ?? '#' }}" class="menu-link">
                            <div>Tax Configuration</div>
                        </a>
                    </li>
                </ul>
            </li> --}}

            {{-- Integrations --}}
            {{-- <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-plug-line"></i>
                    <div>Integrations</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.integrations.api') ?? '#' }}" class="menu-link">
                            <div>API Keys</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.integrations.sso') ?? '#' }}" class="menu-link">
                            <div>SSO / OAuth</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.integrations.storage') ?? '#' }}" class="menu-link">
                            <div>Cloud Storage</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.integrations.zoom') ?? '#' }}" class="menu-link">
                            <div>Video Conferencing</div>
                        </a>
                    </li>
                </ul>
            </li> --}}

            {{-- System Tools --}}
            {{-- <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ri ri-tools-line"></i>
                    <div>System Tools</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.cache') ?? '#' }}" class="menu-link">
                            <div>Cache Management</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.backup') ?? '#' }}" class="menu-link">
                            <div>Backup & Restore</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.queue-monitor') ?? '#' }}" class="menu-link">
                            <div>Queue Monitor</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.settings.system-info') ?? '#' }}" class="menu-link">
                            <div>System Information</div>
                        </a>
                    </li>
                </ul>
            </li> --}}

            {{-- Bottom Spacer --}}
            {{-- <li class="menu-item mt-4">
                <div class="px-4 py-3 mx-2 rounded-3"
                    style="background: rgba(105,108,255,0.08); border: 1px dashed rgba(105,108,255,0.3);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="ri ri-customer-service-2-line text-primary icon-18px"></i>
                        <small class="fw-semibold text-primary">Need Help?</small>
                    </div>
                    <p class="text-muted mb-2" style="font-size: 0.72rem;">
                        Check documentation or contact support.
                    </p>
                    <a href="#" class="btn btn-primary btn-sm w-100" style="font-size: 0.75rem;">
                        <i class="ri ri-lifebuoy-line me-1"></i> Get Support
                    </a>
                </div>
            </li> --}}

        </ul>
    </aside>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            let currentUrl = window.location.href;

            $('.menu-link').each(function() {
                let linkUrl = $(this).attr('href');

                if (
                    linkUrl &&
                    linkUrl !== '#' &&
                    linkUrl !== 'javascript:void(0);' &&
                    currentUrl.includes(linkUrl)
                ) {
                    let currentItem = $(this).closest('.menu-item');
                    currentItem.addClass('active');
                    currentItem.parents('.menu-item').addClass('open active');
                    currentItem.parents('.menu-sub').show();
                }
            });

            // Tooltip initialization
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush

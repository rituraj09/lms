<?php
// views/components/skeleton/sidebar.blade.php
use Livewire\Component;

new class extends Component {
    use \App\Traits\WithAdmin;

    public function exitOrganisation(): void
    {
        \App\Services\OrganisationContext::clear();
        auth('admin')->user()->setCurrentOrganisation(null);
        $this->redirect(route('admin.home'), navigate: true);
    }
};
?>

<div>
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme shadow-sm" style="margin-top: 0;">

        @php
            $authAdmin = auth('admin')->user();
            $activeOrg = \App\Services\OrganisationContext::get();
            $isSuperAdmin = $authAdmin?->isSuperAdmin() ?? false;

            // Helper: check permission OR super admin
            $can = fn(string $perm) => $isSuperAdmin || $authAdmin?->can($perm);
            $canAny = fn(array $perms) => $isSuperAdmin || collect($perms)->some(fn($p) => $authAdmin?->can($p));
        @endphp

        {{-- ============================================ --}}
        {{-- BRAND LOGO                                   --}}
        {{-- ============================================ --}}
        <div class="app-brand border-bottom px-4 py-3 d-flex align-items-center justify-content-between">

            @if ($activeOrg)
                {{-- Organisation Mode Brand --}}
                <a href="{{ route('admin.organisations.dashboard', $activeOrg->id) }}"
                    class="app-brand-link d-flex align-items-center gap-2" >
                    <span class="app-brand-logo flex-shrink-0">
                        @if ($activeOrg->logo)
                            <img style="width: 36px; height: 36px; object-fit: cover;"
                                class="rounded-circle border border-2 border-primary"
                                src="{{ asset('storage/' . $activeOrg->logo) }}" alt="{{ $activeOrg->name }}" />
                        @else
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--bs-primary);"
                                class="d-flex align-items-center justify-content-center text-white fw-bold">
                                {{ strtoupper(substr($activeOrg->name, 0, 2)) }}
                            </div>
                        @endif
                    </span>
                    <div class="app-brand-text-group d-flex flex-column lh-sm">
                        <span class="app-brand-text fw-bold fs-6 text-primary text-nowrap"
                            style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $activeOrg->name }}
                        </span>
                        <span class="app-brand-subtext text-muted text-nowrap"
                            style="font-size: 0.53rem; letter-spacing: 0.02em;">
                            {{ $activeOrg->code }} · Organisation Mode
                        </span>
                    </div>
                </a>
            @else
                {{-- Normal Mode Brand --}}
                <a href="{{ route('admin.home') }}" class="app-brand-link d-flex align-items-center gap-2"
                    >
                    <span class="app-brand-logo flex-shrink-0">
                        <img style="width: 36px; height: 36px; object-fit: contain;" class="rounded"
                            src="{{ asset('assets/img/favicon/favicon.png') }}" alt="{{ config('app.name') }}" />
                    </span>
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
            @endif

            {{-- Collapse Toggle --}}
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

        {{-- ============================================ --}}
        {{-- NAVIGATION                                   --}}
        {{-- ============================================ --}}
        <ul class="menu-inner pt-1 pb-10 ps ps--active-y">

            @if ($activeOrg)

                {{-- ======================================== --}}
                {{-- ORGANISATION MODE MENU                   --}}
                {{-- ======================================== --}}

                {{-- Organisation Info Banner --}}
                <li class="menu-item">
                    <div class="px-3 py-3 mx-2 mb-2 rounded-3"
                        style="background: rgba(105,108,255,0.08); border: 1px solid rgba(105,108,255,0.2);">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-label-primary" style="font-size: 0.62rem;">
                                <i class="ri ri-building-line me-1"></i>ACTIVE
                            </span>
                        </div>
                        <p class="fw-semibold mb-0 text-primary" style="font-size: 0.8rem;">
                            {{ $activeOrg->name }}
                        </p>
                        <p class="text-muted mb-0" style="font-size: 0.68rem;">
                            {{ $activeOrg->organisationType?->name ?? 'Organisation' }}
                            · {{ $activeOrg->students()->count() }} Students
                        </p>
                    </div>
                </li>

                {{-- Home / Exit --}}
                <li class="menu-item">
                    <a href="{{ route('admin.home') }}" class="menu-link" wire:click.prevent="exitOrganisation">
                        <i class="menu-icon icon-base ri ri-arrow-left-circle-line"></i>
                        <div>Home</div>
                        <span class="badge bg-label-secondary ms-auto" style="font-size: 0.6rem;">Exit</span>
                    </a>
                </li>

                {{-- Org Dashboard --}}
                <li class="menu-item">
                    <a href="{{ route('admin.organisations.dashboard', $activeOrg->id) }}" class="menu-link"
                        >
                        <i class="menu-icon icon-base ri ri-dashboard-line"></i>
                        <div>Dashboard</div>
                    </a>
                </li>

                {{-- Students --}}
                @if ($can('student.view'))
                    <li class="menu-header small mt-4">
                        <span class="menu-header-text text-uppercase"
                            style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                            Students
                        </span>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ri ri-graduation-cap-line"></i>
                            <div>Students</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="#" class="menu-link" >
                                    <div>All Students</div>
                                </a>
                                {{-- <a href="{{ route('admin.organisations.students', $activeOrg->id) }}" class="menu-link"
                                    >
                                    <div>All Students</div>
                                </a> --}}
                            </li>
                            @if ($can('student.create'))
                                <li class="menu-item">
                                    <a href="#" class="menu-link">
                                        <div>Add Student</div>
                                    </a>
                                </li>
                            @endif
                            @if ($can('student.import'))
                                <li class="menu-item">
                                    <a href="#" class="menu-link">
                                        <div>Import Students</div>
                                    </a>
                                </li>
                            @endif
                            @if ($can('student.transfer'))
                                <li class="menu-item">
                                    <a href="#" class="menu-link">
                                        <div>Transfer Student</div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- Learning --}}
                <li class="menu-header small mt-4">
                    <span class="menu-header-text text-uppercase"
                        style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                        Learning
                    </span>
                </li>

                @if ($can('course.assign'))
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ri ri-book-open-line"></i>
                            <div>Courses</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div>Assigned Courses</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div>Assign Course</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($can('assessment.assign'))
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                            <div>Assessments</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div>Assigned Assessments</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div>Assign Assessment</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div>Results & Scores</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Reports --}}
                @if ($can('report.view'))
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
                            @if ($can('report.export'))
                                <li class="menu-item">
                                    <a href="#" class="menu-link">
                                        <div>Export Reports</div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- Logs & Settings --}}
                @if ($can('activity.view'))
                    <li class="menu-header small mt-4">
                        <span class="menu-header-text text-uppercase"
                            style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                            Logs & Settings
                        </span>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <i class="menu-icon icon-base ri ri-history-line"></i>
                            <div>Activity Logs</div>
                        </a>
                    </li>
                @endif

                @if ($can('organisation.settings'))
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ri ri-settings-3-line"></i>
                            <div>Org Settings</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div>General</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('admin.organisations.edit', $activeOrg->id) }}" class="menu-link"
                                    >
                                    <div>Edit Organisation</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Switch Organisation --}}
                @if ($isSuperAdmin)
                    @php $adminOrgs = \App\Models\Master\Organisation::active()->limit(5)->get(); @endphp
                @else
                    @php $adminOrgs = $authAdmin->organisations; @endphp
                @endif

                @if ($adminOrgs->count() > 1)
                    <li class="menu-header small mt-4">
                        <span class="menu-header-text text-uppercase"
                            style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                            Switch Organisation
                        </span>
                    </li>
                    @foreach ($adminOrgs as $org)
                        @if ($org->id !== $activeOrg->id)
                            <li class="menu-item">
                                <a href="{{ route('admin.organisations.dashboard', $org->id) }}" class="menu-link"
                                    >
                                    <span class="menu-icon">
                                        <i class="ri ri-building-line"></i>
                                    </span>
                                    <div
                                        style="font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $org->name }}
                                    </div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @else
                {{-- ======================================== --}}
                {{-- NORMAL ADMIN MODE MENU                   --}}
                {{-- ======================================== --}}

                {{-- Dashboard --}}
                <li class="menu-item">
                    <a href="{{ route('admin.home') }}" class="menu-link" >
                        <i class="menu-icon icon-base ri ri-dashboard-line"></i>
                        <div>Dashboard</div>
                    </a>
                </li>

                {{-- ==================== LEARNING MANAGEMENT ==================== --}}
                @if ($canAny(['course.view', 'course.create']))
                    <li class="menu-header small mt-4">
                        <span class="menu-header-text text-uppercase"
                            style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                            Learning Management
                        </span>
                    </li>

                    @if ($can('course.view'))
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
                                @if ($can('course.create'))
                                    <li class="menu-item">
                                        <a href="#" class="menu-link">
                                            <div>Create Course</div>
                                        </a>
                                    </li>
                                @endif
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
                    @endif

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
                @endif

                {{-- ==================== ASSESSMENT & EVALUATION ==================== --}}
                @if ($canAny(['question.view', 'assessment.view']))
                    <li class="menu-header small mt-4">
                        <span class="menu-header-text text-uppercase"
                            style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                            Assessment & Evaluation
                        </span>
                    </li>

                    @if ($can('question.view'))
                        <li class="menu-item">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon icon-base ri ri-question-answer-line"></i>
                                <div>Question Bank</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item">
                                    <a href="{{ route('admin.manage-questions') }}" class="menu-link">
                                        <div>Manage Questions</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if ($can('assessment.view'))
                        <li class="menu-item">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                                <div>Assessments</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item">
                                    <a href="{{ route('admin.assessments.index') }}" class="menu-link">
                                        <div>Manage Assessments</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="#" class="menu-link">
                                        <div>Results & Scores</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

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
                @endif

                {{-- ==================== USER MANAGEMENT ==================== --}}
                @if ($canAny(['student.view', 'admin.view', 'role.view']))
                    <li class="menu-header small mt-4">
                        <span class="menu-header-text text-uppercase"
                            style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                            User Management
                        </span>
                    </li>

                    @if ($can('student.view'))
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
                                @if ($can('student.import'))
                                    <li class="menu-item">
                                        <a href="#" class="menu-link">
                                            <div>Import Students</div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if ($can('admin.view'))
                        <li class="menu-item">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon icon-base ri ri-admin-line"></i>
                                <div>Administrators</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item">
                                    <a href="{{ route('admin.admins.index') }}" class="menu-link" >
                                        <div>All Admins</div>
                                    </a>
                                </li>
                                @if ($can('role.view'))
                                    <li class="menu-item">
                                        <a href="{{ route('admin.roles.index') }}" class="menu-link">
                                            <div>Roles & Permissions</div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                {{-- ==================== ORGANISATION ==================== --}}
                @if ($can('organisation.view'))
                    <li class="menu-header small mt-4">
                        <span class="menu-header-text text-uppercase"
                            style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                            Organization
                        </span>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ri ri-building-2-line"></i>
                            <div>Organization Master</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ route('admin.organisations.index') }}" class="menu-link">
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
                @endif

                {{-- ==================== REPORTS & ANALYTICS ==================== --}}
                @if ($can('report.view'))
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
                            @if ($can('report.export'))
                                <li class="menu-item">
                                    <a href="#" class="menu-link">
                                        <div>Export Reports</div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

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
                            <a href="#" class="menu-link">
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
                @if ($can('settings.view'))
                    <li class="menu-header small mt-4">
                        <span class="menu-header-text text-uppercase"
                            style="font-size: 0.68rem; letter-spacing: 0.08em; font-weight: 700;">
                            System Settings
                        </span>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ri ri-settings-3-line"></i>
                            <div>General Settings</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="#" class="menu-link">
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
                @endif

            @endif

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
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush

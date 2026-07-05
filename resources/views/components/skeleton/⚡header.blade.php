<?php
//views/skeleton/header.blade.php
use App\Traits\WithAdmin;
use Livewire\Component;
use App\Services\ActivityLogger;

new class extends Component {
    use WithAdmin;

    public $notifications;
    public bool $darkMode;

    public function logout()
    {
        $admin = Auth::guard('admin')->user();
        // ✅ Log before logout so we still have user id
        ActivityLogger::log(
            userId:   $admin->id,
            userType: 'admin',
            action:   'logout',
            extra: [
                'description' => "Admin logged out: {$admin->email}",
            ]
        );

        Auth::guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->to(route('admin.login'));
    }

    public function mount()
    {
        $this->notifications = collect();
        $this->darkMode = session()->get('dark_mode', false);
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session()->put('dark_mode', $this->darkMode);
        $this->dispatch('theme-changed', theme: $this->darkMode ? 'dark' : 'light');
    }

    public function switchRole($id)
    {
        $this->admin->update(['current_role_id' => $id]);
        return redirect()->route('admin.home');
    }
};
?>

<div>
    <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme shadow-sm"
        id="layout-navbar">

        {{-- Mobile Menu Toggle --}}
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="icon-base ri ri-menu-line icon-22px"></i>
            </a>
        </div>

        {{-- Brand for Mobile --}}
        <div class="navbar-brand-mobile d-xl-none me-auto">
            <span class="fw-semibold text-primary fs-5">{{ config('app.name') }}</span>
        </div>

        {{-- Right Navbar --}}
        <div class="navbar-nav-right d-flex align-items-center justify-content-end w-100" id="navbar-collapse">

            {{-- Search Bar --}}
            <div class="navbar-search d-none d-md-flex align-items-center me-auto ms-3">
                <div class="input-group input-group-sm" style="min-width: 260px;">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="ri ri-search-line text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 bg-transparent ps-0"
                        placeholder="Search menu, users, courses...">
                </div>
            </div>

            <ul class="navbar-nav flex-row align-items-center gap-1 ms-md-3">

                {{-- Dark Mode Toggle --}}
                <li class="nav-item">
                    <a href="#" wire:click.prevent="toggleDarkMode"
                        class="nav-link btn btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip"
                        title="{{ $darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode' }}">
                        @if ($darkMode)
                            <i class="icon-base ri ri-sun-line icon-22px text-warning"></i>
                        @else
                            <i class="icon-base ri ri-moon-line icon-22px"></i>
                        @endif
                    </a>
                </li>

                {{-- Notifications --}}
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill position-relative"
                        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false">
                        <i class="icon-base ri ri-notification-2-line icon-22px"></i>
                        @if ($this->notifications->count() > 0)
                            <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger"
                                style="font-size:0.6rem">
                                {{ $this->notifications->count() > 99 ? '99+' : $this->notifications->count() }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end py-0 shadow" style="width: 380px; max-width: 95vw;">
                        {{-- Header --}}
                        <div
                            class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom bg-label-primary rounded-top">
                            <div>
                                <h6 class="mb-0 fw-semibold">Notifications</h6>
                                <small class="text-muted">
                                    {{ $this->notifications->count() }} unread message(s)
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                                    data-bs-toggle="tooltip" title="Mark all as read">
                                    <i class="ri ri-mail-open-line icon-18px"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                                    data-bs-toggle="tooltip" title="Settings">
                                    <i class="ri ri-settings-3-line icon-18px"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Notification List --}}
                        <div class="dropdown-notifications-list" style="max-height: 380px; overflow-y: auto;">
                            <ul class="list-group list-group-flush">
                                @forelse ($notifications as $v)
                                    <li
                                        class="list-group-item list-group-item-action dropdown-notifications-item px-4 py-3">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar avatar-sm flex-shrink-0">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    <i class="ri ri-notification-2-fill icon-16px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h6 class="small mb-1 fw-semibold text-truncate">{{ $v->code }}
                                                </h6>
                                                <small class="d-block text-body mb-1">
                                                    Applied by <strong>{{ $v->user->name }}</strong>
                                                </small>
                                                <small class="text-body-secondary">
                                                    <i class="ri ri-time-line me-1"></i>
                                                    {{ \Carbon\Carbon::createFromDate($v->created_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                            <div class="flex-shrink-0 d-flex flex-column gap-1">
                                                <a href="javascript:void(0)"
                                                    class="btn btn-icon btn-sm btn-text-secondary rounded-pill dropdown-notifications-read"
                                                    data-bs-toggle="tooltip" title="Mark as read">
                                                    <span class="badge badge-dot bg-primary"></span>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="btn btn-icon btn-sm btn-text-secondary rounded-pill dropdown-notifications-archive"
                                                    data-bs-toggle="tooltip" title="Dismiss">
                                                    <i class="ri ri-close-line icon-16px"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center py-5">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <i class="ri ri-inbox-line icon-40px text-muted opacity-50"></i>
                                            <p class="text-muted mb-0 small">No notifications yet</p>
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Footer --}}
                        <div class="p-3 border-top">
                            <a class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2"
                                href="#">
                                <i class="ri ri-eye-line icon-16px"></i>
                                <span>View All Notifications</span>
                            </a>
                        </div>
                    </div>
                </li>

                {{-- Quick Settings Shortcut --}}
                <li class="nav-item">
                    <a href="#" class="nav-link btn btn-icon btn-text-secondary rounded-pill"
                        data-bs-toggle="tooltip" title="System Settings">
                        <i class="icon-base ri ri-settings-3-line icon-22px"></i>
                    </a>
                </li>

                {{-- Divider --}}
                <li class="nav-item d-none d-md-block">
                    <div class="vr mx-1 opacity-25" style="height: 28px;"></div>
                </li>

                {{-- User Dropdown --}}
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center gap-2 ps-2"
                        href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-sm avatar-online">
                            <img src="{{ $this->admin->details?->photo_path
                                ? asset('storage/' . $this->admin->details->photo_path)
                                : asset('assets/img/avatars/male.png') }}"
                                alt="avatar" class="rounded-circle" />
                        </div>
                        <div class="d-none d-lg-block lh-1">
                            <span class="d-block fw-semibold small">{{ $this->admin->name }}</span>
                            <span class="d-block text-muted" style="font-size: 0.72rem;">
                                {{-- ✅ Use safe accessor method --}}
                                {{ $this->admin->getPrimaryRoleName() }}
                            </span>
                        </div>
                        <i class="ri ri-arrow-down-s-line d-none d-lg-block text-muted"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end mt-2 py-0 shadow" style="min-width: 240px;">

                        {{-- User Info Header --}}
                        <li class="px-3 py-3 border-bottom rounded-top">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ $this->admin->details?->photo_path
                                        ? asset('storage/' . $this->admin->details->photo_path)
                                        : asset('assets/img/avatars/male.png') }}"
                                        alt="avatar" class="w-px-40 h-auto rounded-circle" />
                                </div>
                                <div class="lh-sm overflow-hidden">
                                    <h6 class="mb-0 fw-semibold text-truncate">{{ $this->admin->name }}</h6>
                                    <small class="text-muted text-truncate d-block">
                                        {{ $this->admin->email ?? 'No Email' }}
                                    </small>
                                    <span class="badge bg-label-primary mt-1" style="font-size:0.65rem;">
                                        {{-- ✅ Safe method --}}
                                        {{ $this->admin->getPrimaryRoleName() }}
                                    </span>
                                </div>
                            </div>
                        </li>

                        {{-- Profile & Account --}}
                        <li class="px-2 pt-2">
                            <p class="text-uppercase text-muted px-2 mb-1"
                                style="font-size: 0.65rem; letter-spacing: 0.08em; font-weight: 600;">
                                Account
                            </p>
                        </li>

                        <li>
                            <a class="dropdown-item rounded-2 mx-1 px-3 py-2" href="#">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="icon-avatar bg-label-info">
                                        <i class="ri ri-user-3-line"></i>
                                    </span>
                                    <span>My Profile</span>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item rounded-2 mx-1 px-3 py-2" href="#"
                                wire:click="$dispatch('show-change-password-modal')">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="icon-avatar bg-label-warning">
                                        <i class="ri ri-lock-password-fill"></i>
                                    </span>
                                    <span>Change Password</span>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item rounded-2 mx-1 px-3 py-2" href="#">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="icon-avatar bg-label-secondary">
                                        <i class="ri ri-history-line"></i>
                                    </span>
                                    <span>Activity Log</span>
                                </div>
                            </a>
                        </li>

                        {{-- Switch Role --}}
                        @if ($this->admin->roles->count() > 1)
                            <li>
                                <div class="dropdown-divider my-1 mx-3"></div>
                            </li>
                            <li class="px-2">
                                <p class="text-uppercase text-muted px-2 mb-1"
                                    style="font-size: 0.65rem; letter-spacing: 0.08em; font-weight: 600;">
                                    Switch Role
                                </p>
                            </li>
                            <li>
                                <div class="px-2 pb-1">
                                    <div
                                        class="d-flex align-items-center gap-2 mb-1 px-2 py-1 rounded-2 bg-label-primary">
                                        <i class="ri ri-shield-check-fill icon-14px text-primary"></i>
                                        <small class="text-primary fw-semibold">
                                            {{-- ✅ Safe --}}
                                            Active: {{ $this->admin->getPrimaryRoleName() }}
                                        </small>
                                    </div>
                                </div>
                            </li>
                            @foreach ($this->admin->roles as $role)
                                {{-- ✅ Fixed: Check against role id, not current_role_id --}}
                                @if ($this->admin->roles->first()?->id !== $role->id)
                                    <li>
                                        <a class="dropdown-item rounded-2 mx-1 px-3 py-2" href="#"
                                            wire:click="switchRole({{ $role->id }})">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="icon-avatar bg-label-success">
                                                    <i class="ri ri-shuffle-line"></i>
                                                </span>
                                                <span>Switch to {{ $role->display_name ?? $role->name }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif

                        {{-- Logout --}}
                        <li>
                            <div class="dropdown-divider my-1 mx-3"></div>
                        </li>
                        <li class="px-2 pb-2">
                            <a class="btn btn-sm btn-danger w-100 d-flex align-items-center justify-content-center gap-2"
                                href="javascript:void(0)" wire:click="logout">
                                <i class="ri ri-logout-box-r-line icon-16px"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul>
                </li>
                {{-- / User Dropdown --}}
            </ul>
        </div>
    </nav>

    {{-- @livewire('modal.change-password') --}}
</div>

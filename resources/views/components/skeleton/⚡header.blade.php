<?php

use App\Traits\WithAdmin;
use Livewire\Component;

new class extends Component {
    use WithAdmin;

    public $notifications;
    public bool $darkMode;
    public function logout()
    {
        Auth::guard('admin')->logout();

        session()->invalidate();
        session()->regenerateToken();

        // Redirect to the login page
        return redirect()->to(route('admin.login'));
    }

    public function mount()
    {
        $this->notifications = collect();

        $this->darkMode = session()->get('dark_mode', false);

    }
    public function toggleDarkMode()
    {
        $this->darkMode = ! $this->darkMode;

        session()->put('dark_mode', $this->darkMode);

        $this->dispatch(
            'theme-changed',
            theme: $this->darkMode ? 'dark' : 'light'
        );
    }
    public function switchRole($id)
    {
        $this->admin->update(['current_role_id' => $id]);
        return redirect()->route('admin.home');

    }
};
?>

<div>

    <nav
        class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
        id="layout-navbar">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="icon-base ri ri-menu-line icon-22px"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">


            <ul class="navbar-nav flex-row align-items-center ms-md-auto">

                <li class="nav-item me-4">
                    <a href="#" wire:click.prevent="toggleDarkMode" class="text-dark">

                        @if($darkMode)
                            <i class="icon-base ri ri-moon-line icon-22px"></i>
                        @else
                            <i class="icon-base ri ri-sun-line icon-22px"></i>
                        @endif

                    </a>
                </li>


                    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-4 me-xl-1">
                        <a
                            class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                            href="javascript:void(0);"
                            data-bs-toggle="dropdown"
                            data-bs-auto-close="outside"
                            aria-expanded="false">
                            <i class="icon-base ri ri-notification-2-line icon-22px"></i>
                            @if($this->notifications->count() > 0)
                                <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end py-0">
                            <li class="dropdown-menu-header border-bottom py-50">
                                <div class="dropdown-header d-flex align-items-center py-2">
                                    <h6 class="mb-0 me-auto">Notification</h6>
                                    <div class="d-flex align-items-center h6 mb-0">
                                        <span class="badge rounded-pill bg-label-primary fs-xsmall me-2">{{$this->notifications->count()}}</span>
                                        <a
                                            href="javascript:void(0)"
                                            class="dropdown-notifications-all p-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Mark all as read"
                                        ><i class="icon-base ri ri-mail-open-line text-heading"></i
                                            ></a>
                                    </div>
                                </div>
                            </li>
                            <li class="dropdown-notifications-list scrollable-container">
                                <ul class="list-group list-group-flush">
                                    @foreach($notifications as $v)
                                        <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar">
                                                        <span class="icon-base ri ri-notification-2-fill"></span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="small mb-1">{{$v->code}}</h6>
                                                    <small class="mb-1 d-block text-body">Applied by {{$v->user->name}}</small>
                                                    <small class="text-body-secondary">{{ \Carbon\Carbon::createFromDate($v->created_at)->diffForHumans() }}</small>
                                                </div>
                                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"
                                                    ><span class="badge badge-dot"></span
                                                        ></a>
                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                                    ><span class="icon-base ri ri-close-line"></span
                                                        ></a>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>


                            <li class="border-top">
                                <div class="d-grid p-4">
                                    <a class="btn btn-primary btn-sm d-flex" href="#">
                                        <small class="align-middle">View all notifications</small>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </li>


                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{is_null($this->admin->details->photo_path) ? asset('assets/img/avatars/male.png') : asset('storage/'.$this->admin->details?->photo_path)}}" alt="avatar" class="rounded-circle"/>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
                        <li>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img
                                                src="{{is_null($this->admin->details->photo_path) ? asset('assets/img/avatars/male.png') : asset('storage/'.$this->admin->details->photo_path)}}"
                                                alt="alt"
                                                class="w-px-30 h-auto rounded-circle"/>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">{{ $this->admin->name }}</h6>
                                        <small class="text-body-secondary">{{ $this->admin->currentrole->name }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="icon-base ri ri-user-3-line icon-22px me-3"></i
                                ><span class="align-middle">My Profile</span>
                            </a>
                        </li>


                        <li>
                            <a class="dropdown-item" href="#" wire:click="$dispatch('show-change-password-modal')">
                                <i class="icon-base ri ri-lock-password-fill icon-22px me-3"></i>
                                <span class="align-middle">Change Password</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <span class="align-middle">
                                    <i class="icon-base ri ri-contacts-line icon-22px me-3"></i>
                                    Logged in as {{$this->admin->currentrole->name}}
                                </span>
                            </a>
                        </li>
                        <li>
                            @foreach($this->admin->roles as $role)
                                @if($this->admin->current_role_id !== $role->id)
                                    <a class="dropdown-item" href="#" wire:click="switchRole({{$role->id}})">
                                    <span class="align-middle">
                                            <i class="icon-base ri ri-shuffle-line icon-22px me-3"></i>
                                            Switch to {{$role->name}}
                                    </span>
                                    </a>
                                @endif
                            @endforeach
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <div class="d-grid px-4 pt-2 pb-1">
                                <a class="btn btn-sm btn-danger d-flex" href="javascript:void(0)" wire:click="logout">
                                    <small class="align-middle">Logout</small>
                                    <i class="icon-base ri ri-logout-box-r-line ms-2 icon-16px"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </nav>
{{--    @livewire('modal.change-password')--}}
</div>

<div>
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="mb-1">
                        <i class="ri ri-shield-user-line me-2"></i>
                        Manage Permissions
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.admins.index') }}">Admins</a>
                            </li>
                            <li class="breadcrumb-item active">Permissions</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Admin Info Card --}}
    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-lg position-relative">
                    @if ($admin->avatar)
                        <img src="{{ asset('storage/' . $admin->avatar) }}" alt="{{ $admin->name }}"
                            class="rounded-circle" style="width: 64px; height: 64px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 64px; height: 64px; font-size: 1.5rem; font-weight: 600;">
                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                        </div>
                    @endif
                    @if ($admin->status === 'active')
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"
                            style="width: 16px; height: 16px;" title="Active"></span>
                    @endif
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">{{ $admin->name }}</h5>
                            <div class="text-muted small mb-2">
                                <i class="ri ri-mail-line me-1"></i>{{ $admin->email }}
                                @if ($admin->mobile)
                                    <span class="ms-3">
                                        <i class="ri ri-phone-line me-1"></i>{{ $admin->mobile }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                @if ($admin->roles->first())
                                    <span class="badge"
                                        style="background-color: {{ $admin->roles->first()->color }}; font-size: 0.875rem;">
                                        <i class="{{ $admin->roles->first()->icon }} me-1"></i>
                                        {{ $admin->roles->first()->display_name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Role</span>
                                @endif

                                {{-- System Permissions Count --}}
                                <span class="badge bg-info">
                                    <i class="ri ri-shield-line me-1"></i>
                                    {{ count($this->systemPermissions) }} System
                                </span>

                                {{-- Org Permissions Count --}}
                                @if ($admin->organisations->isNotEmpty())
                                    <span class="badge bg-warning">
                                        <i class="ri ri-building-line me-1"></i>
                                        {{ $admin->organisations->count() }} Orgs
                                    </span>
                                @endif

                                {{-- Total --}}
                                <span class="badge bg-success">
                                    <i class="ri ri-checkbox-multiple-line me-1"></i>
                                    {{ $totalPermissions }} Total
                                </span>
                            </div>
                        </div>

                        @if ($admin->id === auth('admin')->id())
                            <span class="badge bg-warning text-dark">
                                <i class="ri ri-user-line me-1"></i>
                                You
                            </span>
                        @endif
                    </div>

                    @if ($admin->organisations->isNotEmpty())
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted d-block mb-2">
                                <i class="ri ri-building-line me-1"></i><strong>Assigned Organisations:</strong>
                            </small>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($admin->organisations as $org)
                                    <span class="badge bg-light text-dark border border-secondary">
                                        <i class="ri ri-building-4-line me-1"></i>
                                        {{ $org->name }}
                                        <small class="ms-1">
                                            ({{ ucfirst($org->pivot->access_level) }})
                                        </small>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($admin->id === auth('admin')->id())
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="ri ri-alert-line me-2"></i>
                    <strong>Note:</strong> You are viewing your own permissions. Some actions are restricted for
                    security.
                </div>
            @endif

            @if ($admin->isSuperAdmin())
                <div class="alert alert-danger mt-3 mb-0">
                    <i class="ri ri-vip-crown-line me-2"></i>
                    <strong>Super Admin:</strong> This user has full system access and can only be modified by another
                    Super Admin.
                </div>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-checkbox-circle-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Left Sidebar: Mode Selection & Org Selector --}}
        <div class="col-lg-4 mb-4">
            <div class="card sticky-top shadow-sm" style="top: 20px;">

                {{-- ═══════════════════════════════════════════════════════ --}}
                {{-- MODE SELECTOR                                         --}}
                {{-- ═══════════════════════════════════════════════════════ --}}
                <div class="card-header bg-gradient"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="mb-0 text-white">
                        <i class="ri ri-layout-grid-fill me-2"></i>
                        Permission Modes
                    </h6>
                </div>

                <div class="card-body">
                    <div class="btn-group w-100" role="group">
                        <button type="button" wire:click="$set('mode', 'system')"
                            class="btn btn-sm {{ $mode === 'system' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="ri ri-dashboard-line me-1"></i>
                            System Mode
                        </button>
                        <button type="button" wire:click="$set('mode', 'org')"
                            class="btn btn-sm {{ $mode === 'org' ? 'btn-success' : 'btn-outline-success' }}"
                            @if ($admin->organisations->isEmpty()) disabled @endif>
                            <i class="ri ri-building-line me-1"></i>
                            Org Mode
                        </button>
                    </div>

                    @if ($admin->organisations->isEmpty() && $mode === 'org')
                        <div class="alert alert-info mt-3 mb-0 py-2">
                            <small>
                                <i class="ri ri-information-line me-1"></i>
                                Admin not assigned to any organisations
                            </small>
                        </div>
                    @endif
                </div>

                {{-- ═══════════════════════════════════════════════════════ --}}
                {{-- SYSTEM MODE PANEL                                     --}}
                {{-- ═══════════════════════════════════════════════════════ --}}
                @if ($mode === 'system')
                    <div class="card-header bg-light border-top">
                        <h6 class="mb-0">
                            <i class="ri ri-shield-line me-2 text-primary"></i>
                            System Permissions
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info py-2 mb-3 mt-4">
                            <small>
                                <i class="ri ri-information-line me-1"></i>
                                System-wide permissions apply across the entire application.
                            </small>
                        </div>

                        <div class="bg-light p-3 rounded mb-3">
                            <small class="text-muted d-block mb-2">
                                <i class="ri ri-shield-check-line me-1"></i>Total Assigned:
                            </small>
                            <h4 class="mb-0 text-primary">
                                {{ count($this->systemPermissions) }}
                            </h4>
                        </div>

                        @if (count($this->systemPermissions) > 0 && $admin->id !== auth('admin')->id())
                            <button wire:click="removeAllSystemPermissions"
                                wire:confirm="Remove all system permissions from {{ $admin->name }}?"
                                class="btn btn-sm btn-outline-danger w-100">
                                <i class="ri ri-delete-bin-line me-1"></i>
                                Remove All
                            </button>
                        @endif
                    </div>
                @endif

                {{-- ═══════════════════════════════════════════════════════ --}}
                {{-- ORGANISATION MODE PANEL                               --}}
                {{-- ═══════════════════════════════════════════════════════ --}}
                @if ($mode === 'org' && $admin->organisations->isNotEmpty())
                    <div class="card-header bg-light border-top">
                        <h6 class="mb-0">
                            <i class="ri ri-building-line me-2 text-success"></i>
                            Organisation Selection
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="ri ri-building-4-line me-1"></i>
                                Select Organisation
                            </label>
                            <select wire:model.live="selectedOrg" class="form-select form-select-sm">
                                <option value="">-- Choose Organisation --</option>
                                @foreach ($admin->organisations as $org)
                                    <option value="{{ $org->id }}">
                                        {{ $org->name }}
                                        ({{ ucfirst($org->pivot->access_level) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if ($selectedOrg)
                            @php
                                $currentOrg = $admin->organisations->find($selectedOrg);
                            @endphp

                            @if ($currentOrg)
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <div>
                                                <strong class="d-block">{{ $currentOrg->name }}</strong>
                                                <small class="text-muted">
                                                    Access Level:
                                                    <strong>{{ ucfirst($currentOrg->pivot->access_level) }}</strong>
                                                </small>
                                            </div>
                                        </div>

                                        @if ($currentOrg->pivot->assigned_at)
                                            <small class="text-muted d-block mt-2 pt-2 border-top">
                                                <i class="ri ri-calendar-line me-1"></i>
                                                Assigned:
                                                {{ \Carbon\Carbon::parse($currentOrg->pivot->assigned_at)->diffForHumans() }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded mb-3">
                                    <small class="text-muted d-block mb-2">
                                        <i class="ri ri-shield-check-line me-1"></i>Permissions Assigned:
                                    </small>
                                    <h4 class="mb-0 text-success">
                                        {{ count($this->orgPermissions) }}
                                    </h4>
                                </div>

                                @if (count($this->orgPermissions) > 0 && $admin->id !== auth('admin')->id())
                                    <button wire:click="removeAllOrgPermissions"
                                        wire:confirm="Remove all permissions for {{ $currentOrg->name }}?"
                                        class="btn btn-sm btn-outline-danger w-100">
                                        <i class="ri ri-delete-bin-line me-1"></i>
                                        Remove All
                                    </button>
                                @endif
                            @endif
                        @else
                            <div class="alert alert-warning py-2 mb-0">
                                <small>
                                    <i class="ri ri-alert-line me-1"></i>
                                    Select an organisation to manage permissions
                                </small>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>

        {{-- Right: Permissions Grid --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0">
                                <i
                                    class="me-2 ri {{ $mode === 'system' ? 'ri-shield-check-line text-primary' : 'ri-building-line text-success ' }}">
                                </i>
                                <span x-data="{ mode: @entangle('mode') }"
                                    x-text="mode === 'system' ? 'System Permissions' : 'Organisation Permissions'"></span>
                            </h6>
                            <small class="text-muted">
                                <span x-data="{ mode: @entangle('mode') }"
                                    x-text="mode === 'system' ? 'Application-wide access' : 'Assign individual organisation access'"></span>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ri ri-search-line"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    class="form-control border-start-0" placeholder="Search permissions...">
                                @if ($search)
                                    <button class="btn btn-outline-secondary" wire:click="clearSearch"
                                        type="button">
                                        <i class="ri ri-close-line"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- ═══════════════════════════════════════════════════════ --}}
                    {{-- GROUP FILTER TABS                                     --}}
                    {{-- ═══════════════════════════════════════════════════════ --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <small class="text-muted me-2">
                                <i class="ri ri-filter-line me-1"></i>Filter by group:
                            </small>
                        </div>
                        <div class="btn-group flex-wrap" role="group">
                            <button type="button" wire:click="$set('selectedGroup', 'all')"
                                class="btn btn-sm {{ $selectedGroup === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-apps-line me-1"></i>
                                All Groups
                            </button>
                            @foreach ($permissionGroups as $group)
                                <button type="button" wire:click="$set('selectedGroup', '{{ $group }}')"
                                    class="btn btn-sm {{ $selectedGroup === $group ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $group }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════════════════ --}}
                    {{-- PERMISSIONS LIST BY GROUP                             --}}
                    {{-- ═══════════════════════════════════════════════════════ --}}
                    @forelse ($filteredPermissions as $group => $permissions)
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="mb-0" :class="$mode === 'system' ? 'text-primary' : 'text-success'">
                                    <i class="ri ri-folders-line me-2"></i>
                                    {{ $group }}
                                </h6>
                                <span class="badge" :class="$mode === 'system' ? 'bg-primary' : 'bg-success'"
                                    rounded-pill>
                                    {{ $permissions->count() }}
                                </span>
                            </div>

                            <div class="row g-3">
                                @foreach ($permissions as $permission)
                                    @php
                                        $permissionId = (int) $permission->id;
                                        $isChecked =
                                            $mode === 'system'
                                                ? in_array($permissionId, $this->systemPermissions, true)
                                                : in_array($permission->name, $this->orgPermissions, true);
                                        $isDisabled = $admin->id === auth('admin')->id();
                                        $borderClass = $isChecked
                                            ? ($mode === 'system'
                                                ? 'border-primary shadow-sm'
                                                : 'border-success shadow-sm')
                                            : 'border';
                                    @endphp

                                    <div class="col-md-6">
                                        <div class="card h-100 {{ $borderClass }}">
                                            <div class="card-body p-3">
                                                <div class="form-check">
                                                    @if ($mode === 'system')
                                                        <input class="form-check-input" type="checkbox"
                                                            wire:click="toggleSystemPermission({{ $permissionId }})"
                                                            {{ $isChecked ? 'checked' : '' }}
                                                            {{ $isDisabled ? 'disabled' : '' }}
                                                            id="perm-{{ $permissionId }}"
                                                            value="{{ $permissionId }}">
                                                    @else
                                                        <input class="form-check-input" type="checkbox"
                                                            wire:click="toggleOrgPermission({{ $permissionId }})"
                                                            {{ $isChecked ? 'checked' : '' }}
                                                            {{ $isDisabled || !$selectedOrg ? 'disabled' : '' }}
                                                            id="perm-{{ $permissionId }}"
                                                            value="{{ $permission->name }}">
                                                    @endif

                                                    <label
                                                        class="form-check-label w-100 {{ $isDisabled ? 'text-muted' : '' }}"
                                                        for="perm-{{ $permissionId }}">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="flex-grow-1">
                                                                <strong class="d-block mb-1">
                                                                    {{ $permission->display_name }}
                                                                </strong>
                                                                <code class="small text-muted">
                                                                    {{ $permission->name }}
                                                                </code>
                                                                @if ($permission->description)
                                                                    <small class="text-muted d-block mt-1">
                                                                        {{ $permission->description }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            <div class="ms-2">
                                                                @if ($isChecked)
                                                                    <span class="badge"
                                                                        :class="$mode === 'system' ? 'bg-primary' :
                                                                            'bg-success'">
                                                                        <i class="ri ri-check-line"></i>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="ri ri-inbox-line text-muted" style="font-size: 4rem;"></i>
                            <p class="text-muted mt-3 mb-0">
                                @if ($search)
                                    No permissions found matching "{{ $search }}"
                                @elseif ($mode === 'org' && !$selectedOrg)
                                    Please select an organisation to view permissions
                                @else
                                    No permissions available in this group
                                @endif
                            </p>
                            @if ($search)
                                <button wire:click="clearSearch" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="ri ri-close-circle-line me-1"></i>
                                    Clear Search
                                </button>
                            @endif
                        </div>
                    @endforelse
                </div>

                <div class="card-footer bg-light">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <small class="text-muted">
                                <i class="ri ri-information-line me-1"></i>
                                <strong>Mode:</strong>
                            </small>
                            <span class="badge me-2" :class="$mode === 'system' ? 'bg-primary' : 'bg-secondary'">
                                <i class="ri ri-dashboard-line"></i> System Mode
                            </span>
                            <span class="badge" :class="$mode === 'org' ? 'bg-success' : 'bg-secondary'">
                                <i class="ri ri-building-line"></i> Org Mode
                            </span>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <small class="text-muted">
                                <i class="ri ri-checkbox-multiple-line me-1"></i>
                                <strong>Total:</strong> {{ $totalPermissions }} permission(s)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Initialize Bootstrap tooltips
        document.addEventListener('livewire:navigated', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endpush

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
                        <img src="{{ asset('storage/' . $admin->avatar) }}"
                             alt="{{ $admin->name }}"
                             class="rounded-circle"
                             style="width: 64px; height: 64px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                             style="width: 64px; height: 64px; font-size: 1.5rem; font-weight: 600;">
                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                        </div>
                    @endif
                    @if ($admin->status === 'active')
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"
                              style="width: 16px; height: 16px;"
                              title="Active"></span>
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

                                <span class="badge bg-light text-dark border">
                                    <i class="ri ri-shield-check-line me-1"></i>
                                    {{ count($this->rolePermissions) }} via Role
                                </span>
                                <span class="badge bg-info">
                                    <i class="ri ri-user-star-line me-1"></i>
                                    {{ count($this->directPermissions) }} Direct
                                </span>
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
                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted d-block mb-1">
                                <i class="ri ri-building-line me-1"></i>Organisations:
                            </small>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($admin->organisations as $org)
                                    <span class="badge bg-light text-dark border-secondary">
                                        {{ $org->name }}
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
                    <strong>Note:</strong> You are viewing your own permissions. Some actions are restricted for security.
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
        {{-- Left Sidebar: Role Selection --}}
        <div class="col-lg-4 mb-4">
            <div class="card sticky-top shadow-sm" style="top: 20px;">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="mb-0 text-white">
                        <i class="ri ri-user-settings-line me-2"></i>
                        Role Assignment
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="ri ri-shield-star-line me-1"></i>
                            Primary Role
                        </label>
                        <select wire:model.live="selectedRole"
                                class="form-select"
                                @if($admin->id === auth('admin')->id() && $admin->isSuperAdmin()) disabled @endif>
                            <option value="">-- No Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">
                                    {{ $role->display_name }}
                                    @if ($role->is_system)
                                        (System)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">
                            <i class="ri ri-information-line me-1"></i>
                            Role permissions are automatically inherited
                        </small>
                    </div>

                    @if ($selectedRole)
                        @php
                            $currentRole = $roles->find($selectedRole);
                        @endphp
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; background-color: {{ $currentRole->color }};">
                                        <i class="{{ $currentRole->icon }} text-white"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $currentRole->display_name }}</strong>
                                        <small class="text-muted">{{ $currentRole->name }}</small>
                                    </div>
                                </div>

                                @if ($currentRole->description)
                                    <p class="small text-muted mb-3">
                                        {{ $currentRole->description }}
                                    </p>
                                @endif

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="ri ri-shield-check-line me-1"></i>
                                        Permissions
                                    </small>
                                    <span class="badge rounded-pill"
                                          style="background-color: {{ $currentRole->color }};">
                                        {{ $currentRole->permissions->count() }}
                                    </span>
                                </div>

                                @if ($currentRole->is_system)
                                    <div class="alert alert-info mt-2 mb-0 py-2">
                                        <small>
                                            <i class="ri ri-lock-line me-1"></i>
                                            System Role - Protected
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                @if (count($this->directPermissions) > 0 && $admin->id !== auth('admin')->id())
                    <div class="card-footer bg-light">
                        <button wire:click="removeAllDirectPermissions"
                                wire:confirm="Are you sure you want to remove all direct permissions from {{ $admin->name }}?"
                                class="btn btn-sm btn-outline-danger w-100">
                            <i class="ri ri-delete-bin-line me-1"></i>
                            Remove All Direct Permissions ({{ count($this->directPermissions) }})
                        </button>
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
                                <i class="ri ri-shield-check-line me-2 text-primary"></i>
                                Direct Permissions
                            </h6>
                            <small class="text-muted">
                                Override or supplement role permissions
                            </small>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ri ri-search-line"></i>
                                </span>
                                <input type="text"
                                       wire:model.live.debounce.300ms="search"
                                       class="form-control border-start-0"
                                       placeholder="Search permissions...">
                                @if ($search)
                                    <button class="btn btn-outline-secondary"
                                            wire:click="clearSearch"
                                            type="button">
                                        <i class="ri ri-close-line"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Group Filter Tabs --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <small class="text-muted me-2">
                                <i class="ri ri-filter-line me-1"></i>Filter by group:
                            </small>
                        </div>
                        <div class="btn-group flex-wrap" role="group">
                            <button type="button"
                                    wire:click="$set('selectedGroup', 'all')"
                                    class="btn btn-sm {{ $selectedGroup === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-apps-line me-1"></i>
                                All Groups
                            </button>
                            @foreach ($permissionGroups as $group)
                                <button type="button"
                                        wire:click="$set('selectedGroup', '{{ $group }}')"
                                        class="btn btn-sm {{ $selectedGroup === $group ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $group }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Permissions List by Group --}}
                    @forelse ($filteredPermissions as $group => $permissions)
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="text-primary mb-0">
                                    <i class="ri ri-folders-line me-2"></i>
                                    {{ $group }}
                                </h6>
                                <span class="badge bg-primary rounded-pill">
                                    {{ $permissions->count() }}
                                </span>
                            </div>

                            <div class="row g-3">
                                {{-- Find this section in your blade file and replace it --}}

                                @foreach ($permissions as $permission)
                                    @php
                                        $permissionId = (int) $permission->id; // ✅ Ensure integer
                                        $source = $this->getPermissionSource($permissionId);
                                        $isDirect = $this->isDirectPermission($permissionId);
                                        $isViaRole = in_array($permissionId, $this->rolePermissions, true);
                                        $isChecked = $isDirect; // ✅ Only check if it's a direct permission
                                        $isDisabled = $admin->id === auth('admin')->id();
                                    @endphp

                                    <div class="col-md-6">
                                        <div class="card h-100 {{ $isDirect ? 'border-info shadow-sm' : '' }} {{ $isViaRole && !$isDirect ? 'border-secondary' : 'border' }}">
                                            <div class="card-body p-3">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           wire:click="toggleDirectPermission({{ $permissionId }})"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           {{ $isDisabled ? 'disabled' : '' }}
                                                           id="perm-{{ $permissionId }}"
                                                           value="{{ $permissionId }}">
                                                    <label class="form-check-label w-100 {{ $isDisabled ? 'text-muted' : '' }}"
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

                                                                {{-- ✅ Debug info (remove in production) --}}
                                                                @if (config('app.debug'))
                                                                    <small class="text-danger d-block mt-1">
                                                                        Debug: ID={{ $permissionId }},
                                                                        Direct={{ $isDirect ? 'yes' : 'no' }},
                                                                        Role={{ $isViaRole ? 'yes' : 'no' }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            <div class="ms-2">
                                                                @if ($source === 'both')
                                                                    <span class="badge bg-success"
                                                                          data-bs-toggle="tooltip"
                                                                          title="Granted via Role + Direct">
                                        <i class="ri ri-check-double-line"></i>
                                    </span>
                                                                @elseif ($source === 'role')
                                                                    <span class="badge bg-secondary"
                                                                          data-bs-toggle="tooltip"
                                                                          title="Granted via Role Only">
                                        <i class="ri ri-shield-line"></i>
                                    </span>
                                                                @elseif ($source === 'direct')
                                                                    <span class="badge bg-info"
                                                                          data-bs-toggle="tooltip"
                                                                          title="Direct Permission Only">
                                        <i class="ri ri-user-star-line"></i>
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
                                <strong>Legend:</strong>
                            </small>
                            <span class="badge bg-secondary ms-2">
                                <i class="ri ri-shield-line"></i> Via Role
                            </span>
                            <span class="badge bg-info ms-2">
                                <i class="ri ri-user-star-line"></i> Direct
                            </span>
                            <span class="badge bg-success ms-2">
                                <i class="ri ri-check-double-line"></i> Both
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
        document.addEventListener('livewire:navigated', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endpush

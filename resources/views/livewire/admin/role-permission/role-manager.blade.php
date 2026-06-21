<div>
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

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- LIST VIEW --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if ($viewMode === 'list')
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1">
                            <i class="ri ri-shield-star-line me-2"></i>
                            Role Designations
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.home') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Roles</li>
                            </ol>
                        </nav>
                        <small class="text-muted">
                            <i class="ri ri-information-line"></i>
                            Roles are job designations with predefined default permissions.
                        </small>
                    </div>
                    @can('system.role.create')
                        <button wire:click="showCreateForm" class="btn btn-primary">
                            <i class="ri ri-add-line me-1"></i>
                            Create Role
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ri ri-search-line"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="form-control border-start-0" placeholder="Search roles...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <select wire:model.live="sortBy" class="form-select">
                            <option value="name">Sort by Name</option>
                            <option value="users_count">Sort by Users</option>
                            <option value="permissions_count">Sort by Permissions</option>
                            <option value="created_at">Sort by Created Date</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Roles Table --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Permissions</th>
                            <th>Admins</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>
                                    <code class="text-dark">{{ $role->name }}</code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-inline-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $role->color }};">
                                            <i class="{{ $role->icon }} text-white"></i>
                                        </span>
                                        <div>
                                            <h6 class="mb-0">{{ $role->display_name }}</h6>
                                            @if ($role->description)
                                                <small
                                                    class="text-muted">{{ Str::limit($role->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="ri ri-shield-check-line me-1"></i>
                                        {{ $role->permissions_count }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="ri ri-user-line me-1"></i>
                                        {{ $role->users_count }}
                                    </span>
                                </td>
                                <td>
                                    @if ($role->is_system)
                                        <span class="badge bg-warning">
                                            <i class="ri ri-lock-line me-1"></i>
                                            System
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="ri ri-check-line me-1"></i>
                                            Custom
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button wire:click="showViewDetails({{ $role->id }})"
                                            class="btn btn-outline-info" title="View" type="button">
                                            <i class="ri ri-eye-line"></i>
                                        </button>

                                        @can('system.role.edit')
                                            @if (!$role->is_system)
                                                <button wire:click="showEditForm({{ $role->id }})"
                                                    class="btn btn-outline-primary" title="Edit" type="button">
                                                    <i class="ri ri-edit-2-line"></i>
                                                </button>
                                            @endif

                                            @if (!$role->is_system)
                                                <button wire:click="deleteRole({{ $role->id }})"
                                                    wire:confirm="Are you sure you want to delete this role?"
                                                    class="btn btn-outline-danger" title="Delete" type="button">
                                                    <i class="ri ri-delete-bin-line"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ri ri-inbox-line" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <p class="mt-3 mb-0">No roles found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="card-footer">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- CREATE/EDIT FORM VIEW --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if (in_array($viewMode, ['create', 'edit']))
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i
                                class="ri {{ $viewMode === 'create' ? 'ri-add-circle-line' : 'ri-edit-circle-line' }} me-2"></i>
                            {{ $viewMode === 'create' ? 'Create New Role' : 'Edit Role' }}
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.home') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="#" wire:click.prevent="backToList">Roles</a>
                                </li>
                                <li class="breadcrumb-item active">{{ $viewMode === 'create' ? 'Create' : 'Edit' }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <button wire:click="backToList" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i>
                        Back to List
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left: Role Details --}}
            <div class="col-lg-4">
                <div class="card border h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="ri ri-information-line me-2"></i>
                            Role Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info py-2 mb-3 mt-4">
                            <small>
                                <i class="ri ri-information-line me-1"></i>
                                These permissions will be assigned by default to all users with this role.
                            </small>
                        </div>

                        {{-- Role Name --}}
                        <div class="mb-3 ">
                            <label class="form-label fw-bold">
                                Role Name <span class="text-danger">*</span>
                            </label>
                            @if ($viewMode === 'edit')
                                <input type="text" class="form-control bg-light" value="{{ $name }}"
                                    readonly>
                                <small class="text-muted">Role name cannot be changed</small>
                            @else
                                <input type="text" wire:model.blur="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="e.g., content_manager">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>

                        {{-- Display Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Display Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" wire:model.blur="display_name"
                                class="form-control @error('display_name') is-invalid @enderror"
                                placeholder="e.g., Content Manager">
                            @error('display_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea wire:model.blur="description" class="form-control" rows="3" placeholder="Role description..."></textarea>
                        </div>

                        {{-- Color & Icon --}}
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Color</label>
                                <div class="input-group">
                                    <input type="color" wire:model="color" class="form-control form-control-color"
                                        style="max-width: 50px;">
                                    <input type="text" wire:model.blur="color"
                                        class="form-control form-control-sm" placeholder="#6c757d">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="{{ $icon }}"></i>
                                    </span>
                                    <input type="text" wire:model.blur="icon" class="form-control form-control-sm"
                                        placeholder="ri-shield-line">
                                </div>
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div class="card border bg-light">
                            <div class="card-body py-2">
                                <small class="text-muted d-block mb-2">Preview:</small>
                                <div class="d-flex align-items-center gap-2">
                                    <span
                                        class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 36px; height: 36px; background-color: {{ $color }};">
                                        <i class="{{ $icon }} text-white"></i>
                                    </span>
                                    <div>
                                        <strong>{{ $display_name ?: 'Role Name' }}</strong>
                                        <small class="d-block text-muted">{{ $name ?: 'role_name' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Permissions --}}
            <div class="col-lg-8">
                <div class="card border h-100">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="ri ri-shield-check-line me-2"></i>
                                Default Permissions
                                <span class="badge bg-primary ms-2">{{ $totalSelected }} selected</span>
                            </h6>
                            <div class="btn-group btn-group-sm">
                                <button type="button" wire:click="selectAllPermissions"
                                    class="btn btn-outline-success">
                                    <i class="ri ri-check-line me-1"></i>All
                                </button>
                                <button type="button" wire:click="deselectAllPermissions"
                                    class="btn btn-outline-danger">
                                    <i class="ri ri-close-line me-1"></i>None
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        {{-- Permission Mode Tabs --}}
                        <ul class="nav nav-tabs px-3 pt-3">
                            <li class="nav-item">
                                <button type="button" wire:click="$set('permissionTab', 'system')"
                                    class="nav-link {{ $permissionTab === 'system' ? 'active' : '' }}">
                                    <i class="ri ri-dashboard-line me-1"></i>
                                    System Mode
                                    <span class="badge bg-primary ms-1">
                                        {{ count($selectedSystemPermissions) }}
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" wire:click="$set('permissionTab', 'org')"
                                    class="nav-link {{ $permissionTab === 'org' ? 'active' : '' }}">
                                    <i class="ri ri-building-line me-1"></i>
                                    Org Mode
                                    <span class="badge bg-success ms-1">
                                        {{ count($selectedOrgPermissions) }}
                                    </span>
                                </button>
                            </li>
                        </ul>

                        <div class="p-3" style="max-height: 500px; overflow-y: auto;">
                            {{-- System Permissions Tab --}}
                            @if ($permissionTab === 'system')
                                <div class="d-flex justify-content-end mb-2">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" wire:click="selectAllSystemPermissions"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="ri ri-check-line me-1"></i>All System
                                        </button>
                                        <button type="button" wire:click="deselectAllSystemPermissions"
                                            class="btn btn-outline-secondary btn-sm">
                                            <i class="ri ri-close-line me-1"></i>Clear
                                        </button>
                                    </div>
                                </div>

                                @foreach ($allSystemPermissions as $group => $permissions)
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2 pb-1 border-bottom">
                                            <h6 class="text-primary mb-0 small fw-bold">
                                                <i class="ri ri-folder-line me-1"></i>
                                                {{ $group }}
                                            </h6>
                                            <span class="badge bg-primary ms-2 rounded-pill"
                                                style="font-size: 0.65rem;">
                                                {{ count($permissions) }}
                                            </span>
                                        </div>

                                        <div class="row g-2">
                                            @foreach ($permissions as $permission)
                                                <div class="col-md-6">
                                                    <div
                                                        class="card h-100 border {{ in_array($permission['id'], $selectedSystemPermissions) ? 'border-primary bg-primary bg-opacity-10' : '' }}">
                                                        <div class="card-body p-3">
                                                            <div class="form-check ">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model.live="selectedSystemPermissions"
                                                                    value="{{ $permission['id'] }}"
                                                                    id="sys-perm-{{ $viewMode }}-{{ $permission['id'] }}">
                                                                <label class="form-check-label w-100"
                                                                    for="sys-perm-{{ $viewMode }}-{{ $permission['id'] }}">
                                                                    <span class="d-block fw-semibold"
                                                                        style="font-size: 0.8rem;">
                                                                        {{ $permission['display_name'] }}
                                                                    </span>
                                                                    <code class="text-muted"
                                                                        style="font-size: 0.7rem;">
                                                                        {{ $permission['name'] }}
                                                                    </code>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            {{-- Org Permissions Tab --}}
                            @if ($permissionTab === 'org')
                                <div class="d-flex justify-content-end mb-2">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" wire:click="selectAllOrgPermissions"
                                            class="btn btn-outline-success btn-sm">
                                            <i class="ri ri-check-line me-1"></i>All Org
                                        </button>
                                        <button type="button" wire:click="deselectAllOrgPermissions"
                                            class="btn btn-outline-secondary btn-sm">
                                            <i class="ri ri-close-line me-1"></i>Clear
                                        </button>
                                    </div>
                                </div>

                                @foreach ($allOrgPermissions as $group => $permissions)
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2 pb-1 border-bottom">
                                            <h6 class="text-success mb-0 small fw-bold">
                                                <i class="ri ri-folder-line me-1"></i>
                                                {{ $group }}
                                            </h6>
                                            <span class="badge bg-success ms-2 rounded-pill"
                                                style="font-size: 0.65rem;">
                                                {{ count($permissions) }}
                                            </span>
                                        </div>

                                        <div class="row g-2">
                                            @foreach ($permissions as $permission)
                                                <div class="col-md-6">
                                                    <div
                                                        class="card h-100 border {{ in_array($permission['id'], $selectedOrgPermissions) ? 'border-success bg-success bg-opacity-10' : '' }}">
                                                        <div class="card-body p-3">
                                                            <div class="form-check ">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model.live="selectedOrgPermissions"
                                                                    value="{{ $permission['id'] }}"
                                                                    id="org-perm-{{ $viewMode }}-{{ $permission['id'] }}">
                                                                <label class="form-check-label w-100"
                                                                    for="org-perm-{{ $viewMode }}-{{ $permission['id'] }}">
                                                                    <span class="d-block fw-semibold"
                                                                        style="font-size: 0.8rem;">
                                                                        {{ $permission['display_name'] }}
                                                                    </span>
                                                                    <code class="text-muted"
                                                                        style="font-size: 0.7rem;">
                                                                        {{ $permission['name'] }}
                                                                    </code>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="button" wire:click="{{ $viewMode === 'create' ? 'createRole' : 'updateRole' }}"
                        class="btn btn-{{ $viewMode === 'create' ? 'primary' : 'warning' }}">
                        <i class="ri ri-save-line me-1"></i>
                        {{ $viewMode === 'create' ? 'Create Role' : 'Update Role' }}
                    </button>
                    <button type="button" wire:click="backToList" class="btn btn-secondary">
                        <i class="ri ri-close-line me-1"></i>
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- VIEW DETAILS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if ($viewMode === 'view')
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="ri ri-eye-line me-2"></i>
                            View Role Details
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.home') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="#" wire:click.prevent="backToList">Roles</a>
                                </li>
                                <li class="breadcrumb-item active">View</li>
                            </ol>
                        </nav>
                    </div>
                    <button wire:click="backToList" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i>
                        Back to List
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left: Role Details --}}
            <div class="col-lg-4">
                <div class="card border h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Role Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 mt-3">
                            <small class="text-muted">Role Name</small>
                            <p class="mb-0"><code>{{ $name }}</code></p>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Display Name</small>
                            <p class="mb-0">
                                <span class="d-inline-flex align-items-center gap-2">
                                    <span
                                        class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 28px; height: 28px; background-color: {{ $color }};">
                                        <i class="{{ $icon }} text-white" style="font-size: 0.75rem;"></i>
                                    </span>
                                    <strong>{{ $display_name }}</strong>
                                </span>
                            </p>
                        </div>

                        @if ($description)
                            <div class="mb-3">
                                <small class="text-muted">Description</small>
                                <p class="mb-0">{{ $description }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                @if ($is_system)
                                    <span class="badge bg-warning">
                                        <i class="ri ri-lock-line me-1"></i>System
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="ri ri-check-line me-1"></i>Custom
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <small class="text-muted">Total Permissions</small>
                            <p class="mb-0">
                                <span class="badge bg-info">
                                    {{ count($selectedSystemPermissions) + count($selectedOrgPermissions) }}
                                </span>
                                <small class="text-muted ms-2">
                                    ({{ count($selectedSystemPermissions) }} System +
                                    {{ count($selectedOrgPermissions) }} Org)
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Permissions --}}
            <div class="col-lg-8">
                <div class="card border h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="ri ri-shield-check-line me-2"></i>
                            Assigned Default Permissions
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-tabs px-3 pt-3">
                            <li class="nav-item">
                                <button type="button" wire:click="$set('permissionTab', 'system')"
                                    class="nav-link {{ $permissionTab === 'system' ? 'active' : '' }}">
                                    <i class="ri ri-dashboard-line me-1"></i>
                                    System Mode
                                    <span class="badge bg-primary ms-1">
                                        {{ count($selectedSystemPermissions) }}
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" wire:click="$set('permissionTab', 'org')"
                                    class="nav-link {{ $permissionTab === 'org' ? 'active' : '' }}">
                                    <i class="ri ri-building-line me-1"></i>
                                    Org Mode
                                    <span class="badge bg-success ms-1">
                                        {{ count($selectedOrgPermissions) }}
                                    </span>
                                </button>
                            </li>
                        </ul>

                        <div class="p-3" style="max-height: 500px; overflow-y: auto;">
                            @if ($permissionTab === 'system')
                                @if (count($selectedSystemPermissions) > 0)
                                    @foreach ($allSystemPermissions as $group => $permissions)
                                        @php
                                            $groupPerms = array_filter(
                                                $permissions,
                                                fn($p) => in_array($p['id'], $selectedSystemPermissions),
                                            );
                                        @endphp
                                        @if (count($groupPerms) > 0)
                                            <div class="mb-3">
                                                <h6 class="text-primary small fw-bold mb-2">
                                                    <i class="ri ri-folder-line me-1"></i>{{ $group }}
                                                </h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($groupPerms as $permission)
                                                        <span
                                                            class="badge bg-primary-subtle text-primary border border-primary">
                                                            {{ $permission['display_name'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-muted text-center py-3">No system permissions assigned</p>
                                @endif
                            @endif

                            @if ($permissionTab === 'org')
                                @if (count($selectedOrgPermissions) > 0)
                                    @foreach ($allOrgPermissions as $group => $permissions)
                                        @php
                                            $groupPerms = array_filter(
                                                $permissions,
                                                fn($p) => in_array($p['id'], $selectedOrgPermissions),
                                            );
                                        @endphp
                                        @if (count($groupPerms) > 0)
                                            <div class="mb-3">
                                                <h6 class="text-success small fw-bold mb-2">
                                                    <i class="ri ri-folder-line me-1"></i>{{ $group }}
                                                </h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($groupPerms as $permission)
                                                        <span
                                                            class="badge bg-success-subtle text-success border border-success">
                                                            {{ $permission['display_name'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-muted text-center py-3">No organisation permissions assigned</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush

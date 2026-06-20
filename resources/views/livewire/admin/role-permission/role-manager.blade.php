<div>
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="mb-1">
                        <i class="ri ri-shield-star-line me-2"></i>
                        Roles Management
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Roles</li>
                        </ol>
                    </nav>
                </div>
                @can('role.create')
                    <button wire:click="openCreateModal" class="btn btn-primary">
                        <i class="ri ri-add-line me-1"></i>
                        Create Role
                    </button>
                @endcan
            </div>
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

    {{-- Search & Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="ri ri-search-line"></i>
                        </span>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               class="form-control border-start-0"
                               placeholder="Search roles...">
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
                                        <small class="text-muted">{{ Str::limit($role->description, 50) }}</small>
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
                                <button wire:click="openViewModal({{ $role->id }})"
                                        class="btn btn-outline-info"
                                        title="View"
                                        type="button">
                                    <i class="ri ri-eye-line"></i>
                                </button>

                                @if (!$role->is_system)
                                    @can('role.edit')
                                        <button wire:click="openEditModal({{ $role->id }})"
                                                class="btn btn-outline-primary"
                                                title="Edit"
                                                type="button">
                                            <i class="ri ri-edit-2-line"></i>
                                        </button>
                                    @endcan

                                    @can('role.delete')
                                        <button wire:click="deleteRole({{ $role->id }})"
                                                wire:confirm="Are you sure you want to delete this role?"
                                                class="btn btn-outline-danger"
                                                title="Delete"
                                                type="button">
                                            <i class="ri ri-delete-bin-line"></i>
                                        </button>
                                    @endcan
                                @endif
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

        {{-- Pagination --}}
        @if ($roles->hasPages())
            <div class="card-footer">
                {{ $roles->links() }}
            </div>
        @endif
    </div>

    {{-- ✅ CREATE MODAL --}}
    @if ($isCreateModalOpen)
        <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="ri ri-add-circle-line me-2"></i>
                            Create New Role
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeCreateModal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   wire:model.blur="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g., content_manager">
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   wire:model.blur="display_name"
                                   class="form-control @error('display_name') is-invalid @enderror"
                                   placeholder="e.g., Content Manager">
                            @error('display_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea wire:model.blur="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Role description..."></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Color <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="color"
                                           wire:model="color"
                                           class="form-control form-control-color"
                                           style="max-width: 60px;">
                                    <input type="text"
                                           wire:model.blur="color"
                                           class="form-control"
                                           placeholder="#6c757d">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Icon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri {{ $icon }}"></i>
                                    </span>
                                    <input type="text"
                                           wire:model.blur="icon"
                                           class="form-control"
                                           placeholder="ri ri-shield-line">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-primary mb-3">
                            <i class="ri ri-shield-check-line me-2"></i>
                            Permissions
                        </h6>

                        <div class="btn-group btn-group-sm mb-3" role="group">
                            <button type="button"
                                    wire:click="selectAllPermissions"
                                    class="btn btn-outline-success">
                                <i class="ri ri-check-line me-1"></i>
                                Select All
                            </button>
                            <button type="button"
                                    wire:click="deselectAllPermissions"
                                    class="btn btn-outline-danger">
                                <i class="ri ri-close-line me-1"></i>
                                Deselect All
                            </button>
                        </div>

                        <div style="max-height: 300px; overflow-y: auto;" class="border p-3 rounded">
                            @foreach ($allPermissions as $group => $permissions)
                                <div class="mb-3">
                                    <strong class="d-block mb-2 text-muted">{{ $group }}</strong>
                                    <div class="row g-2">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           wire:model.live="selectedPermissions"
                                                           value="{{ $permission['id'] }}"
                                                           id="perm-create-{{ $permission['id'] }}">
                                                    <label class="form-check-label"
                                                           for="perm-create-{{ $permission['id'] }}">
                                                        {{ $permission['display_name'] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="ri ri-information-line me-2"></i>
                            <strong>Selected:</strong> {{ count($selectedPermissions) }} permission(s)
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                wire:click="closeCreateModal">
                            Close
                        </button>
                        <button type="button"
                                class="btn btn-primary"
                                wire:click="createRole">
                            <i class="ri ri-save-line me-1"></i>
                            Create Role
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ✅ EDIT MODAL --}}
    @if ($isEditModalOpen)
        <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="ri ri-edit-circle-line me-2"></i>
                            Edit Role
                        </h5>
                        <button type="button" class="btn-close"
                                wire:click="closeEditModal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name (Read-only)</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $name }}"
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   wire:model.blur="display_name"
                                   class="form-control @error('display_name') is-invalid @enderror">
                            @error('display_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea wire:model.blur="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="3"></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Color</label>
                                <div class="input-group">
                                    <input type="color"
                                           wire:model="color"
                                           class="form-control form-control-color"
                                           style="max-width: 60px;">
                                    <input type="text"
                                           wire:model.blur="color"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri {{ $icon }}"></i>
                                    </span>
                                    <input type="text"
                                           wire:model.blur="icon"
                                           class="form-control">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-primary mb-3">
                            <i class="ri ri-shield-check-line me-2"></i>
                            Permissions
                        </h6>

                        <div class="btn-group btn-group-sm mb-3" role="group">
                            <button type="button"
                                    wire:click="selectAllPermissions"
                                    class="btn btn-outline-success">
                                <i class="ri ri-check-line me-1"></i>
                                Select All
                            </button>
                            <button type="button"
                                    wire:click="deselectAllPermissions"
                                    class="btn btn-outline-danger">
                                <i class="ri ri-close-line me-1"></i>
                                Deselect All
                            </button>
                        </div>

                        <div style="max-height: 300px; overflow-y: auto;" class="border p-3 rounded">
                            @foreach ($allPermissions as $group => $permissions)
                                <div class="mb-3">
                                    <strong class="d-block mb-2 text-muted">{{ $group }}</strong>
                                    <div class="row g-2">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           wire:model.live="selectedPermissions"
                                                           value="{{ $permission['id'] }}"
                                                           id="perm-edit-{{ $permission['id'] }}">
                                                    <label class="form-check-label"
                                                           for="perm-edit-{{ $permission['id'] }}">
                                                        {{ $permission['display_name'] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="ri ri-information-line me-2"></i>
                            <strong>Selected:</strong> {{ count($selectedPermissions) }} permission(s)
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                wire:click="closeEditModal">
                            Close
                        </button>
                        <button type="button"
                                class="btn btn-warning"
                                wire:click="updateRole">
                            <i class="ri ri-save-line me-1"></i>
                            Update Role
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ✅ VIEW MODAL --}}
    @if ($isViewModalOpen)
        <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="ri ri-eye-line me-2"></i>
                            View Role Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeViewModal"></button>
                    </div>

                    <div class="modal-body">
                        @if ($roleId)
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Role Name</h6>
                                    <p class="mb-0">
                                        <code class="text-dark">{{ $name }}</code>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Display Name</h6>
                                    <p class="mb-0">
                                        <span class="d-inline-flex align-items-center gap-2">
                                            <span class="d-inline-flex align-items-center justify-content-center"
                                                  style="width: 28px; height: 28px; border-radius: 50%; background-color: {{ $color }};">
                                                <i class="{{ $icon }} text-white"></i>
                                            </span>
                                            <strong>{{ $display_name }}</strong>
                                        </span>
                                    </p>
                                </div>
                            </div>

                            @if ($description)
                                <div class="mb-4">
                                    <h6 class="text-muted mb-2">Description</h6>
                                    <p class="mb-0">{{ $description }}</p>
                                </div>
                            @endif

                            <h6 class="text-primary mb-3">
                                <i class="ri ri-shield-check-line me-2"></i>
                                Assigned Permissions ({{ count($selectedPermissions) }})
                            </h6>

                            @if (count($selectedPermissions) > 0)
                                <div class="row g-2">
                                    @foreach ($allPermissions as $group => $permissions)
                                        @php
                                            $groupPermissions = array_filter($permissions, fn($p) => in_array($p['id'], $selectedPermissions));
                                        @endphp

                                        @if (count($groupPermissions) > 0)
                                            <div class="col-12">
                                                <strong class="d-block mb-2 text-muted">{{ $group }}</strong>
                                                <div class="row g-2">
                                                    @foreach ($groupPermissions as $permission)
                                                        <div class="col-md-6">
                                                            <span class="badge bg-info">
                                                                {{ $permission['display_name'] }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <hr class="my-2">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No permissions assigned</p>
                            @endif
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                wire:click="closeViewModal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

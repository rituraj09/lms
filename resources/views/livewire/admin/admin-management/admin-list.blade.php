{{-- resources/views/livewire/admin/admin-management/admin-list.blade.php --}}

<div>
    {{-- Header --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Admin Users</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admins</li>
                </ol>
            </nav>
        </div>
        @can('admin.create')
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary" wire:navigate>
                <i class="fas fa-plus me-2"></i>Add Admin
            </a>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                            placeholder="Search by name, email or mobile...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Per Page</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Admin</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Organisations</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm avatar-online">
                                        @if ($admin->avatar)
                                            <img src="{{ asset('storage/' . $admin->avatar) }}"
                                                alt="{{ $admin->name }}" class="rounded-circle">
                                        @else
                                            <div class="avatar-initials rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                style="width:32px; height:32px;">
                                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $admin->name }}</h6>
                                        <small class="text-muted">
                                            {{ $admin->details?->first_name }}
                                            {{ $admin->details?->last_name }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <small>{{ $admin->email }}</small>
                                    <small class="text-muted">{{ $admin->mobile }}</small>
                                </div>
                            </td>
                            <td>
                                @if ($admin->roles->first())
                                    <span class="badge"
                                        style="background-color: {{ $admin->roles->first()->color ?? '#6c757d' }}">
                                        <i class="{{ $admin->roles->first()->icon ?? 'fas fa-user' }} me-1 fa-xs"></i>
                                        {{ $admin->roles->first()->display_name ?? $admin->roles->first()->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Role</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($admin->organisations as $org)
                                        <span class="badge bg-light text-dark">{{ $org->name }}</span>
                                    @empty
                                        <small class="text-muted">None</small>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $admin->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($admin->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    @can('admin.edit')
                                        <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                            class="btn btn-sm btn-outline-primary" wire:navigate title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.admins.permissions', $admin->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Manage Permissions" wire:navigate>
                                            <i class="fas fa-shield-alt"></i>
                                        </a>
                                    @endcan

                                    @if ($admin->id !== auth('admin')->id())
                                        @can('admin.edit')
                                            <button wire:click="toggleStatus({{ $admin->id }})"
                                                class="btn btn-sm btn-outline-warning" title="Toggle Status">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        @endcan

                                        @can('admin.delete')
                                            @if (!$admin->isSuperAdmin())
                                                <button wire:click="deleteAdmin({{ $admin->id }})"
                                                    wire:confirm="Delete this admin?" class="btn btn-sm btn-outline-danger"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No admins found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($admins->hasPages())
            <div class="card-footer">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
</div>

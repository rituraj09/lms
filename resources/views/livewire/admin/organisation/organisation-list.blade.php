{{-- resources/views/livewire/admin/organisation/organisation-list.blade.php --}}

<div>
    {{-- Header --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Organisations</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Organisations</li>
                </ol>
            </nav>
        </div>
        @if (auth('admin')->user()->isSuperAdmin() || auth('admin')->user()->can('organisation.create'))
            <a href="{{ route('admin.organisations.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line me-2"></i>Add Organisation
            </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri ri-search-line"></i></span>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                            placeholder="Search by name, code or email...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select wire:model.live="typeFilter" class="form-select">
                        <option value="">All Types</option>
                        @foreach ($organisationTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Per Page</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="12">12</option>
                        <option value="24">24</option>
                        <option value="48">48</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button wire:click="$set('search', '')" class="btn btn-lg btn-primary p-4 w-100">
                        <i class="ri ri-search-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading --}}
    <div wire:loading.flex class="justify-content-center mb-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    {{-- Organisations --}}
    <div wire:loading.remove>
        @if ($organisations->count() > 0)
            <div class="row g-4">
                @foreach ($organisations as $org)
                    <div class="col-xl-4 col-md-6">
                        <div class="card h-100 organisation-card border-0 shadow-sm">
                            {{-- Banner --}}
                            <div class="org-banner position-relative"
                                style="height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.5rem 0.5rem 0 0; overflow:hidden;">
                                @if ($org->banner)
                                    <img src="{{ asset('storage/' . $org->banner) }}"
                                        class="w-100 h-100 object-fit-cover" alt="">
                                @endif
                                <span
                                    class="badge position-absolute top-0 end-0 m-2
                                {{ $org->status === 'active' ? 'bg-success' : ($org->status === 'inactive' ? 'bg-secondary' : 'bg-danger') }}">
                                    {{ ucfirst($org->status) }}
                                </span>
                            </div>

                            <div class="card-body">
                                {{-- Logo & Name --}}
                                <div class="d-flex align-items-center mb-3">
                                    <div class="org-logo me-3"
                                        style="z-index:100; width:60px; height:60px; border-radius:50%; overflow:hidden; border:3px solid #fff; box-shadow:0 2px 8px rgba(0,0,0,0.15); margin-top:-60px; background:#fff;">
                                        @if ($org->logo)
                                            <img src="{{ asset('storage/' . $org->logo) }}"
                                                class="w-100 h-100 object-fit-cover" alt="{{ $org->name }}">
                                        @else
                                            <div
                                                class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary text-white fw-bold fs-5">
                                                {{ strtoupper(substr($org->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $org->name }}</h6>
                                        <small class="text-muted">{{ $org->code }}</small>
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="org-info">
                                    <div class="d-flex align-items-center mb-1 text-sm text-muted">
                                        <i class="ri ri-community-line me-2 text-primary"></i>
                                        {{ $org->organisationType?->name ?? 'N/A' }}
                                    </div>
                                    @if ($org->email)
                                        <div class="d-flex align-items-center mb-1 text-sm text-muted">
                                            <i class="ri ri-mail-line me-2 text-primary"></i>
                                            {{ $org->email }}
                                        </div>
                                    @endif
                                    @if ($org->city)
                                        <div class="d-flex align-items-center mb-1 text-sm text-muted">
                                            <i class="ri ri-map-pin-line me-2 text-primary"></i>
                                            {{ $org->city }}{{ $org->district?->name ? ', ' . $org->district->name : '' }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Stats --}}
                                <div class="row g-2 mt-3">
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded text-center">
                                            <div class="fw-bold text-primary">{{ $org->students_count }}</div>
                                            <small class="text-muted">Students</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded text-center">
                                            <div class="fw-bold text-success">
                                                {{ $org->max_students === 0 ? '∞' : $org->max_students }}
                                            </div>
                                            <small class="text-muted">Max Seats</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div
                                class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                                <button wire:click="enterOrganisation({{ $org->id }})"
                                    class="btn btn-primary btn-sm mt-3">

                                    <i class="ri ri-arrow-right-s-line me-1"></i> Enter
                                </button>
                                <div class="d-flex gap-1">
                                    @can('organisation.edit')
                                        <a href="{{ route('admin.organisations.edit', $org->id) }}"
                                            class="btn btn-outline-secondary btn-sm" title="Edit">

                                            <i class="ri ri-pencil-fill"></i>
                                        </a>
                                    @endcan
                                    @can('organisation.delete')
                                        <button wire:click="deleteOrganisation({{ $org->id }})"
                                            wire:confirm="Are you sure you want to delete this organisation?"
                                            class="btn btn-outline-danger btn-sm" title="Delete">

                                            <i class="ri ri-delete-bin-fill"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $organisations->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri ri-community-line fa-4x text-muted mb-3"></i>
                    <h5>No Organisations Found</h5>
                    <p class="text-muted mb-3">
                        @if ($search || $statusFilter || $typeFilter)
                            No organisations match your search criteria.
                        @else
                            Get started by creating your first organisation.
                        @endif
                    </p>
                    @can('organisation.create')
                        @if (!$search && !$statusFilter && !$typeFilter)
                            <a href="{{ route('admin.organisations.create') }}" class="btn btn-primary">

                                <i class="ri ri-add-line me-2"></i>Create Organisation
                            </a>
                        @else
                            <button wire:click="$set('search', '')" class="btn btn-outline-secondary">
                                Clear Filters
                            </button>
                        @endif
                    @endcan
                </div>
            </div>
        @endif
    </div>
</div>

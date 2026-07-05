{{-- resources/views/livewire/admin/admin-management/admin-list.blade.php --}}
<div>

    {{-- ============================================================ --}}
    {{-- Header                                                        --}}
    {{-- ============================================================ --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Admin Users</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Admins</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            <i class="ri ri-add-line me-2"></i>Add Admin
        </a>
    </div>

    {{-- ============================================================ --}}
    {{-- Filters                                                       --}}
    {{-- ============================================================ --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ri ri-search-line"></i>
                        </span>
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

    {{-- ============================================================ --}}
    {{-- Table                                                         --}}
    {{-- ============================================================ --}}
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
                            {{-- Admin --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        @if ($admin->avatar)
                                            <img src="{{ asset('storage/' . $admin->avatar) }}"
                                                alt="{{ $admin->name }}" class="rounded-circle"
                                                style="width:40px; height:40px; object-fit:cover;">
                                        @else
                                            <div class="avatar-initials rounded-circle bg-primary text-white
                                                    d-flex align-items-center justify-content-center"
                                                style="width:40px; height:40px; font-size:13px; font-weight:600;">
                                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $admin->name }}</h6>
                                        <small class="text-muted">
                                            {{ $admin->full_name }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <small><i class="ri ri-mail-line me-1"></i>{{ $admin->email }}</small>
                                    <small class="text-muted"><i
                                            class="ri ri-phone-line me-1"></i>{{ $admin->mobile }}</small>
                                </div>
                            </td>

                            {{-- Role --}}
                            <td>
                                @if ($admin->roles->first())
                                    <span class="badge"
                                        style="background-color: {{ $admin->getPrimaryRoleColor() }}; font-size: 0.75rem;">
                                        <i class="{{ $admin->getPrimaryRoleIcon() }} me-1"></i>
                                        {{ $admin->getPrimaryRoleName() }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary" style="font-size: 0.75rem;">No Role</span>
                                @endif
                            </td>

                            {{-- Organisations --}}
                            <td>
                                @if ($admin->organisations->count())
                                    <button class="org-toggle-btn" onclick="toggleOrgs(this)" type="button"
                                        title="Click to view organisations">
                                        <i class="ri ri-building-line"></i>
                                        {{ $admin->organisations->count() }}
                                        {{ Str::plural('Org', $admin->organisations->count()) }}
                                        <span class="org-arrow">
                                            <i class="ri ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="org-badges">
                                        @foreach ($admin->organisations as $org)
                                            <span class="badge bg-light text-dark border">
                                                <i class="ri ri-building-4-line me-1"></i>
                                                {{ $org->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <small class="text-muted fst-italic">None</small>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @php
                                    $statusClass = match ($admin->status) {
                                        'active' => 'bg-success',
                                        'inactive' => 'bg-secondary',
                                        'suspended' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($admin->status) }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end align-items-center">

                                    {{-- View Details --}}
                                    <button wire:click="viewAdmin({{ $admin->id }})"
                                        class="btn btn-sm btn-outline-secondary" title="View Details">
                                        <i class="ri ri-eye-line"></i>
                                    </button>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.admins.edit', encrypt($admin->id)) }}"
                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="ri ri-edit-2-line"></i>
                                    </a>

                                    {{-- Permissions --}}
                                    <a href="{{ route('admin.admins.check-permissions', encrypt($admin->id)) }}"
                                        class="btn btn-sm btn-outline-info" title="Manage Permissions">
                                        <i class="ri ri-shield-user-fill"></i>
                                    </a>

                                    @if ($admin->id !== auth('admin')->id())
                                        {{-- Animated Toggle Status --}}
                                        <label class="toggle-switch"
                                            title="{{ $admin->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                            wire:click.prevent="toggleStatus({{ $admin->id }})">
                                            <input type="checkbox" {{ $admin->status === 'active' ? 'checked' : '' }}
                                                onclick="animateToggle(this)" readonly>
                                            <span class="toggle-slider"></span>
                                        </label>

                                        {{-- Delete --}}
                                        @if (!$admin->isSuperAdmin())
                                            <button wire:click="deleteAdmin({{ $admin->id }})"
                                                wire:confirm="Are you sure you want to delete this admin?"
                                                class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="ri ri-delete-bin-line"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="ri ri-user-search-line fs-3 d-block mb-2"></i>
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

    {{-- ============================================================ --}}
    {{-- Admin Detail Modal (Livewire powered)                         --}}
    {{-- ============================================================ --}}
    @if ($showDetailModal && $viewingAdmin)
        <div class="admin-modal-overlay" wire:click.self="closeDetailModal">
            <div class="admin-modal" role="dialog" aria-modal="true">

                {{-- Modal Header --}}
                <div class="admin-modal-header">
                    <button class="admin-modal-close" wire:click="closeDetailModal" type="button" title="Close">
                        <i class="ri ri-close-line"></i>
                    </button>

                    <div class="d-flex align-items-start gap-3">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            @if ($viewingAdmin->avatar)
                                <img src="{{ asset('storage/' . $viewingAdmin->avatar) }}"
                                    alt="{{ $viewingAdmin->name }}" class="admin-modal-avatar">
                            @else
                                <div class="admin-modal-avatar admin-modal-avatar-initials">
                                    {{ strtoupper(substr($viewingAdmin->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Name & Meta --}}
                        <div class="flex-grow-1">
                            <h5 class="mb-2 fw-bold">{{ $viewingAdmin->name }}</h5>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                <small class="text-muted">
                                    <i class="ri ri-mail-line me-1"></i>{{ $viewingAdmin->email }}
                                </small>
                                @php
                                    $statusClass = match ($viewingAdmin->status) {
                                        'active' => 'bg-success',
                                        'inactive' => 'bg-secondary',
                                        'suspended' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($viewingAdmin->status) }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @if ($viewingAdmin->roles->first())
                                    <span class="badge"
                                        style="background-color: {{ $viewingAdmin->getPrimaryRoleColor() }}; font-size: 0.75rem;">
                                        <i class="{{ $viewingAdmin->getPrimaryRoleIcon() }} me-1"></i>
                                        {{ $viewingAdmin->getPrimaryRoleName() }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary" style="font-size: 0.75rem;">No Role</span>
                                @endif

                                @if ($viewingAdmin->isSuperAdmin())
                                    <span class="badge bg-danger" style="font-size: 0.7rem;">
                                        <i class="ri ri-shield-star-line me-1"></i>Super Admin
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="admin-modal-body">

                    {{-- Personal Information --}}
                    <div class="admin-modal-section-title">
                        <i class="ri ri-user-3-line"></i>
                        Personal Information
                    </div>
                    <div class="admin-modal-grid mb-4">
                        <div class="admin-modal-info-item">
                            <label>Full Name</label>
                            <p>{{ $viewingAdmin->full_name }}</p>
                        </div>
                        <div class="admin-modal-info-item">
                            <label>Username</label>
                            <p>{{ $viewingAdmin->name }}</p>
                        </div>
                        <div class="admin-modal-info-item">
                            <label>Gender</label>
                            <p>{{ $viewingAdmin->details?->gender ? ucfirst($viewingAdmin->details->gender) : '—' }}
                            </p>
                        </div>
                        <div class="admin-modal-info-item">
                            <label>Date of Birth</label>
                            <p>{{ $viewingAdmin->details?->date_of_birth ? $viewingAdmin->details->date_of_birth->format('d M Y') : '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Contact Details --}}
                    <div class="admin-modal-section-title">
                        <i class="ri ri-contacts-line"></i>
                        Contact Details
                    </div>
                    <div class="admin-modal-grid mb-4">
                        <div class="admin-modal-info-item admin-modal-span-2">
                            <label>Email Address</label>
                            <p>{{ $viewingAdmin->email ?? '—' }}</p>
                        </div>
                        <div class="admin-modal-info-item">
                            <label>Mobile</label>
                            <p>{{ $viewingAdmin->mobile ?? '—' }}</p>
                        </div>
                        <div class="admin-modal-info-item">
                            <label>Member Since</label>
                            <p>{{ $viewingAdmin->created_at?->format('d M Y') }}</p>
                        </div>
                    </div>

                    {{-- Professional Details --}}
                    @if (
                        $viewingAdmin->details &&
                            ($viewingAdmin->details->designation ||
                                $viewingAdmin->details->qualification ||
                                $viewingAdmin->details->expertise))
                        <div class="admin-modal-section-title">
                            <i class="ri ri-briefcase-line"></i>
                            Professional Details
                        </div>
                        <div class="admin-modal-grid mb-4">
                            <div class="admin-modal-info-item">
                                <label>Designation</label>
                                <p>{{ $viewingAdmin->details->designation ?? '—' }}</p>
                            </div>
                            <div class="admin-modal-info-item">
                                <label>Qualification</label>
                                <p>{{ $viewingAdmin->details->qualification ?? '—' }}</p>
                            </div>
                            <div class="admin-modal-info-item admin-modal-span-2">
                                <label>Expertise</label>
                                <p>{{ $viewingAdmin->details->expertise ?? '—' }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Address --}}
                    @if ($viewingAdmin->details && ($viewingAdmin->details->address_line1 || $viewingAdmin->details->city))
                        <div class="admin-modal-section-title">
                            <i class="ri ri-map-pin-line"></i>
                            Address
                        </div>
                        <div class="admin-modal-grid mb-4">
                            <div class="admin-modal-info-item admin-modal-span-2">
                                <label>Address</label>
                                <p>
                                    {{ $viewingAdmin->details->address_line1 ?? '' }}
                                    @if ($viewingAdmin->details->address_line2)
                                        <br>{{ $viewingAdmin->details->address_line2 }}
                                    @endif
                                </p>
                            </div>
                            <div class="admin-modal-info-item">
                                <label>City</label>
                                <p>{{ $viewingAdmin->details->city ?? '—' }}</p>
                            </div>
                            <div class="admin-modal-info-item">
                                <label>Postal Code</label>
                                <p>{{ $viewingAdmin->details->postal_code ?? '—' }}</p>
                            </div>
                            <div class="admin-modal-info-item">
                                <label>State</label>
                                <p>{{ $viewingAdmin->details->state?->name ?? '—' }}</p>
                            </div>
                            <div class="admin-modal-info-item">
                                <label>District</label>
                                <p>{{ $viewingAdmin->details->district?->name ?? '—' }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Access & Role --}}
                    <div class="admin-modal-section-title">
                        <i class="ri ri-shield-line"></i>
                        Access & Role
                    </div>
                    <div class="admin-modal-grid mb-4">
                        <div class="admin-modal-info-item">
                            <label>Assigned Role</label>
                            <p>{{ $viewingAdmin->getPrimaryRoleName() }}</p>
                        </div>
                        <div class="admin-modal-info-item">
                            <label>Account Status</label>
                            <p>{{ ucfirst($viewingAdmin->status) }}</p>
                        </div>
                        <div class="admin-modal-info-item">
                            <label>Current Mode</label>
                            <p>
                                @if ($viewingAdmin->isInOrgMode())
                                    <span class="badge bg-info">Organisation Mode</span>
                                @else
                                    <span class="badge bg-primary">System Mode</span>
                                @endif
                            </p>
                        </div>
                        <div class="admin-modal-info-item">
                            <label>Current Organisation</label>
                            <p>{{ $viewingAdmin->currentOrganisation?->name ?? 'None' }}</p>
                        </div>
                    </div>

                    {{-- Organisations --}}
                    <div class="admin-modal-section-title">
                        <i class="ri ri-building-line"></i>
                        Organisations ({{ $viewingAdmin->organisations->count() }})
                    </div>
                    @if ($viewingAdmin->organisations->count())
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach ($viewingAdmin->organisations as $org)
                                <span class="admin-modal-org-badge">
                                    <i class="ri ri-building-4-line"></i>
                                    {{ $org->name }}
                                    @if ($org->pivot->access_level)
                                        <small
                                            class="text-muted ms-1">({{ ucfirst($org->pivot->access_level) }})</small>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted fst-italic mb-2">No organisations assigned.</p>
                    @endif

                    {{-- Bio --}}
                    @if ($viewingAdmin->details?->bio)
                        <div class="admin-modal-section-title mt-3">
                            <i class="ri ri-file-text-line"></i>
                            Bio
                        </div>
                        <p class="mb-0 text-muted">{{ $viewingAdmin->details->bio }}</p>
                    @endif

                </div>

                {{-- Modal Footer --}}
                <div class="admin-modal-footer">
                    <a href="{{ route('admin.admins.edit', encrypt($viewingAdmin->id)) }}" class="btn btn-primary btn-sm">
                        <i class="ri ri-edit-2-line me-1"></i> Edit Admin
                    </a>
                    <a href="{{ route('admin.admins.check-permissions', encrypt($viewingAdmin->id)) }}"
                        class="btn btn-outline-info btn-sm">
                        <i class="ri ri-shield-user-fill me-1"></i> Manage Permissions
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="closeDetailModal">
                        Close
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Styles                                                        --}}
    {{-- ============================================================ --}}
    @push('style')
        <style>
            /* ── Organisations Toggle ───────────────────────────── */
            .org-badges {
                display: none;
                margin-top: 6px;
                animation: fadeSlideDown 0.25s ease;
            }

            .org-badges.show {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
            }

            @keyframes fadeSlideDown {
                from {
                    opacity: 0;
                    transform: translateY(-6px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .org-toggle-btn {
                cursor: pointer;
                user-select: none;
                font-size: 0.75rem;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                border: 1px dashed #adb5bd;
                border-radius: 20px;
                padding: 2px 10px;
                color: #6c757d;
                background: transparent;
                transition: all 0.2s ease;
            }

            .org-toggle-btn:hover {
                background: #f1f3f5;
                color: #343a40;
                border-color: #6c757d;
            }

            .org-toggle-btn .org-arrow {
                transition: transform 0.25s ease;
                display: inline-block;
            }

            .org-toggle-btn.open .org-arrow {
                transform: rotate(180deg);
            }

            /* ── Animated Toggle Switch ─────────────────────────── */
            .toggle-switch {
                position: relative;
                display: inline-block;
                width: 42px;
                height: 22px;
            }

            .toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
                position: absolute;
            }

            .toggle-slider {
                position: absolute;
                cursor: pointer;
                inset: 0;
                background-color: #adb5bd;
                border-radius: 34px;
                transition: background-color 0.3s ease;
            }

            .toggle-slider:before {
                content: "";
                position: absolute;
                height: 16px;
                width: 16px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                border-radius: 50%;
                transition: transform 0.3s ease;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
            }

            .toggle-switch input:checked+.toggle-slider {
                background-color: #28a745;
            }

            .toggle-switch input:checked+.toggle-slider:before {
                transform: translateX(20px);
            }

            .toggle-switch input:disabled+.toggle-slider {
                opacity: 0.5;
                cursor: not-allowed;
            }

            .toggle-slider.toggling {
                animation: togglePulse 0.3s ease;
            }

            @keyframes togglePulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 6px rgba(40, 167, 69, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
                }
            }

            /* ── Admin Detail Modal ─────────────────────────────── */
            .admin-modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.6);
                z-index: 9999;
                /* ✅ HIGHEST Z-INDEX */
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
                backdrop-filter: blur(4px);
                animation: overlayFadeIn 0.2s ease;
                overflow-y: auto;
                /* ✅ Allow scroll */
            }

            @keyframes overlayFadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .admin-modal {
                background: #fff;
                border-radius: 16px;
                width: 100%;
                max-width: 700px;
                /* ✅ Wider modal */
                max-height: 90vh;
                overflow-y: auto;
                box-shadow: 0 24px 64px rgba(0, 0, 0, 0.22);
                animation: modalSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                display: flex;
                flex-direction: column;
                margin: auto;
                /* ✅ Center properly */
            }

            @keyframes modalSlideUp {
                from {
                    opacity: 0;
                    transform: translateY(32px) scale(0.94);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            /* Header */
            .admin-modal-header {
                position: relative;
                padding: 1.75rem 2rem 1.5rem;
                border-bottom: 1px solid #e9ecef;
                background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
                border-radius: 16px 16px 0 0;
            }

            .admin-modal-close {
                position: absolute;
                top: 1.25rem;
                right: 1.25rem;
                background: #f1f3f5;
                border: none;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                color: #6c757d;
                font-size: 1.2rem;
                z-index: 10;
            }

            .admin-modal-close:hover {
                background: #dee2e6;
                color: #212529;
                transform: rotate(90deg);
            }

            /* Avatar */
            .admin-modal-avatar {
                width: 72px;
                height: 72px;
                border-radius: 50%;
                object-fit: cover;
                border: 4px solid #fff;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            }

            .admin-modal-avatar-initials {
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: 1px;
            }

            /* Body */
            .admin-modal-body {
                padding: 1.75rem 2rem 1.5rem;
                flex: 1;
                overflow-y: auto;
            }

            /* Section Title */
            .admin-modal-section-title {
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #adb5bd;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .admin-modal-section-title::after {
                content: '';
                flex: 1;
                height: 1px;
                background: linear-gradient(to right, #dee2e6, transparent);
            }

            /* Info Grid */
            .admin-modal-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.25rem;
            }

            .admin-modal-info-item label {
                font-size: 0.7rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.6px;
                color: #adb5bd;
                margin-bottom: 4px;
                display: block;
            }

            .admin-modal-info-item p {
                font-size: 0.9rem;
                color: #212529;
                margin: 0;
                font-weight: 500;
                line-height: 1.5;
            }

            .admin-modal-span-2 {
                grid-column: span 2;
            }

            /* Org Badge */
            .admin-modal-org-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 20px;
                padding: 5px 13px;
                font-size: 0.8rem;
                color: #495057;
                font-weight: 500;
            }

            /* Footer */
            .admin-modal-footer {
                padding: 1.25rem 2rem;
                border-top: 1px solid #e9ecef;
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
                background: #fafbfc;
                border-radius: 0 0 16px 16px;
            }

            /* Scrollbar for modal */
            .admin-modal::-webkit-scrollbar {
                width: 6px;
            }

            .admin-modal::-webkit-scrollbar-track {
                background: #f1f3f5;
            }

            .admin-modal::-webkit-scrollbar-thumb {
                background: #ced4da;
                border-radius: 10px;
            }

            .admin-modal::-webkit-scrollbar-thumb:hover {
                background: #adb5bd;
            }
        </style>
    @endpush

    {{-- ============================================================ --}}
    {{-- Scripts                                                       --}}
    {{-- ============================================================ --}}
    @push('script')
        <script>
            /**
             * Toggle organisation badges visibility
             */
            function toggleOrgs(btn) {
                const orgBadges = btn.nextElementSibling;
                const isOpen = orgBadges.classList.contains('show');
                orgBadges.classList.toggle('show', !isOpen);
                btn.classList.toggle('open', !isOpen);
            }

            /**
             * Pulse animation on toggle slider
             */
            function animateToggle(input) {
                const slider = input.nextElementSibling;
                slider.classList.add('toggling');
                slider.addEventListener('animationend', () => {
                    slider.classList.remove('toggling');
                }, {
                    once: true
                });
            }

            /**
             * Escape key to close modal
             */
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const overlay = document.querySelector('.admin-modal-overlay');
                    if (overlay) {
                        @this.call('closeDetailModal');
                    }
                }
            });

            /**
             * Lock body scroll when modal is open
             */
            function handleBodyScroll() {
                const overlay = document.querySelector('.admin-modal-overlay');
                document.body.style.overflow = overlay ? 'hidden' : '';
            }

            // Watch for Livewire DOM updates
            document.addEventListener('livewire:navigated', handleBodyScroll);
            Livewire.hook('commit', ({
                component,
                commit,
                respond,
                succeed
            }) => {
                succeed(() => setTimeout(handleBodyScroll, 50));
            });
        </script>
    @endpush

</div>

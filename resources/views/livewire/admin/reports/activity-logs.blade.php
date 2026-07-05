{{-- resources/views/livewire/admin/activity-logs/index.blade.php --}}

<div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- LIST VIEW  (view = 0)                                  --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    @if($view === 0)

        {{-- ── Page Header ─────────────────────────────────── --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-semibold mb-1">Activity Logs</h4>
                <p class="text-muted small mb-0">
                    Track all admin and user activities across the system
                </p>
            </div>
            <span class="badge bg-label-secondary fs-6 px-3 py-2">
                <i class="ri ri-list-check me-1"></i>
                {{ number_format($logs->total()) }} Records
            </span>
        </div>

        {{-- ── Filter Card ──────────────────────────────────── --}}
        <div class="card mb-4 shadow-none border">
            <div class="card-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri ri-filter-3-line text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Filters</h6>
                    @php
                        $activeFilters = collect([$search, $userType, $action, $dateFrom, $dateTo])
                            ->filter()->count();
                    @endphp
                    @if($activeFilters > 0)
                        <span class="badge bg-primary rounded-pill">{{ $activeFilters }} active</span>
                    @endif
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row g-3">

                    {{-- Search --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-medium text-muted mb-1">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="ri ri-search-line text-muted"></i>
                            </span>
                            <input
                                type="text"
                                wire:model.live.debounce.400ms="search"
                                placeholder="Name, action, IP, description..."
                                class="form-control border-start-0 ps-0"
                            />
                            @if($search)
                                <button wire:click="$set('search', '')"
                                        class="btn btn-outline-secondary border-start-0" type="button">
                                    <i class="ri ri-close-line"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- User Type --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">User Type</label>
                        <select wire:model.live="userType" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                    {{-- Action --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">Action</label>
                        <select wire:model.live="action" class="form-select form-select-sm">
                            <option value="">All Actions</option>
                            @foreach($uniqueActions as $act)
                                <option value="{{ $act }}">
                                    {{ ucwords(str_replace('_', ' ', $act)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">From Date</label>
                        <input type="date" wire:model.live="dateFrom"
                               class="form-control form-control-sm"/>
                    </div>

                    {{-- Date To --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">To Date</label>
                        <input type="date" wire:model.live="dateTo"
                               class="form-control form-control-sm"/>
                    </div>

                    {{-- Per Page --}}
                    <div class="col-12 col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">Per Page</label>
                        <div class="d-flex gap-2">
                            <select wire:model.live="perPage" class="form-select form-select-sm">
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            @if($activeFilters > 0)
                                <button wire:click="clearFilters"
                                        class="btn btn-sm btn-outline-danger" type="button"
                                        title="Clear all filters">
                                    <i class="ri ri-refresh-line"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- Active Filter Badges --}}
            @if($activeFilters > 0)
                <div class="card-footer py-2 border-top bg-light">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="small text-muted me-1">
                            <i class="ri ri-price-tag-3-line me-1"></i>Active:
                        </span>

                        @if($search)
                            <span class="badge bg-label-info d-inline-flex align-items-center gap-1 px-2 py-1">
                                <i class="ri ri-search-line" style="font-size:11px;"></i>
                                {{ Str::limit($search, 20) }}
                                <button wire:click="$set('search', '')"
                                        type="button" class="btn-close p-0 ms-1"
                                        style="font-size:8px;"></button>
                            </span>
                        @endif

                        @if($userType)
                            <span class="badge bg-label-primary d-inline-flex align-items-center gap-1 px-2 py-1">
                                <i class="ri ri-user-line" style="font-size:11px;"></i>
                                {{ ucfirst($userType) }}
                                <button wire:click="$set('userType', '')"
                                        type="button" class="btn-close p-0 ms-1"
                                        style="font-size:8px;"></button>
                            </span>
                        @endif

                        @if($action)
                            <span class="badge bg-label-success d-inline-flex align-items-center gap-1 px-2 py-1">
                                <i class="ri ri-flashlight-line" style="font-size:11px;"></i>
                                {{ ucwords(str_replace('_', ' ', $action)) }}
                                <button wire:click="$set('action', '')"
                                        type="button" class="btn-close p-0 ms-1"
                                        style="font-size:8px;"></button>
                            </span>
                        @endif

                        @if($dateFrom || $dateTo)
                            <span class="badge bg-label-warning d-inline-flex align-items-center gap-1 px-2 py-1">
                                <i class="ri ri-calendar-line" style="font-size:11px;"></i>
                                {{ $dateFrom ?: '...' }} → {{ $dateTo ?: '...' }}
                                <button wire:click="$set('dateFrom', ''); $set('dateTo', '')"
                                        type="button" class="btn-close p-0 ms-1"
                                        style="font-size:8px;"></button>
                            </span>
                        @endif

                        <button wire:click="clearFilters"
                                class="btn btn-sm btn-link text-danger p-0 ms-auto text-decoration-none">
                            <i class="ri ri-delete-bin-line me-1"></i> Clear All
                        </button>
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Table Card ────────────────────────────────────── --}}
        <div class="card shadow-none border">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>

                        <th style="width:220px;">
                            <span class="fw-semibold text-muted small">User</span>
                        </th>

                        <th style="width:110px;">
                            <button wire:click="sort('user_type')"
                                    class="btn btn-link btn-sm text-muted p-0 fw-semibold
                                               text-decoration-none d-flex align-items-center gap-1">
                                Type
                                <i class="ri {{ $sortField === 'user_type'
                                        ? ($sortDir === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary')
                                        : 'ri-arrow-up-down-line opacity-25' }}"></i>
                            </button>
                        </th>

                        <th>
                            <button wire:click="sort('action')"
                                    class="btn btn-link btn-sm text-muted p-0 fw-semibold
                                               text-decoration-none d-flex align-items-center gap-1">
                                Action
                                <i class="ri {{ $sortField === 'action'
                                        ? ($sortDir === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary')
                                        : 'ri-arrow-up-down-line opacity-25' }}"></i>
                            </button>
                        </th>

                        <th>Description</th>

                        <th style="width:130px;">
                            <span class="fw-semibold text-muted small">IP Address</span>
                        </th>

                        <th style="width:160px;">
                            <button wire:click="sort('created_at')"
                                    class="btn btn-link btn-sm text-muted p-0 fw-semibold
                                               text-decoration-none d-flex align-items-center gap-1">
                                Date & Time
                                <i class="ri {{ $sortField === 'created_at'
                                        ? ($sortDir === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary')
                                        : 'ri-arrow-up-down-line opacity-25' }}"></i>
                            </button>
                        </th>

                        <th style="width:80px;" class="text-center">
                            <span class="fw-semibold text-muted small">Action</span>
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($logs as $index => $log)
                        <tr wire:key="log-{{ $log->id }}">

                            {{-- # --}}
                            <td class="ps-4 text-muted small">
                                {{ $logs->firstItem() + $index }}
                            </td>

                            {{-- ✅ User Info --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Avatar --}}
                                    <div class="avatar avatar-sm">
                                        @if(!empty($log->user_info['avatar']))
                                            <img src="{{ asset('storage/' . $log->user_info['avatar']) }}"
                                                 alt="{{ $log->user_info['name'] }}"
                                                 class="rounded-circle"
                                                 style="width:32px; height:32px; object-fit:cover;"/>
                                        @else
                                            <span class="avatar-initial rounded-circle
                                                {{ $log->user_type === 'admin' ? 'bg-label-primary' : 'bg-label-info' }}"
                                                  style="width:32px; height:32px; font-size:13px;">
                                                {{ strtoupper(substr($log->user_info['name'], 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Name + ID --}}
                                    <div>
                                        <div class="fw-medium text-body small lh-1 mb-1">
                                            {{ $log->user_info['name'] }}
                                        </div>
                                        <div class="text-muted" style="font-size:11px;">
                                            <i class="ri ri-hashtag" style="font-size:10px;"></i>
                                            {{ $log->user_id ?: 'Guest' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- User Type --}}
                            <td>
                                @if($log->user_type === 'admin')
                                    <span class="badge bg-label-primary d-inline-flex align-items-center gap-1">
                                        <i class="ri ri-shield-user-line"></i> Admin
                                    </span>
                                @else
                                    <span class="badge bg-label-info d-inline-flex align-items-center gap-1">
                                        <i class="ri ri-user-line"></i> User
                                    </span>
                                @endif
                            </td>

                            {{-- Action Badge --}}
                            <td>
                                @php
                                    $actionConfig = [
                                        'login_success'         => ['bg-label-success',   'ri-login-box-line'],
                                        'login_failed'          => ['bg-label-danger',    'ri-close-circle-line'],
                                        'login_rate_limited'    => ['bg-label-warning',   'ri-alarm-warning-line'],
                                        'logout'                => ['bg-label-secondary', 'ri-logout-box-line'],
                                        'password_reset'        => ['bg-label-warning',   'ri-lock-password-line'],
                                        'create' => ['bg-label-primary',   'ri-add-circle-line'],
                                        'update' => ['bg-label-warning',   'ri-edit-line'],
                                        'change'       => ['bg-label-warning',   'ri-edit-2-line'],
                                        'delete'       => ['bg-label-danger',    'ri-delete-bin-line'],
                                        'grant_permission'       => ['bg-label-success',    'ri-shield-check-line'],
                                        'revoke_permission'       => ['bg-label-danger',    'ri-close-fill'],
                                       'assign_assessment_organisations'       => ['bg-label-success',    'ri-folder-shared-line'],
                                       'remove_assessment_organisation'       => ['bg-label-danger',    'ri-folder-reduce-line'],

                                    ];
                                    [$color, $icon] = $actionConfig[$log->action]
                                        ?? ['bg-label-secondary', 'ri-information-line'];
                                @endphp
                                <span class="badge {{ $color }} d-inline-flex align-items-center gap-1">
                                    <i class="ri {{ $icon }}"></i>
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>

                            {{-- Description --}}
                            <td class="text-muted small" style="max-width:260px;">
                                <span class="d-inline-block text-truncate w-100"
                                      title="{{ $log->description }}">
                                    {{ $log->description ?? '—' }}
                                </span>
                            </td>

                            {{-- IP --}}
                            <td>
                                <span class="badge bg-label-secondary font-monospace" style="font-size:11px;">
                                    <i class="ri ri-map-pin-line me-1" style="font-size:10px;"></i>
                                    {{ $log->ip_address ?? '—' }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td class="small text-muted">
                                <div class="fw-medium text-body lh-1 mb-1">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}
                                </div>
                                <div style="font-size:11px;">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}
                                </div>
                            </td>

                            {{-- ✅ View Detail Button --}}
                            <td class="text-center">
                                <button
                                    wire:click="viewDetail({{ $log->id }})"
                                    class="btn btn-sm btn-icon btn-outline-primary rounded-circle"
                                    title="View Details"
                                >
                                    <i class="ri ri-eye-line"></i>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center gap-2 text-muted">
                                    <i class="ri ri-file-list-3-line" style="font-size:3rem; opacity:0.3;"></i>
                                    <p class="mb-0 fw-medium">No activity logs found</p>
                                    @if($activeFilters > 0)
                                        <p class="small mb-0">Try adjusting your filters</p>
                                        <button wire:click="clearFilters"
                                                class="btn btn-sm btn-outline-primary mt-1">
                                            <i class="ri ri-refresh-line me-1"></i> Clear Filters
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="card-footer border-top py-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                        {{-- Showing X to Y of Z --}}
                        <div class="small text-muted">
                            Showing
                            <span class="fw-medium text-body">{{ $logs->firstItem() }}</span>
                            to
                            <span class="fw-medium text-body">{{ $logs->lastItem() }}</span>
                            of
                            <span class="fw-medium text-body">{{ number_format($logs->total()) }}</span>
                            results
                        </div>

                        {{-- Pagination Controls --}}
                        @php
                            $currentPage = $logs->currentPage();
                            $lastPage    = $logs->lastPage();
                            $start       = max(1, $currentPage - 2);
                            $end         = min($lastPage, $currentPage + 2);
                        @endphp

                        <ul class="pagination pagination-sm mb-0 flex-wrap">

                            {{-- « First --}}
                            <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                                <button
                                    wire:click="gotoPage(1)"
                                    class="page-link"
                                    title="First Page"
                                    {{ $currentPage == 1 ? 'disabled' : '' }}
                                >
                                    <i class="ri ri-arrow-left-double-line"></i>
                                </button>
                            </li>

                            {{-- ‹ Prev --}}
                            <li class="page-item {{ $logs->onFirstPage() ? 'disabled' : '' }}">
                                <button
                                    wire:click="previousPage"
                                    class="page-link"
                                    title="Previous Page"
                                    {{ $logs->onFirstPage() ? 'disabled' : '' }}
                                >
                                    <i class="ri ri-arrow-left-s-line"></i>
                                </button>
                            </li>

                            {{-- First page + leading dots --}}
                            @if($start > 1)
                                <li class="page-item d-none d-sm-block">
                                    <button wire:click="gotoPage(1)" class="page-link">1</button>
                                </li>
                                @if($start > 2)
                                    <li class="page-item disabled d-none d-sm-block">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            {{-- Windowed Page Numbers --}}
                            @for($page = $start; $page <= $end; $page++)
                                <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                                    @if($page === $currentPage)
                                        <span class="page-link">{{ $page }}</span>
                                    @else
                                        <button
                                            wire:click="gotoPage({{ $page }})"
                                            class="page-link"
                                        >
                                            {{ $page }}
                                        </button>
                                    @endif
                                </li>
                            @endfor

                            {{-- Last page + trailing dots --}}
                            @if($end < $lastPage)
                                @if($end < $lastPage - 1)
                                    <li class="page-item disabled d-none d-sm-block">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item d-none d-sm-block">
                                    <button wire:click="gotoPage({{ $lastPage }})" class="page-link">
                                        {{ $lastPage }}
                                    </button>
                                </li>
                            @endif

                            {{-- › Next --}}
                            <li class="page-item {{ !$logs->hasMorePages() ? 'disabled' : '' }}">
                                <button
                                    wire:click="nextPage"
                                    class="page-link"
                                    title="Next Page"
                                    {{ !$logs->hasMorePages() ? 'disabled' : '' }}
                                >
                                    <i class="ri ri-arrow-right-s-line"></i>
                                </button>
                            </li>

                            {{-- » Last --}}
                            <li class="page-item {{ $currentPage == $lastPage ? 'disabled' : '' }}">
                                <button
                                    wire:click="gotoPage({{ $lastPage }})"
                                    class="page-link"
                                    title="Last Page"
                                    {{ $currentPage == $lastPage ? 'disabled' : '' }}
                                >
                                    <i class="ri ri-arrow-right-double-line"></i>
                                </button>
                            </li>

                        </ul>

                    </div>

                    {{-- Mobile: Page X of Y --}}
                    <div class="d-flex d-sm-none justify-content-center mt-2">
            <span class="small text-muted">
                Page
                <span class="fw-medium text-body">{{ $currentPage }}</span>
                of
                <span class="fw-medium text-body">{{ $lastPage }}</span>
            </span>
                    </div>

                </div>

            @else
                <div class="card-footer border-top py-2">
                    <div class="small text-muted">
                        Showing all
                        <span class="fw-medium text-body">{{ number_format($logs->total()) }}</span>
                        results
                    </div>
                </div>
            @endif




        </div>

    @endif
    {{-- END LIST VIEW --}}


    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- DETAIL VIEW  (view = 1)                                --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    @if($view === 1 && $selectedLogDetail)

        @php $log = $selectedLogDetail; @endphp

        {{-- ── Header ─────────────────────────────────────── --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>
                <h4 class="fw-semibold mb-0">Activity Log Detail</h4>
                <p class="text-muted small mb-0">
                    Log #{{ $log['id'] }} —
                    {{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y, h:i A') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <button wire:click="backToList"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="ri ri-arrow-left-line"></i> Back to Logs
                </button>
            </div>
        </div>

        <div class="row g-4">

            {{-- ── Left: User Card ────────────────────────── --}}
            <div class="col-12 col-md-4">

                {{-- User Info Card --}}
                <div class="card shadow-none border mb-4">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <i class="ri ri-user-line text-primary"></i> User Information
                        </h6>
                    </div>
                    <div class="card-body text-center py-4">

                        {{-- Avatar --}}
                        <div class="mb-3">
                            @if(!empty($log['user']['avatar']))
                                <img src="{{ asset('storage/' . $log['user']['avatar']) }}"
                                     alt="{{ $log['user']['name'] }}"
                                     class="rounded-circle border"
                                     style="width:80px; height:80px; object-fit:cover;"/>
                            @else
                                <div class="avatar avatar-xl mx-auto">
                                    <span class="avatar-initial rounded-circle
                                        {{ $log['user_type'] === 'admin' ? 'bg-label-primary' : 'bg-label-info' }}"
                                          style="width:80px; height:80px; font-size:2rem;">
                                        {{ strtoupper(substr($log['user']['name'], 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Name --}}
                        <h5 class="fw-semibold mb-1">{{ $log['user']['name'] }}</h5>

                        {{-- Type Badge --}}
                        @if($log['user_type'] === 'admin')
                            <span class="badge bg-label-primary mb-3">
                                <i class="ri ri-shield-user-line me-1"></i> Admin
                            </span>
                        @else
                            <span class="badge bg-label-info mb-3">
                                <i class="ri ri-user-line me-1"></i> User
                            </span>
                        @endif

                        <hr class="my-3"/>

                        {{-- Details List --}}
                        <ul class="list-unstyled text-start small mb-0">
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ri ri-hashtag text-muted" style="width:16px;"></i>
                                <span class="text-muted">User ID:</span>
                                <span class="ms-auto fw-medium">{{ $log['user_id'] ?: 'Guest' }}</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ri ri-mail-line text-muted" style="width:16px;"></i>
                                <span class="text-muted">Email:</span>
                                <span class="ms-auto fw-medium text-truncate"
                                      style="max-width:140px;"
                                      title="{{ $log['user']['email'] }}">
                                    {{ $log['user']['email'] }}
                                </span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="ri ri-phone-line text-muted" style="width:16px;"></i>
                                <span class="text-muted">Mobile:</span>
                                <span class="ms-auto fw-medium">{{ $log['user']['mobile'] }}</span>
                            </li>
                        </ul>

                    </div>
                </div>

                {{-- Session Card --}}
                <div class="card shadow-none border">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <i class="ri ri-global-line text-primary"></i> Session Info
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <ul class="list-unstyled small mb-0">

                            <li class="d-flex align-items-start gap-2 mb-3">
                                <i class="ri ri-map-pin-2-line text-muted mt-1" style="width:16px;"></i>
                                <div>
                                    <div class="text-muted mb-1">IP Address</div>
                                    <span class="badge bg-label-secondary font-monospace">
                                        {{ $log['ip_address'] ?? '—' }}
                                    </span>
                                </div>
                            </li>

                            <li class="d-flex align-items-start gap-2 mb-3">
                                <i class="ri ri-calendar-event-line text-muted mt-1" style="width:16px;"></i>
                                <div>
                                    <div class="text-muted mb-1">Date & Time</div>
                                    <div class="fw-medium">
                                        {{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y') }}
                                    </div>
                                    <div class="text-muted" style="font-size:11px;">
                                        {{ \Carbon\Carbon::parse($log['created_at'])->format('h:i:s A') }}
                                    </div>
                                </div>
                            </li>

                            <li class="d-flex align-items-start gap-2">
                                <i class="ri ri-computer-line text-muted mt-1" style="width:16px;"></i>
                                <div>
                                    <div class="text-muted mb-1">User Agent</div>
                                    <div class="small text-body"
                                         style="word-break:break-word; font-size:11px; line-height:1.5;">
                                        {{ $log['user_agent'] ?? '—' }}
                                    </div>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>

            </div>

            {{-- ── Right: Activity Detail ──────────────────── --}}
            <div class="col-12 col-md-8">

                {{-- Activity Card --}}
                <div class="card shadow-none border mb-4">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <i class="ri ri-flashlight-line text-primary"></i> Activity Details
                        </h6>
                    </div>
                    <div class="card-body py-3">

                        {{-- Action --}}
                        <div class="mb-4">
                            <label class="text-muted small fw-medium d-block mb-2">Action</label>
                            @php
                                $actionConfig = [
                                    'login_success'         => ['bg-label-success',   'ri-login-box-line'],
                                    'login_failed'          => ['bg-label-danger',    'ri-close-circle-line'],
                                    'login_rate_limited'    => ['bg-label-warning',   'ri-alarm-warning-line'],
                                    'logout'                => ['bg-label-secondary', 'ri-logout-box-line'],
                                    'password_reset'        => ['bg-label-warning',   'ri-lock-password-line'],
                                     'create' => ['bg-label-primary',   'ri-add-circle-line'],
                                    'update' => ['bg-label-warning',   'ri-edit-line'],
                                    'change'       => ['bg-label-warning',   'ri-edit-2-line'],
                                    'delete'       => ['bg-label-danger',    'ri-delete-bin-line'],
                                ];
                                [$color, $icon] = $actionConfig[$log['action']]
                                    ?? ['bg-label-secondary', 'ri-information-line'];
                            @endphp
                            <span class="badge {{ $color }} fs-6 px-3 py-2 d-inline-flex align-items-center gap-2">
                                <i class="ri {{ $icon }}"></i>
                                {{ ucwords(str_replace('_', ' ', $log['action'])) }}
                            </span>
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label class="text-muted small fw-medium d-block mb-2">Description</label>
                            <div class="bg-light rounded p-3 small text-body">
                                {{ $log['description'] ?? '—' }}
                            </div>
                        </div>

                        {{-- Model Info --}}
                        @if($log['model_type'] || $log['model_id'])
                            <div class="mb-4">
                                <label class="text-muted small fw-medium d-block mb-2">
                                    Related Record
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-label-secondary px-3 py-2">
                                        <i class="ri ri-database-2-line me-1"></i>
                                        {{ class_basename($log['model_type']) ?? '—' }}
                                    </span>
                                    @if($log['model_id'])
                                        <span class="badge bg-label-secondary px-3 py-2 font-monospace">
                                            <i class="ri ri-hashtag me-1"></i>
                                            {{ $log['model_id'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Properties Card --}}
                @if(!empty($log['properties']))
                    <div class="card shadow-none border">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                <i class="ri ri-code-s-slash-line text-primary"></i>
                                Additional Properties
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th class="small fw-semibold" style="width:40%;">Key</th>
                                        <th class="small fw-semibold">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($log['properties'] as $key => $value)
                                        <tr>
                                            <td>
                                                    <span class="badge bg-label-secondary font-monospace">
                                                        {{ $key }}
                                                    </span>
                                            </td>
                                            <td class="small">
                                                @if(is_array($value))
                                                    <pre class="mb-0 small bg-light rounded p-2"
                                                         style="font-size:11px;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @elseif(is_bool($value))
                                                    <span class="badge {{ $value ? 'bg-label-success' : 'bg-label-danger' }}">
                                                            {{ $value ? 'true' : 'false' }}
                                                        </span>
                                                @else
                                                    <span class="text-body">{{ $value }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    @endif
    {{-- END DETAIL VIEW --}}

    {{-- ── Loading Overlay ──────────────────────────────────── --}}
    <div wire:loading.flex
         class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center"
         style="background:rgba(0,0,0,0.15); z-index:9999;">
        <div class="bg-white rounded-3 shadow-lg px-4 py-3 d-flex align-items-center gap-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="small fw-medium text-muted">Loading...</span>
        </div>
    </div>

</div>

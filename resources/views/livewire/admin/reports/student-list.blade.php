<div class="min-h-screen bg-light py-4 py-lg-5">
    <div class="container-fluid px-4">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">

                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}" class="text-decoration-none">
                                <i class="ri ri-dashboard-line me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <i class="ri ri-file-chart-line me-1"></i>Reports
                        </li>
                        <li class="breadcrumb-item active">Student Report</li>
                    </ol>
                </nav>

                {{-- Title --}}
                <div class="mt-3 mb-4">
                    <h1 class="h2 fw-bold mb-1 text-dark">
                        <i class="ri ri-graduation-cap-line me-2"></i>Student Report
                    </h1>
                    <p class="text-muted mb-0">View all students across organisations</p>
                </div>

            </div>
        </div>

        {{-- DataTable Card --}}
        <div class="card shadow-sm border-0">

            {{-- Toolbar --}}
            <div class="card-header border-bottom py-3 px-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                    <h5 class="card-title mb-0">{{ $title }}</h5>

                    <div class="d-flex align-items-center flex-wrap gap-2">

                        {{-- Search --}}
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-end-0 text-muted">
                                <i class="ri ri-search-line"></i>
                            </span>
                            <input wire:model.live.debounce.300ms="search"
                                   type="search"
                                   class="form-control form-control-sm border-start-0 ps-0"
                                   placeholder="Search name, email, phone, student ID…"
                                   style="min-width: 250px;" />
                        </div>

                        {{-- Export --}}
                        <button wire:click="export('csv')"
                                class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                            <i class="ri ri-file-excel-line"></i>
                            Export
                        </button>

                    </div>
                </div>
            </div>

            {{-- Active Filter Pills --}}
            @if(count($this->activeFilters) > 0 || $search)
                <div class="d-flex flex-wrap align-items-center gap-2 px-4 py-2 bg-primary-subtle border-bottom">
                    @if($search)
                        <span class="badge rounded-pill text-dark border border-primary-subtle fw-normal px-3 py-2">
                            Search: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="btn-close ms-1" style="font-size:9px"></button>
                        </span>
                    @endif
                    @foreach($this->activeFilters as $key => $value)
                        <span class="badge rounded-pill text-dark border border-primary-subtle fw-normal px-3 py-2">
                            {{ $key === 'statusFilter' ? 'Status' : 'Organisation' }}: {{ $value }}
                            <button wire:click="clearFilter('{{ $key }}')" class="btn-close ms-1" style="font-size:9px"></button>
                        </span>
                    @endforeach
                    <button wire:click="clearAllFilters"
                            class="btn btn-link btn-sm text-primary p-0 fw-semibold text-decoration-none">
                        Clear all
                    </button>
                </div>
            @endif

            {{-- Filter Panel --}}
            <div class="border-bottom bg-light px-4 py-3">
                <div class="row g-3">
                    @foreach($this->filters as $filter)
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold text-uppercase text-muted mb-1"
                                   style="font-size:11px; letter-spacing:.5px">
                                {{ $filter['label'] }}
                            </label>
                            <select wire:model.live="{{ $filter['key'] }}"
                                    class="form-select form-select-sm">
                                @foreach($filter['options'] as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    {{-- Per Page --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label fw-semibold text-uppercase text-muted mb-1"
                               style="font-size:11px; letter-spacing:.5px">
                            Per Page
                        </label>
                        <select wire:model.live="perPage" class="form-select form-select-sm">
                            @foreach($perPageOptions as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-sm fw-semibold" style="font-size: 0.875rem;">
                    <thead class="table-light">
                    <tr>
                        {{-- Student ID --}}
                        <th wire:click="sort('details.student_id')"
                            class="px-4 py-3 cursor-pointer user-select-none"
                            style="width: 140px;">
                                <span class="d-inline-flex align-items-center gap-1">
                                    Student ID
                                    @if($sortBy === 'details.student_id')
                                        <i class="ri ri-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}-line text-primary"></i>
                                    @else
                                        <i class="ri ri-arrow-up-down-line opacity-25"></i>
                                    @endif
                                </span>
                        </th>

                        {{-- Full Name --}}
                        <th wire:click="sort('name')"
                            class="px-4 py-3 cursor-pointer user-select-none">
                                <span class="d-inline-flex align-items-center gap-1">
                                    Full Name
                                    @if($sortBy === 'name')
                                        <i class="ri ri-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}-line text-primary"></i>
                                    @else
                                        <i class="ri ri-arrow-up-down-line opacity-25"></i>
                                    @endif
                                </span>
                        </th>

                        {{-- Organisation --}}
                        <th wire:click="sort('organisation_id')"
                            class="px-4 py-3 cursor-pointer user-select-none">
                                <span class="d-inline-flex align-items-center gap-1">
                                    Organisation
                                    @if($sortBy === 'organisation_id')
                                        <i class="ri ri-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}-line text-primary"></i>
                                    @else
                                        <i class="ri ri-arrow-up-down-line opacity-25"></i>
                                    @endif
                                </span>
                        </th>

                        {{-- Email --}}
                        <th class="px-4 py-3">Email</th>

                        {{-- Phone --}}
                        <th class="px-4 py-3" style="width: 130px;">Phone No</th>

                        {{-- Enrolled On --}}
                        <th wire:click="sort('created_at')"
                            class="px-4 py-3 cursor-pointer user-select-none"
                            style="width: 130px;">
                                <span class="d-inline-flex align-items-center gap-1">
                                    Enrolled On
                                    @if($sortBy === 'created_at')
                                        <i class="ri ri-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}-line text-primary"></i>
                                    @else
                                        <i class="ri ri-arrow-up-down-line opacity-25"></i>
                                    @endif
                                </span>
                        </th>

                        {{-- Status --}}
                        <th wire:click="sort('status')"
                            class="px-4 py-3 cursor-pointer user-select-none"
                            style="width: 110px;">
                                <span class="d-inline-flex align-items-center gap-1">
                                    Status
                                    @if($sortBy === 'status')
                                        <i class="ri ri-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}-line text-primary"></i>
                                    @else
                                        <i class="ri ri-arrow-up-down-line opacity-25"></i>
                                    @endif
                                </span>
                        </th>

                        {{-- Action --}}
                        <th class="px-4 py-3 text-center" style="width: 80px;">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($this->rows as $student)
                        <tr wire:key="student-{{ $student->id }}">

                            {{-- Student ID --}}
                            <td class="px-4 py-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1">
                                        {{ $student->details->student_id ?? 'N/A' }}
                                    </span>
                            </td>

                            {{-- Full Name --}}
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Avatar --}}
                                    @if($student->avatar && \Storage::disk('public')->exists($student->avatar))
                                        <img src="{{ asset('storage/' . $student->avatar) }}"
                                             alt="{{ $student->full_name }}"
                                             class="rounded-circle border"
                                             style="width: 36px; height: 36px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle border bg-primary bg-opacity-10
                                                        d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width: 36px; height: 36px;">
                                            <i class="ri ri-user-line text-primary" style="font-size: 1rem;"></i>
                                        </div>
                                    @endif
                                    <span class="fw-semibold text-dark">{{ $student->full_name }}</span>
                                </div>
                            </td>

                            {{-- Organisation --}}
                            <td class="px-4 py-3">
                                {{ $student->organisation->name ?? '—' }}
                            </td>

                            {{-- Email --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $student->email }}
                            </td>

                            {{-- Phone --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $student->phone ?? '—' }}
                            </td>

                            {{-- Enrolled On --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $student->created_at->format('d M Y') }}
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                @php
                                    $badge = match($student->status) {
                                        'active'    => 'bg-success',
                                        'inactive'  => 'bg-secondary',
                                        'suspended' => 'bg-danger',
                                        'pending'   => 'bg-warning',
                                        default     => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                            </td>

                            {{-- Action --}}
                            <td class="px-4 py-3 text-center">
                                <button wire:click="dispatchAction('view-student', {{ $student->id }})"
                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                        title="View Details">
                                    <i class="ri ri-eye-line"></i>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="ri ri-user-search-line" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-3 mb-2 fw-semibold">{{ $emptyMessage }}</p>
                                @if($search || count($this->activeFilters) > 0)
                                    <button wire:click="clearAllFilters"
                                            class="btn btn-sm btn-outline-primary">
                                        Clear filters
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer / Pagination --}}
            <div class="card-footer border-top d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3">
                <span class="small text-muted">
                    Showing
                    <strong>{{ $this->rows->firstItem() ?? 0 }}</strong>–<strong>{{ $this->rows->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $this->rows->total() }}</strong> students
                </span>
                <div>
                    {{ $this->rows->links() }}
                </div>
            </div>

        </div>

    </div>

    {{-- Toast --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div x-data="{
                show: false, message: '', type: 'success',
                init() {
                    window.addEventListener('notify', (e) => {
                        this.message = e.detail.message;
                        this.type    = e.detail.type ?? 'success';
                        this.show    = true;
                        setTimeout(() => this.show = false, 3500);
                    });
                }
             }"
             x-show="show"
             x-transition
             :class="type === 'success' ? 'bg-success' : 'bg-danger'"
             class="toast align-items-center border-0 text-white show"
             style="display: none;">
            <div class="d-flex">
                <div class="toast-body" x-text="message"></div>
                <button @click="show = false" class="btn-close btn-close-white me-2 m-auto"></button>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer { cursor: pointer; }
    </style>
</div>

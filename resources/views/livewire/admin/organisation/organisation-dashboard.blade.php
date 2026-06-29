{{-- resources/views/livewire/admin/organisation/organisation-dashboard.blade.php --}}

<div>
    {{-- Header --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="ri ri-building-2-line me-2"></i>{{ $organisation->name }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.organisations.index') }}">Organisations</a></li>
                    <li class="breadcrumb-item active">{{ $organisation->name }}</li>
                </ol>
            </nav>
        </div>

    </div>

    {{-- Organisation Info Card --}}
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Organisation Details</h6>
                    <div class="mb-3">
                        <small class="text-muted d-block">Code</small>
                        <strong>{{ $organisation->code }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Type</small>
                        <span class="badge"
                            style="background-color: {{ $organisation->organisationType?->color ?? '#6c757d' }}">
                            {{ $organisation->organisationType?->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge {{ $organisation->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($organisation->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Location</small>
                        <strong>
                            {{ $organisation->city }}{{ $organisation->district?->name ? ', ' . $organisation->district->name : '' }},
                            {{ $organisation->state?->name ?? '' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-primary bg-opacity-10 rounded-circle mx-auto mb-2"
                                style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="ri ri-group-line text-primary fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">{{ $stats['total_students'] }}</h5>
                            <small class="text-muted">Total Students</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-success bg-opacity-10 rounded-circle mx-auto mb-2"
                                style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="ri ri-checkbox-circle-line text-success fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">{{ $stats['active_students'] }}</h5>
                            <small class="text-muted">Active Students</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-warning bg-opacity-10 rounded-circle mx-auto mb-2"
                                style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="ri ri-time-line text-warning fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">{{ $stats['pending_students'] }}</h5>
                            <small class="text-muted">Pending Students</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subscription Status --}}
            @if ($organisation->subscription_end)
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">Subscription Status</h6>
                        <div class="progress" style="height: 25px;">
                            @php
                                $start = \Carbon\Carbon::parse($organisation->subscription_start);
                                $end = \Carbon\Carbon::parse($organisation->subscription_end);
                                $now = now();
                                $total = $end->diffInDays($start);
                                $elapsed = $now->diffInDays($start);
                                $percentage = min(($elapsed / $total) * 100, 100);
                            @endphp
                            <div class="progress-bar {{ $percentage >= 90 ? 'bg-danger' : 'bg-primary' }}"
                                role="progressbar" style="width: {{ $percentage }}%"
                                aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $percentage }}%
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Expires on: <strong>{{ $organisation->subscription_end->format('d M Y') }}</strong>
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @can('org.student.view')
                            <a href="#" class="btn btn-outline-primary">
                                <i class="ri ri-user-star-line me-2"></i>Manage Students
                            </a>
                        @endcan

                        @can('org.course.assign')
                            <a href="#" class="btn btn-outline-info">
                                <i class="ri ri-book-open-line me-2"></i>Assign Courses
                            </a>
                        @endcan

                        @can('org.assessment.assign')
                            <a href="#" class="btn btn-outline-warning">
                                <i class="ri ri-clipboard-line me-2"></i>Assign Assessments
                            </a>
                        @endcan

                        @can('org.report.view')
                            <a href="#" class="btn btn-outline-success">
                                <i class="ri ri-bar-chart-line me-2"></i>View Reports
                            </a>
                        @endcan

                        @can('org.organisation.settings')
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="ri ri-settings-3-line me-2"></i>Settings
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

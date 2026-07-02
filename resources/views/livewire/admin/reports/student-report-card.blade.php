{{-- resources/views/livewire/student-report-card.blade.php --}}

<div class="container py-4">
    <div class="card shadow">
        {{-- Header --}}
        <div class="card-header text-white p-4" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
            <h2 class="h3 mb-0 fw-bold text-white">Student Report Cards</h2>
        </div>

        {{-- Filters --}}
        <div class="card-body bg-light border-bottom p-4">
            <div class="row g-3">
                {{-- Search --}}
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="ri ri-search-line"></i>
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control"
                            placeholder="Search by name, email, or student ID..."
                        />
                    </div>
                </div>

                {{-- Age Group Filter --}}
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Age Group</label>
                    <select
                        wire:model.live="ageGroupFilter"
                        class="form-select"
                    >
                        <option value="">All Age Groups</option>
                        @foreach($ageGroups as $ageGroup)
                            <option value="{{ $ageGroup->id }}">{{ $ageGroup->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Export Button --}}
                <div class="col-md-4 d-flex align-items-end">
                    <button
                        wire:click="exportReports"
                        class="btn btn-success w-100"
                    >
                        <i class="ri ri-download-2-line me-1"></i>
                        Export Reports
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th class="text-uppercase fw-bold small">Student ID</th>
                    <th class="text-uppercase fw-bold small">Student Name</th>
                    <th class="text-uppercase fw-bold small">Physical Age</th>
                    <th class="text-uppercase fw-bold small">Mental Age Group</th>
                    <th class="text-uppercase fw-bold small">IQ Score</th>
                    <th class="text-uppercase fw-bold small">EQ Score</th>
                    <th class="text-uppercase fw-bold small">LQ Score</th>
                    <th class="text-uppercase fw-bold small">Detailed Report</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    @php
                        $reportService = app(\App\Services\StudentReportService::class);
                        $student = $reportService->getStudentReportCard($user->id);
                    @endphp

                    @if($student)
                        <tr class="transition-hover">
                            <td class="fw-medium">{{ $student['student_id'] }}</td>
                            <td>{{ $student['student_name'] }}</td>
                            <td class="text-muted">{{ $student['physical_age'] }} years</td>
                            <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                        {{ $student['mental_age_group'] }}
                                    </span>
                            </td>
                            <td>
                                    <span class="fw-semibold text-success">
                                        <i class="ri ri-brain-line me-1"></i>{{ $student['iq_score'] }}
                                    </span>
                            </td>
                            <td>
                                    <span class="fw-semibold text-info">
                                        <i class="ri ri-heart-line me-1"></i>{{ $student['eq_score'] }}
                                    </span>
                            </td>
                            <td>
                                    <span class="fw-semibold text-warning">
                                        <i class="ri ri-shield-star-line me-1"></i>{{ $student['lq_score'] }}
                                    </span>
                            </td>
                            <td>
                                <a
                                    href="{{ route('admin.reports.detailed-report', $user['id']) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="ri ri-eye-line me-1"></i>View Details
                                </a>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="ri ri-file-list-3-line fs-1 d-block mb-2"></i>
                            <p class="mb-0">No student reports found.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer bg-light p-3">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="ri ri-check-line me-2"></i>
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<style>
    .transition-hover {
        transition: background-color 0.15s ease-in-out;
    }

    .transition-hover:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
</style>

{{-- resources/views/livewire/student-detailed-report.blade.php --}}

<div class="container py-4">
    <div class="card shadow-lg">
        {{-- Header --}}
        <div class="card-header bg-gradient  p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h1 class="h3 mb-1 fw-bold">Detailed Student Report</h1>
            <p class="mb-0">Comprehensive assessment analysis</p>
        </div>

        {{-- Student Information --}}
        <div class="card-body bg-light border-bottom p-4">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Student Name</p>
                            <p class="h6 fw-semibold mb-0">{{ $reportData['student_name'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Institute Name</p>
                            <p class="h6 fw-semibold mb-0">{{ $reportData['institute_name'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Physical Age</p>
                            <p class="h6 fw-semibold mb-0">{{ $reportData['physical_age'] }} years</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Current Mental Age Group</p>
                            <p class="h6 fw-semibold mb-0 text-primary">{{ $reportData['mental_age_group'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sub-Skills Performance --}}
        <div class="card-body p-4">
            <h2 class="h4 fw-bold mb-4">Sub-Skill Performance Analysis</h2>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                    <tr>
                        <th class="text-uppercase fw-bold small" style="width: 80px;">Sl No</th>
                        <th class="text-uppercase fw-bold small">Sub-Skill Name</th>
                        <th class="text-uppercase fw-bold small">Score vs Total Attempts</th>
                        <th class="text-uppercase fw-bold small" style="width: 200px;">Success Rate</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reportData['sub_skills'] as $index => $skill)
                        <tr class="transition-hover">
                            <td class="fw-medium">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $skill['name'] }}</td>
                            <td>
                                <span class="fw-semibold text-success">{{ $skill['correct'] }}</span>
                                correct answer{{ $skill['correct'] != 1 ? 's' : '' }} out of
                                <span class="fw-semibold text-primary">{{ $skill['total'] }}</span>
                                attempt{{ $skill['total'] != 1 ? 's' : '' }}
                            </td>
                            <td>
                                @php
                                    $percentage = $skill['total'] > 0 ? round(($skill['correct'] / $skill['total']) * 100, 1) : 0;
                                    $progressClass = $percentage >= 70 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger');
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar {{ $progressClass }}"
                                             role="progressbar"
                                             style="width: {{ $percentage }}%;"
                                             aria-valuenow="{{ $percentage }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="fw-semibold text-nowrap">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if(empty($reportData['sub_skills']))
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="ri ri-information-line fs-4"></i>
                                <p class="mb-0 mt-2">No sub-skill data available.</p>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3">
            <a href="{{ route('admin.reports.report-cards') }}"
               class="btn btn-link text-decoration-none">
                <i class="ri ri-arrow-left-line me-1"></i>
                Back to Report Cards
            </a>

            <button onclick="window.print()"
                    class="btn btn-primary">
                <i class="ri ri-printer-line me-1"></i>
                Print Report
            </button>
        </div>
    </div>
</div>

<style>
    .transition-hover {
        transition: background-color 0.15s ease-in-out;
    }

    .transition-hover:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn, .card-footer {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

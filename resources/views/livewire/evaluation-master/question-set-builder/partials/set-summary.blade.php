{{--
    partials/set-summary.blade.php
    Right sidebar summary of the question set being built.
--}}
<div class="card shadow-sm border-0 mb-4 position-sticky" style="top:1rem;">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-bar-chart-2-line text-primary me-2"></i>Set Summary
        </h6>
    </div>
    <div class="card-body p-4">

        {{-- Set info --}}
        <div class="mb-3 pb-3 border-bottom">
            <div class="small text-muted mb-1">Code</div>
            <div class="font-monospace fw-medium">{{ $setCode }}</div>
        </div>

        <div class="mb-3 pb-3 border-bottom">
            <div class="small text-muted mb-1">Type</div>
            <span class="badge bg-primary-subtle text-primary px-2 py-1">
                {{ strtoupper($setType) }}
            </span>
        </div>

        <div class="mb-3 pb-3 border-bottom">
            <div class="small text-muted mb-1">Status</div>
            <span class="badge px-2 py-1
                @if($setStatus === 'publish') bg-success
                @elseif($setStatus === 'draft') bg-warning text-dark
                @else bg-secondary @endif">
                {{ ucfirst($setStatus) }}
            </span>
        </div>

        {{-- Counters --}}
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="text-center p-2 bg-light rounded">
                    <div class="fs-3 fw-bold text-primary">{{ count($groups) }}</div>
                    <div class="small text-muted">Groups</div>
                </div>
            </div>
            <div class="col-6">
                <div class="text-center p-2 bg-light rounded">
                    <div class="fs-3 fw-bold text-success">{{ $totalQuestions }}</div>
                    <div class="small text-muted">Questions</div>
                </div>
            </div>
        </div>

        {{-- Per-group breakdown --}}
        @if (count($groups))
            <div class="small fw-semibold mb-2 text-dark">Group Breakdown</div>
            <div class="d-flex flex-column gap-1 mb-3">
                @foreach ($groups as $g)
                    <div class="d-flex justify-content-between align-items-center
                        bg-light rounded px-2 py-1">
                        <span class="small text-truncate me-2" style="max-width:130px;"
                            title="{{ $g['title'] }}">{{ $g['title'] }}</span>
                        <span class="badge bg-primary-subtle text-primary">
                            {{ $g['question_count'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Edit details link --}}
        <a href="{{ route('admin.question-sets.index') }}"
            class="btn btn-outline-secondary w-100 btn-sm">
            <i class="ri ri-arrow-go-back-line me-1"></i>Back to List
        </a>

    </div>
</div>

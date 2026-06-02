{{--
    partials/preview/score-summary.blade.php
    Right-column score summary card inside the preview view.
--}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-trophy-fill text-warning me-2"></i>Score Summary
        </h6>
    </div>

    <div class="card-body p-4">
        <div class="row g-3">

            <div class="col-6">
                <div class="text-center p-3 bg-light rounded">
                    <div class="small text-muted mb-1">Max Score</div>
                    <div class="fs-3 fw-bold text-success">
                        {{ number_format((float) $maxScore, 2) }}
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="text-center p-3 bg-light rounded">
                    <div class="small text-muted mb-1">Negative Mark</div>
                    <div class="fs-3 fw-bold text-danger">
                        {{ number_format((float) $negativeMark, 2) }}
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="text-center p-3 bg-light rounded">
                    <div class="small text-muted mb-1">Correct Options</div>
                    <div class="fs-2 fw-bold text-primary">
                        {{ $this->correctOptionsCount }} / {{ count($options) }}
                    </div>
                </div>
            </div>

        </div>

        @if ($timeLimit || $adminNotes)
            <hr class="my-3">

            @if ($timeLimit)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="ri ri-timer-line text-muted"></i>
                    <span class="small text-muted">Time Limit:</span>
                    <strong>{{ $timeLimit }} seconds</strong>
                </div>
            @endif

            @if ($adminNotes)
                <div class="d-flex align-items-start gap-2">
                    <i class="ri ri-newspaper-line text-muted mt-1"></i>
                    <div>
                        <span class="small text-muted d-block">Admin Notes:</span>
                        <small class="text-muted">{{ Str::limit($adminNotes, 100) }}</small>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

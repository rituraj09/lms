{{--
    partials/form/scoring.blade.php
    Right-column scoring settings: time limit, max score,
    negative mark, shuffle toggle, score summary.
--}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-trophy-fill text-warning me-2"></i>Scoring Settings
        </h6>
    </div>

    <div class="card-body p-4">

        {{-- Time Limit --}}
        <div class="mb-3">
            <label class="form-label fw-medium small">
                Time Limit <span class="text-muted">(seconds)</span>
            </label>
            <input type="number"
                wire:model.blur="timeLimit"
                class="form-control @error('timeLimit') is-invalid @enderror"
                min="1" max="65535"
                placeholder="e.g. 60" />
            @error('timeLimit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Max Score (read-only, computed from options) --}}
        <div class="mb-3">
            <label class="form-label fw-medium small">
                Max Score <span class="text-danger">*</span>
            </label>
            <input type="number"
                class="form-control bg-light"
                value="{{ $maxScore }}"
                readonly
                title="Calculated automatically from correct option weightages" />
            @error('maxScore')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Negative Mark --}}
        <div class="mb-3">
            <label class="form-label fw-medium small">Negative Mark</label>
            <div class="input-group">
                <span class="input-group-text bg-danger-subtle text-danger">
                    <i class="ri ri-indeterminate-circle-fill"></i>
                </span>
                <input type="number"
                    wire:model.live="negativeMark"
                    class="form-control"
                    step="0.01" min="0"
                    placeholder="0.00" />
            </div>
            <div class="form-text small">Marks deducted for a wrong answer.</div>
        </div>

        {{-- Shuffle Options --}}
        <div class="mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                    wire:model="isOptionsShuffle"
                    id="shuffleCheck" />
                <label class="form-check-label fw-medium small" for="shuffleCheck">
                    Shuffle Options
                </label>
            </div>
            <div class="form-text small">Randomize option order for each student.</div>
        </div>

        {{-- Score Summary --}}
        <div class="bg-light rounded p-3 mt-3">
            <div class="small fw-semibold mb-2 text-dark">Score Summary</div>

            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Max Score</span>
                <span class="fw-bold text-success">+{{ number_format((float) $maxScore, 2) }}</span>
            </div>

            <div class="d-flex justify-content-between small">
                <span class="text-muted">Negative Mark</span>
                <span class="fw-bold text-danger">−{{ number_format((float) $negativeMark, 2) }}</span>
            </div>

            <hr class="my-2">

            <div class="d-flex justify-content-between small">
                <span class="fw-semibold">Correct Options</span>
                <span class="fw-bold text-primary">{{ $this->correctOptionsCount }}</span>
            </div>
        </div>

    </div>
</div>

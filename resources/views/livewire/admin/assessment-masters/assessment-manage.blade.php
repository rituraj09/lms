<div class="assessment-manage-wrapper">

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="ri ri-checkbox-circle-line fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="ri ri-error-warning-line fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="ri ri-error-warning-fill me-2 fs-5"></i>
                <strong>Please fix the following errors:</strong>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="ri ri-draft-line text-primary me-2"></i>
                {{ $assessmentId ? 'Edit Assessment' : 'New Assessment' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.assessment-masters.list') }}" >Assessments</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $assessmentId ? 'Edit' : 'New' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a  href="{{ route('admin.assessment-masters.list') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ri ri-arrow-left-line me-1"></i> Cancel
            </a>
            <button type="button" wire:click="saveAssessment" wire:loading.attr="disabled"
                    class="btn btn-primary btn-sm shadow-sm">
                <span wire:loading wire:target="saveAssessment">
                    <span class="spinner-border spinner-border-sm me-1"></span>Saving…
                </span>
                <span wire:loading.remove wire:target="saveAssessment">
                    <i class="ri ri-arrow-right-line me-1"></i>Save & Go to Builder
                </span>
            </button>
        </div>
    </div>

    <form wire:submit.prevent="saveAssessment">
        <div class="row g-4">

            {{-- Left Column --}}
            <div class="col-lg-8">

                {{-- Basic Info --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-information-line text-primary me-2"></i>Assessment Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-medium small">Assessment Code</label>
                                <div class="form-control bg-light text-muted small d-flex align-items-center gap-2">
                                    <i class="ri ri-barcode-line"></i>{{ $assessment_code }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-medium small">
                                    Assessment Type <span class="text-danger">*</span>
                                </label>
                                <select wire:model="assessment_type_id"
                                        class="form-select @error('assessment_type_id') is-invalid @enderror">
                                    <option value="">— Select Type —</option>
                                    @foreach ($assessmentTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('assessment_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-medium small">
                                    Age Group <span class="text-danger">*</span>
                                </label>
                                <select wire:model="age_group_id"
                                        class="form-select @error('age_group_id') is-invalid @enderror">
                                    <option value="">— Select Age Group —</option>
                                    @foreach ($ageGroups as $ag)
                                        <option value="{{ $ag['id'] }}">{{ $ag['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('age_group_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Cover Image --}}
                            <div class="col-12">
                                <label class="form-label fw-medium small">
                                    Cover Image <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <div class="d-flex align-items-start gap-3 flex-wrap">

                                    <div class="border rounded d-flex align-items-center justify-content-center bg-light overflow-hidden"
                                         style="width:160px;height:110px;flex-shrink:0;">
                                        @if ($cover_image_file)
                                            <img src="{{ $cover_image_file->temporaryUrl() }}" alt="Preview"
                                                 class="img-fluid w-100 h-100 object-fit-cover">
                                        @elseif ($cover_image_path && !$removeCoverImage)
                                            <img src="{{ Storage::url($cover_image_path) }}" alt="Cover Image"
                                                 class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="text-center text-muted small px-2">
                                                <i class="ri-image-add-line fs-2 d-block mb-1"></i>No image
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex flex-column gap-2 justify-content-center">
                                        <div>
                                            <input type="file" wire:model="cover_image_file"
                                                   id="coverImageInput"
                                                   accept="image/jpg,image/jpeg,image/png,image/webp"
                                                   class="d-none">
                                            <label for="coverImageInput"
                                                   class="btn btn-sm btn-outline-primary mb-0"
                                                   style="cursor:pointer;">
                                                <i class="ri-upload-2-line me-1"></i>
                                                {{ ($cover_image_path && !$removeCoverImage) || $cover_image_file ? 'Change Image' : 'Upload Image' }}
                                            </label>
                                        </div>

                                        @if (($cover_image_path && !$removeCoverImage) || $cover_image_file)
                                            <button type="button" wire:click="removeCoverImageFile"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="ri-delete-bin-6-line me-1"></i>Remove
                                            </button>
                                        @endif

                                        <div wire:loading wire:target="cover_image_file" class="text-primary small">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                            Uploading...
                                        </div>

                                        <small class="text-muted">JPG, PNG, WEBP &bull; Max 2 MB</small>

                                        @error('cover_image_file')
                                        <div class="text-danger small">
                                            <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium small">
                                    Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       placeholder="Assessment title...">
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium small">
                                    Instructions <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <textarea wire:model="instructions" class="form-control" rows="3"
                                          placeholder="Instructions shown to students..."></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Marks & Duration --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-trophy-line text-primary me-2"></i>Marks & Duration
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-md-3">
                                <label class="form-label fw-medium small">Total Marks</label>
                                <div class="form-control bg-light text-success fw-semibold">{{ $total_marks }}</div>
                                <small class="text-muted">Auto-calculated</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium small">
                                    Passing Marks <span class="text-danger">*</span>
                                </label>
                                <input type="number" wire:model="passing_marks"
                                       class="form-control @error('passing_marks') is-invalid @enderror"
                                       min="0" step="0.5">
                                @error('passing_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium small">
                                    Duration (minutes) <span class="text-danger">*</span>
                                </label>
                                <input type="number" wire:model="duration_minutes"
                                       class="form-control" min="1" placeholder="e.g. 60">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium small d-block">Has Negative Mark?</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox"
                                           wire:model.live="has_negative_mark"
                                           id="negMark" role="switch">
                                    <label class="form-check-label small" for="negMark">
                                        {{ $has_negative_mark ? 'Enabled' : 'Disabled' }}
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div class="col-lg-4">

                {{-- Assessment Settings --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-settings-4-line text-primary me-2"></i>Assessment Settings
                        </h6>
                    </div>
                    <div class="card-body p-3">

                        <div class="mb-3">
                            <label class="form-label fw-medium small">
                                <i class="ri ri-repeat-line me-1 text-primary"></i>
                                Max Attempts <span class="text-danger">*</span>
                            </label>
                            <input type="number" wire:model="max_attempts"
                                   class="form-control form-control-sm @error('max_attempts') is-invalid @enderror"
                                   min="1" placeholder="e.g. 1">
                            @error('max_attempts')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of times a student can attempt this assessment.</small>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="mb-0 small fw-medium">
                                        <i class="ri ri-shuffle-line me-1 text-primary"></i>Shuffle Sections
                                    </p>
                                    <small class="text-muted">Randomize the order of sections for each attempt.</small>
                                </div>
                                <div class="form-check form-switch ms-3 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           wire:model="shuffle_sections" id="shuffleSections">
                                    <label class="form-check-label small" for="shuffleSections">
                                        {{ $shuffle_sections ? 'Yes' : 'No' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="mb-0 small fw-medium">
                                        <i class="ri ri-bar-chart-line me-1 text-success"></i>Show Result Immediately
                                    </p>
                                    <small class="text-muted">Display result to student right after submission.</small>
                                </div>
                                <div class="form-check form-switch ms-3 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           wire:model.live="show_result_immediately" id="showResult">
                                    <label class="form-check-label small" for="showResult">
                                        {{ $show_result_immediately ? 'Yes' : 'No' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        @if ($show_result_immediately)
                            <hr class="my-3">
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 small fw-medium">
                                            <i class="ri ri-checkbox-circle-line me-1 text-success"></i>Show Correct Answers
                                        </p>
                                        <small class="text-muted">Show the correct answer along with the result.</small>
                                    </div>
                                    <div class="form-check form-switch ms-3 mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               wire:model.live="show_correct_answers" id="showCorrectAnswers">
                                        <label class="form-check-label small" for="showCorrectAnswers">
                                            {{ $show_correct_answers ? 'Yes' : 'No' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            @if ($show_correct_answers)
                                <hr class="my-3">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="mb-0 small fw-medium">
                                                <i class="ri ri-book-open-line me-1 text-info"></i>Show Explanations
                                            </p>
                                            <small class="text-muted">Show answer explanations with correct answers.</small>
                                        </div>
                                        <div class="form-check form-switch ms-3 mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   wire:model="show_explainations" id="showExplainations">
                                            <label class="form-check-label small" for="showExplainations">
                                                {{ $show_explainations ? 'Yes' : 'No' }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>

                {{-- Status & Save --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-settings-3-line text-primary me-2"></i>Status & Save
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <label class="form-label fw-medium small">Status</label>
                        <div class="d-flex gap-3 mb-3">
                            @foreach ([
                                'draft'     => ['secondary', 'ri-draft-line'],
                                'publish'   => ['success',   'ri-checkbox-circle-line'],
                                'unpublish' => ['warning',   'ri-eye-off-line'],
                            ] as $val => [$color, $icon])
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           wire:model="status" value="{{ $val }}"
                                           id="status-{{ $val }}">
                                    <label class="form-check-label small" for="status-{{ $val }}">
                                        <i class="ri {{ $icon }} text-{{ $color }} me-1"></i>
                                        {{ ucfirst($val) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <label class="form-label fw-medium small">Admin Note</label>
                        <textarea wire:model="admin_note" class="form-control border-0 bg-light" rows="3"
                                  placeholder="Internal note..."></textarea>
                    </div>
                    <div class="card-footer bg-white p-3">
                        <button type="button" wire:click="saveAssessment" wire:loading.attr="disabled"
                                class="btn btn-primary w-100">
                            <span wire:loading wire:target="saveAssessment">
                                <span class="spinner-border spinner-border-sm me-1"></span>Saving…
                            </span>
                            <span wire:loading.remove wire:target="saveAssessment">
                                <i class="ri ri-arrow-right-line me-1"></i>Save & Go to Builder
                            </span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

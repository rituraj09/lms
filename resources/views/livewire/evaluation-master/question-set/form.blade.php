{{--
    question-set/form.blade.php
    Step 1 — Question Set details form.
--}}
<div>
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="ri ri-stack-line text-primary me-2"></i>
                {{ $isEditing ? 'Edit' : 'New' }} Question Set
                <span class="badge bg-primary-subtle text-primary ms-2 fw-normal small">Step 1 of 3 — Set Details</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="#" wire:click.prevent="cancelForm">Question Sets</a></li>
                    <li class="breadcrumb-item active">{{ $isEditing ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="cancelForm">
            <i class="ri ri-arrow-left-line me-1"></i>Cancel
        </button>
    </div>

    @include('livewire.evaluation-master.partials.alerts')

    <form wire:submit.prevent="saveAndContinue">
        <div class="row g-4">

            {{-- ── Left Column ───────────────────────────── --}}
            <div class="col-lg-8">

                {{-- Basic Details --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-information-line text-primary me-2"></i>Basic Details
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- Code (readonly, auto-generated) --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">Code</label>
                                <input type="text" class="form-control bg-light" value="{{ $code }}"
                                    readonly />
                            </div>

                            {{-- Set Type --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">Set Type <span
                                        class="text-danger">*</span></label>
                                <select wire:model="questionSetType"
                                    class="form-select @error('questionSetType') is-invalid @enderror">
                                    <option value="">— Select Type —</option>
                                    @foreach (\App\Livewire\EvaluationMaster\QuestionSetManager::SET_TYPES as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('questionSetType')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Age Group --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">Age Group <span
                                        class="text-danger">*</span></label>
                                <select wire:model="ageGroupId"
                                    class="form-select @error('ageGroupId') is-invalid @enderror">
                                    <option value="">— Select Age Group —</option>
                                    @foreach ($ageGroups as $group)
                                        <option value="{{ $group['id'] }}">{{ $group['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('ageGroupId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title --}}
                            <div class="col-12">
                                <label class="form-label fw-medium small">Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model.live="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="e.g. Cognitive Ability Assessment — Level 1" />
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Slug --}}
                            <div class="col-12">
                                <label class="form-label fw-medium small">Slug</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted small">/sets/</span>
                                    <input type="text" wire:model="slug" class="form-control font-monospace small"
                                        placeholder="auto-generated-from-title" />
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label fw-medium small">Description <span
                                        class="text-muted">(optional)</span></label>
                                <textarea wire:model="description" class="form-control" rows="3"
                                    placeholder="Brief description of this question set..."></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Settings --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-settings-3-line text-secondary me-2"></i>Settings
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- Timer --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">
                                    Total Timer <span class="text-muted">(seconds)</span>
                                </label>
                                <input type="number" wire:model="timer"
                                    class="form-control @error('timer') is-invalid @enderror" min="1"
                                    max="65535" placeholder="e.g. 3600" />
                                @error('timer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small">Leave blank for no overall time limit.</div>
                            </div>

                            {{-- Passing Score --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">Passing Score <span
                                        class="text-muted">(optional)</span></label>
                                <input type="number" wire:model="passingScore" class="form-control" min="0"
                                    placeholder="e.g. 70" />
                                <div class="form-text small">Minimum score required to pass.</div>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">Status <span
                                        class="text-danger">*</span></label>
                                <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft">Draft</option>
                                    <option value="publish">Publish</option>
                                    <option value="unpublish">Unpublish</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Randomize --}}
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="randomizeQuestions"
                                        id="randomize" />
                                    <label class="form-check-label fw-medium small" for="randomize">
                                        Randomize question order
                                    </label>
                                </div>
                                <div class="form-text small">Questions will appear in random order for each candidate.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Right Column ──────────────────────────── --}}
            <div class="col-lg-4">

                {{-- Cover Image --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-image-line text-secondary me-2"></i>Cover Image
                            <span class="badge bg-secondary fw-normal ms-1">Optional</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        @if ($imagePath)
                            <div class="position-relative d-inline-block mb-3">
                                <img src="{{ Storage::url($imagePath) }}" alt="Cover"
                                    class="img-thumbnail w-100 rounded" style="max-height:180px;object-fit:cover;">
                                <button type="button" wire:click="removeImagePath"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0"
                                    style="width:24px;height:24px;" title="Remove">
                                    <i class="ri ri-close-line"></i>
                                </button>
                            </div>
                        @endif

                        @if (!$imagePath)
                            <div x-data="{ dragging: false }" @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; $refs.imgFile.files = $event.dataTransfer.files; $refs.imgFile.dispatchEvent(new Event('change'))"
                                :class="dragging ? 'border-primary bg-primary bg-opacity-5' : 'border-secondary'"
                                class="upload-dropzone border border-2 border-dashed rounded-3 text-center p-4"
                                style="cursor:pointer;" @click="$refs.imgFile.click()">
                                <input type="file" x-ref="imgFile" wire:model="imageUpload"
                                    accept="image/jpeg,image/png,image/gif,image/webp" class="d-none" />
                                <div wire:loading wire:target="imageUpload" class="text-muted small">
                                    <span class="spinner-border spinner-border-sm me-1"></span>Uploading…
                                </div>
                                <div wire:loading.remove wire:target="imageUpload">
                                    <i class="ri ri-upload-cloud-2-line fs-2 text-muted"></i>
                                    <p class="mb-1 small fw-medium text-dark mt-1">Click or drag &amp; drop</p>
                                    <p class="mb-0 small text-muted">JPEG, PNG, GIF, WebP — max 4 MB</p>
                                </div>
                            </div>
                            @error('imageUpload')
                                <div class="text-danger small mt-1"><i
                                        class="ri ri-error-warning-line me-1"></i>{{ $message }}</div>
                            @enderror
                        @endif

                        @if ($imageUpload)
                            <div class="mt-3 d-flex align-items-start gap-2">
                                <img src="{{ $imageUpload->temporaryUrl() }}" class="img-thumbnail rounded"
                                    style="max-height:100px;object-fit:cover;">
                                <div>
                                    <p class="small fw-medium mb-1">{{ $imageUpload->getClientOriginalName() }}</p>
                                    <p class="small text-muted mb-2">
                                        {{ number_format($imageUpload->getSize() / 1024, 1) }} KB</p>
                                    <button type="button" wire:click="removeImageUpload"
                                        class="btn btn-outline-danger btn-sm">
                                        <i class="ri ri-delete-bin-line me-1"></i>Remove
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Step indicator --}}
                <div class="card border-0 bg-opacity-5 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3 text-primary">Creation Steps</h6>
                        <div class="d-flex flex-column gap-2">
                            @foreach ([['num' => 1, 'label' => 'Set Details', 'desc' => 'Title, type, timer', 'done' => true], ['num' => 2, 'label' => 'Add Groups', 'desc' => 'Organise into sub-groups', 'done' => false], ['num' => 3, 'label' => 'Add Questions', 'desc' => 'Pick questions per group', 'done' => false]] as $step)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold small
                                        {{ $step['done'] ? 'bg-primary text-white' : 'bg-white border text-muted' }}"
                                        style="width:30px;height:30px;">
                                        {{ $step['num'] }}
                                    </div>
                                    <div>
                                        <div
                                            class="small fw-semibold {{ $step['done'] ? 'text-primary' : 'text-muted' }}">
                                            {{ $step['label'] }}
                                        </div>
                                        <div class="small text-muted">{{ $step['desc'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Save & Continue --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                        <span wire:loading wire:target="saveAndContinue">
                            <span class="spinner-border spinner-border-sm me-2"></span>Saving…
                        </span>
                        <span wire:loading.remove wire:target="saveAndContinue">
                            <i class="ri ri-arrow-right-line me-2"></i>Save &amp; Continue to Builder
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

@push('styles')
    <style>
        .upload-dropzone {
            transition: border-color .2s, background .2s;
        }

        .upload-dropzone:hover {
            border-color: var(--bs-primary) !important;
        }
    </style>
@endpush

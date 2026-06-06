{{--
    question/form/options.blade.php
    MCQ answer options — each option supports text OR image+label mode.
    Requires Alpine `activeTab` from parent x-data scope.
--}}
<div class="card shadow-sm border-0 mb-4">

    {{-- ── Card Header ─────────────────────────────────────── --}}
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

            <h6 class="mb-0 fw-semibold text-dark">
                <i class="ri ri-list-check-3 text-primary me-2"></i>
                Answer Options
                <span class="badge bg-primary ms-1">{{ count($options) }}</span>
            </h6>

            <div class="d-flex align-items-center gap-3">

                {{-- Single / Multiple toggle --}}
                <div class="d-flex align-items-center gap-1 bg-light rounded p-1">
                    <button type="button"
                        wire:click="$set('selectionType', 'single')"
                        class="btn btn-sm {{ $selectionType === 'single' ? 'btn-primary' : 'btn-light' }}">
                        <i class="ri ri-record-circle-line me-1"></i>Single
                    </button>
                    <button type="button"
                        wire:click="$set('selectionType', 'multiple')"
                        class="btn btn-sm {{ $selectionType === 'multiple' ? 'btn-primary' : 'btn-light' }}">
                        <i class="ri ri-checkbox-circle-line me-1"></i>Multiple
                    </button>
                </div>

                <button type="button"
                    wire:click="addOption"
                    class="btn btn-outline-primary btn-sm"
                    @if(count($options) >= 8) disabled @endif>
                    <i class="ri ri-add-large-line me-1"></i>Add Option
                </button>

            </div>
        </div>

        @if ($selectionType === 'multiple')
            <div class="mt-2">
                <span class="badge bg-info-subtle text-info border border-info-subtle small">
                    <i class="ri ri-information-line me-1"></i>
                    Multi-select: students can choose multiple answers. Set weightage per correct option.
                </span>
            </div>
        @endif
    </div>

    {{-- ── Card Body ───────────────────────────────────────── --}}
    <div class="card-body p-4">

        @error('options')
            <div class="alert alert-warning py-2 small mb-3">
                <i class="ri ri-error-warning-fill me-1"></i>{{ $message }}
            </div>
        @enderror

        @foreach ($options as $index => $option)
            @php $isImage = ($option['option_type'] ?? 'text') === 'image'; @endphp

            <div class="option-card rounded-3 border mb-3 overflow-hidden
                {{ $option['is_correct'] ? 'border-success' : 'border-light' }}"
                wire:key="option-{{ $index }}">

                {{-- ── Option Header ──────────────────────── --}}
                <div class="option-header d-flex align-items-center gap-2 px-3 py-2 flex-wrap
                    {{ $option['is_correct'] ? 'bg-success-subtle' : 'bg-light' }}">

                    {{-- Correct toggle --}}
                    <button type="button"
                        wire:click="toggleCorrect({{ $index }})"
                        class="btn btn-sm {{ $option['is_correct'] ? 'btn-success' : 'btn-outline-secondary' }} rounded-circle p-0 flex-shrink-0"
                        style="width:32px;height:32px;"
                        title="{{ $option['is_correct'] ? 'Mark as Incorrect' : 'Mark as Correct' }}">
                        @if ($selectionType === 'single')
                            <i class="ri {{ $option['is_correct'] ? 'ri-record-circle-fill' : 'ri-circle-line' }}"></i>
                        @else
                            <i class="ri {{ $option['is_correct'] ? 'ri-checkbox-circle-fill' : 'ri-checkbox-circle-line' }}"></i>
                        @endif
                    </button>

                    {{-- Option label badge --}}
                    <span class="badge bg-secondary fs-6 fw-bold d-inline-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:30px;height:30px;">
                        {{ $option['id'] }}
                    </span>

                    @if ($option['is_correct'])
                        <span class="badge bg-success">
                            <i class="ri ri-check-fill me-1"></i>Correct
                        </span>
                    @endif

                    {{-- ── Text / Image type toggle ─────────────── --}}
                    <div class="d-flex align-items-center gap-1 bg-white rounded p-1 border ms-1">
                        <button type="button"
                            wire:click="setOptionType({{ $index }}, 'text')"
                            class="btn btn-xs btn-sm py-0 px-2 {{ ! $isImage ? 'btn-primary' : 'btn-light' }}"
                            title="Text option">
                            <i class="ri ri-text me-1"></i>Text
                        </button>
                        <button type="button"
                            wire:click="setOptionType({{ $index }}, 'image')"
                            class="btn btn-xs btn-sm py-0 px-2 {{ $isImage ? 'btn-primary' : 'btn-light' }}"
                            title="Image option">
                            <i class="ri ri-image-line me-1"></i>Image
                        </button>
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-2">

                        {{-- Weightage --}}
                        <div class="d-flex align-items-center gap-1">
                            <label class="small text-muted mb-0 text-nowrap">Weightage:</label>
                            <input type="number"
                                wire:model.live="options.{{ $index }}.weightage"
                                class="form-control form-control-sm text-center"
                                style="width:70px;"
                                step="0.1" min="0" max="100"
                                placeholder="0" />
                        </div>

                        {{-- Remove --}}
                        <button type="button"
                            wire:click="removeOption({{ $index }})"
                            class="btn btn-sm btn-outline-danger"
                            title="Remove option">
                            <i class="ri ri-delete-bin-fill"></i>
                        </button>

                    </div>
                </div>

                {{-- ── Option Body ─────────────────────────── --}}
                <div class="option-body p-3">

                    @if (! $isImage)
                        {{-- TEXT MODE: multilingual text inputs --}}
                        @foreach ($languages as $langCode => $lang)
                            <div x-show="activeTab === '{{ $langCode }}'">
                                <input type="text"
                                    wire:model="options.{{ $index }}.text.{{ $langCode }}"
                                    class="form-control form-control-sm"
                                    placeholder="Option {{ $option['id'] }} — {{ $lang['label'] }}..." />
                            </div>
                        @endforeach

                    @else
                        {{-- IMAGE MODE: upload + optional text label --}}
                        <div class="row g-3 align-items-start">

                            {{-- Image upload / preview column --}}
                            <div class="col-md-5"
                                x-data="{ dragging: false }"
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="
                                    dragging = false;
                                    $refs.optFile{{ $index }}.files = $event.dataTransfer.files;
                                    $refs.optFile{{ $index }}.dispatchEvent(new Event('change'))
                                ">

                                {{-- Persisted image --}}
                                @if (! empty($option['image_path']))
                                    <div class="position-relative d-inline-block mb-2">
                                        <img src="{{ Storage::url($option['image_path']) }}"
                                             alt="Option image"
                                             class="img-thumbnail rounded"
                                             style="max-height:120px;object-fit:contain;">
                                        <button type="button"
                                            wire:click="removeOptionImagePath({{ $index }})"
                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0"
                                            style="width:22px;height:22px;line-height:1;"
                                            title="Remove">
                                            <i class="ri ri-close-line" style="font-size:.75rem;"></i>
                                        </button>
                                    </div>
                                @endif

                                {{-- New upload dropzone --}}
                                @if (empty($option['image_path']))
                                    <div :class="dragging ? 'border-primary bg-primary bg-opacity-5' : 'border-secondary'"
                                         class="upload-dropzone border border-2 border-dashed rounded-3 text-center p-3"
                                         style="cursor:pointer;min-height:90px;"
                                         @click="$refs.optFile{{ $index }}.click()">

                                        <input type="file"
                                            x-ref="optFile{{ $index }}"
                                            wire:model="optionImageUploads.{{ $index }}"
                                            accept="image/jpeg,image/png,image/gif"
                                            class="d-none" />

                                        <div wire:loading wire:target="optionImageUploads.{{ $index }}" class="text-muted small">
                                            <span class="spinner-border spinner-border-sm"></span>
                                        </div>
                                        <div wire:loading.remove wire:target="optionImageUploads.{{ $index }}">
                                            <i class="ri ri-upload-cloud-2-line fs-4 text-muted"></i>
                                            <p class="mb-0 small text-muted mt-1">JPEG / PNG / GIF</p>
                                        </div>
                                    </div>
                                    @error("optionImageUploads.{$index}")
                                        <div class="text-danger small mt-1">
                                            <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                @endif

                                {{-- Staged image preview --}}
                                @if (! empty($optionImageUploads[$index]))
                                    <div class="mt-2 d-flex align-items-start gap-2">
                                        <img src="{{ $optionImageUploads[$index]->temporaryUrl() }}"
                                             alt="Preview"
                                             class="img-thumbnail rounded"
                                             style="max-height:80px;object-fit:contain;">
                                        <button type="button"
                                            wire:click="removeOptionImageUpload({{ $index }})"
                                            class="btn btn-outline-danger btn-sm">
                                            <i class="ri ri-close-line"></i>
                                        </button>
                                    </div>
                                @endif

                            </div>

                            {{-- Optional text label column --}}
                            <div class="col-md-7">
                                <label class="form-label small text-muted mb-1">
                                    <i class="ri ri-text me-1"></i>Label <span class="fw-normal">(optional)</span>
                                </label>
                                @foreach ($languages as $langCode => $lang)
                                    <div x-show="activeTab === '{{ $langCode }}'" class="mb-1">
                                        <input type="text"
                                            wire:model="options.{{ $index }}.text.{{ $langCode }}"
                                            class="form-control form-control-sm"
                                            placeholder="{{ $lang['flag'] }} Label in {{ $lang['label'] }}..." />
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endif

                </div>
            </div>
        @endforeach

        {{-- Summary --}}
        <div class="d-flex gap-3 pt-2 border-top">
            <small class="text-muted">
                <i class="ri ri-check-circle-fill text-success me-1"></i>
                Correct: <strong>{{ $this->correctOptionsCount }}</strong>
            </small>
            <small class="text-muted">
                <i class="ri ri-list-ordered-2 me-1"></i>
                Total: <strong>{{ count($options) }}</strong>
            </small>
        </div>

    </div>
</div>

{{--
    partials/form/options.blade.php
    MCQ answer options card: add / remove / toggle correct / weightage.
    Requires Alpine `activeTab` from parent x-data scope.
--}}
<div class="card shadow-sm border-0 mb-4">

    {{-- Card Header --}}
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

    {{-- Card Body --}}
    <div class="card-body p-4">

        @error('options')
            <div class="alert alert-warning py-2 small mb-3">
                <i class="ri ri-error-warning-fill me-1"></i>{{ $message }}
            </div>
        @enderror

        {{-- Option rows --}}
        @foreach ($options as $index => $option)
            <div class="option-card rounded-3 border mb-3 overflow-hidden
                {{ $option['is_correct'] ? 'border-success' : 'border-light' }}"
                wire:key="option-{{ $index }}">

                {{-- Option Header Row --}}
                <div class="option-header d-flex align-items-center gap-3 px-3 py-2
                    {{ $option['is_correct'] ? 'bg-success-subtle' : 'bg-light' }}">

                    {{-- Correct toggle button --}}
                    <button type="button"
                        wire:click="toggleCorrect({{ $index }})"
                        class="btn btn-sm {{ $option['is_correct'] ? 'btn-success' : 'btn-outline-secondary' }} rounded-circle p-0"
                        style="width:32px;height:32px;"
                        title="{{ $option['is_correct'] ? 'Mark as Incorrect' : 'Mark as Correct' }}">
                        @if ($selectionType === 'single')
                            <i class="ri {{ $option['is_correct'] ? 'ri-record-circle-fill' : 'ri-circle-line' }}"></i>
                        @else
                            <i class="ri {{ $option['is_correct'] ? 'ri-checkbox-circle-fill' : 'ri-checkbox-circle-line' }}"></i>
                        @endif
                    </button>

                    {{-- Option label badge --}}
                    <span class="badge bg-secondary fs-6 fw-bold d-inline-flex align-items-center justify-content-center"
                        style="width:30px;height:30px;">
                        {{ $option['id'] }}
                    </span>

                    @if ($option['is_correct'])
                        <span class="badge bg-success">
                            <i class="ri ri-check-fill me-1"></i>Correct
                        </span>
                    @endif

                    <div class="ms-auto d-flex align-items-center gap-2">

                        {{-- Weightage input --}}
                        <div class="d-flex align-items-center gap-1">
                            <label class="small text-muted mb-0 text-nowrap">Weightage:</label>
                            <input type="number"
                                wire:model.live="options.{{ $index }}.weightage"
                                class="form-control form-control-sm text-center"
                                style="width:70px;"
                                step="0.1" min="0" max="100"
                                placeholder="0" />
                        </div>

                        {{-- Remove button --}}
                        <button type="button"
                            wire:click="removeOption({{ $index }})"
                            class="btn btn-sm btn-outline-danger"
                            title="Remove option">
                            <i class="ri ri-delete-bin-fill"></i>
                        </button>

                    </div>
                </div>

                {{-- Option Text Inputs (multilingual) --}}
                <div class="option-body p-3">
                    @foreach ($languages as $langCode => $lang)
                        <div x-show="activeTab === '{{ $langCode }}'">
                            <input type="text"
                                wire:model="options.{{ $index }}.text.{{ $langCode }}"
                                class="form-control form-control-sm"
                                placeholder="Option {{ $option['id'] }} — {{ $lang['label'] }}..." />
                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach

        {{-- Summary row --}}
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

{{--
    partials/question-picker.blade.php
    Full-screen modal — search + checkbox select questions for a group.
--}}
<div
    class="modal d-flex align-items-start justify-content-center"
    style="display:flex!important; background:rgba(0,0,0,.5); position:fixed; inset:0; z-index:1055; overflow-y:auto; padding:2rem 1rem;">

    <div class="modal-dialog modal-xl w-100 my-0" style="max-width:1100px;">
        <div class="modal-content shadow-lg border-0">

            {{-- Modal Header --}}
            <div class="modal-header border-bottom py-3">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="ri ri-question-line text-primary me-2"></i>
                        Add Questions to Group
                    </h5>
                    <p class="small text-muted mb-0 mt-1">
                        Search, filter and select questions. Already-assigned questions are hidden.
                    </p>
                </div>
                <button type="button" wire:click="closePicker" class="btn-close ms-3"></button>
            </div>

            {{-- Search + Filter bar --}}
            <div class="modal-body border-bottom py-3 bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="ri ri-search-line text-muted"></i>
                            </span>
                            <input type="text"
                                wire:model.live.debounce.300ms="pickerSearch"
                                class="form-control"
                                placeholder="Search by question code..." />
                            @if ($pickerSearch)
                                <button type="button" wire:click="$set('pickerSearch', '')"
                                    class="btn btn-outline-secondary">
                                    <i class="ri ri-close-line"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select wire:model.live="pickerTypeFilter" class="form-select">
                            <option value="">All Question Types</option>
                            @foreach ($questionTypes as $type)
                                <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                            {{ count($pickerSelected) }} selected
                        </span>
                    </div>
                </div>
            </div>

            {{-- Question Grid --}}
            <div class="modal-body p-4" style="max-height:55vh; overflow-y:auto;">

                @if ($this->pickerQuestions->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ri ri-search-line fs-2 d-block mb-2"></i>
                        <span class="small">No published questions found matching your filters.</span>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($this->pickerQuestions as $question)
                            @php
                                $isSelected = in_array($question->id, $pickerSelected);
                                $contents   = json_decode($question->question_contents, true);
                                $stem       = $contents['stem']['en'] ?? ($contents['stem'][array_key_first($contents['stem'] ?? ['en'=>''])] ?? '');
                            @endphp

                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 border question-picker-card
                                    {{ $isSelected ? 'border-primary bg-primary bg-opacity-5' : '' }}"
                                    style="cursor:pointer; transition: all .15s;"
                                    wire:click="togglePickerQuestion({{ $question->id }})">

                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-start gap-2 mb-2">
                                            {{-- Checkbox --}}
                                            <div class="form-check flex-shrink-0 mt-1">
                                                <input class="form-check-input" type="checkbox"
                                                    @checked($isSelected)
                                                    onclick="event.stopPropagation()"
                                                    wire:click.stop="togglePickerQuestion({{ $question->id }})" />
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <span class="badge bg-light text-dark border font-monospace small d-block mb-1">
                                                    {{ $question->code }}
                                                </span>
                                                <span class="badge bg-secondary-subtle text-secondary small">
                                                    {{ $question->questionType->name ?? '—' }}
                                                </span>
                                            </div>
                                            <span class="badge bg-success-subtle text-success flex-shrink-0">
                                                {{ number_format($question->max_score, 1) }} pts
                                            </span>
                                        </div>

                                        {{-- Stem preview --}}
                                        <p class="small text-muted mb-0 mt-2" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                            {!! strip_tags($stem) ?: '<em>No stem text</em>' !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $this->pickerQuestions->links() }}
                    </div>
                @endif

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-top py-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted">
                    <i class="ri ri-information-line me-1"></i>
                    Click cards to select. Already-assigned questions are not shown.
                </span>
                <div class="d-flex gap-2">
                    <button type="button" wire:click="closePicker" class="btn btn-outline-secondary">
                        Cancel
                    </button>
                    <button type="button"
                        wire:click="addSelectedToGroup"
                        class="btn btn-primary"
                        @if(empty($pickerSelected)) disabled @endif
                        wire:loading.attr="disabled" wire:target="addSelectedToGroup">
                        <span wire:loading wire:target="addSelectedToGroup">
                            <span class="spinner-border spinner-border-sm me-1"></span>Adding…
                        </span>
                        <span wire:loading.remove wire:target="addSelectedToGroup">
                            <i class="ri ri-add-large-line me-1"></i>
                            Add {{ count($pickerSelected) }} Question{{ count($pickerSelected) !== 1 ? 's' : '' }}
                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    .question-picker-card:hover { border-color: var(--bs-primary) !important; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
</style>
@endpush

{{--
    partials/preview/options.blade.php
    Answer-options list inside the preview card.
    Requires Alpine `previewTab` in ancestor x-data.
--}}
<div class="preview-section mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="bg-success bg-opacity-10 p-2 rounded">
            <i class="ri ri-list-check-3 text-success"></i>
        </div>
        <h6 class="mb-0 fw-semibold">Answer Options</h6>

        <span class="badge bg-info ms-1">
            <i class="ri ri-{{ $selectionType === 'single' ? 'record-circle' : 'checkbox-circle' }}-line me-1"></i>
            {{ $selectionType === 'single' ? 'Single Select' : 'Multiple Select' }}
        </span>

        @if ($isOptionsShuffle)
            <span class="badge bg-secondary">
                <i class="ri ri-shuffle-line me-1"></i>Shuffle
            </span>
        @endif
    </div>

    @foreach ($options as $option)
        <div class="option-preview-item mb-3 p-3 rounded border
            {{ $option['is_correct'] ? 'border-success bg-success bg-opacity-10' : 'border-secondary' }}">

            <div class="d-flex align-items-start gap-3">

                {{-- Option label --}}
                <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:40px;height:40px;">
                    <span class="fw-bold fs-5">{{ $option['id'] }}</span>
                </div>

                {{-- Option text --}}
                <div class="flex-grow-1">
                    @foreach ($languages as $langCode => $lang)
                        <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                            <p class="mb-0">
                                {{ $option['text'][$langCode]
                                    ?: ($option['text'][array_key_first($languages)]
                                    ?: 'No text available.') }}
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- Correct badge --}}
                @if ($option['is_correct'])
                    <div class="flex-shrink-0">
                        <span class="badge bg-success px-3 py-2">
                            <i class="ri ri-check-fill me-1"></i>Correct
                            @if ($selectionType === 'multiple' && $option['weightage'] > 0)
                                ({{ number_format((float) $option['weightage'], 1) }} pts)
                            @endif
                        </span>
                    </div>
                @endif

            </div>
        </div>
    @endforeach
</div>

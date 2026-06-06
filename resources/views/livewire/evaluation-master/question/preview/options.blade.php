{{--
    question/preview/options.blade.php
    Read-only options display — handles both text and image option types.
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

    @foreach ($options as $idx => $option)
        @php
            $isImage     = ($option['option_type'] ?? 'text') === 'image';
            $isCorrect   = $option['is_correct'] ?? false;
            $imagePath   = $option['image_path'] ?? null;
            $stagedImage = $optionImageUploads[$idx] ?? null;
        @endphp

        <div class="option-preview-item mb-3 p-3 rounded border
            {{ $isCorrect ? 'border-success bg-success bg-opacity-10' : 'border-secondary' }}">

            <div class="d-flex align-items-start gap-3">

                {{-- Option letter badge --}}
                <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:40px;height:40px;">
                    <span class="fw-bold fs-5">{{ $option['id'] }}</span>
                </div>

                {{-- Option content --}}
                <div class="flex-grow-1">

                    @if ($isImage)
                        {{-- Image (persisted or staged) --}}
                        @if ($imagePath)
                            <img src="{{ Storage::url($imagePath) }}"
                                 alt="Option {{ $option['id'] }}"
                                 class="img-thumbnail rounded mb-2"
                                 style="max-height:120px; object-fit:contain;">
                        @elseif ($stagedImage)
                            <img src="{{ $stagedImage->temporaryUrl() }}"
                                 alt="Option {{ $option['id'] }} preview"
                                 class="img-thumbnail rounded mb-2"
                                 style="max-height:120px; object-fit:contain;">
                        @else
                            <span class="badge bg-light text-muted border mb-2">
                                <i class="ri ri-image-line me-1"></i>No image uploaded
                            </span>
                        @endif

                        {{-- Optional text label below image --}}
                        @foreach ($languages as $langCode => $lang)
                            <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                                @if (! empty($option['text'][$langCode]))
                                    <p class="mb-0 small text-muted">{{ $option['text'][$langCode] }}</p>
                                @endif
                            </div>
                        @endforeach

                    @else
                        {{-- Text option --}}
                        @foreach ($languages as $langCode => $lang)
                            <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                                <p class="mb-0">
                                    {{ $option['text'][$langCode]
                                        ?: ($option['text'][array_key_first($languages)]
                                        ?: 'No text available.') }}
                                </p>
                            </div>
                        @endforeach
                    @endif

                </div>

                {{-- Correct badge --}}
                @if ($isCorrect)
                    <div class="flex-shrink-0">
                        <span class="badge bg-success px-3 py-2">
                            <i class="ri ri-check-fill me-1"></i>Correct
                            @if ($selectionType === 'multiple' && ($option['weightage'] ?? 0) > 0)
                                ({{ number_format((float) $option['weightage'], 1) }} pts)
                            @endif
                        </span>
                    </div>
                @endif

            </div>
        </div>
    @endforeach
</div>

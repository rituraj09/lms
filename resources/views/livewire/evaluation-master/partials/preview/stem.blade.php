{{--
    partials/preview/stem.blade.php
    Question stem section inside the preview card.
    Requires Alpine `previewTab` in ancestor x-data.
--}}
<div class="preview-section mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="bg-primary bg-opacity-10 p-2 rounded">
            <i class="ri ri-question-line text-primary"></i>
        </div>
        <h6 class="mb-0 fw-semibold">Question Stem</h6>
    </div>

    <div class="bg-light rounded p-4">
        @foreach ($languages as $langCode => $lang)
            <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                <p class="mb-0 fs-5 fw-medium">
                    {{ $stem[$langCode] ?: ($stem[array_key_first($languages)] ?? 'No question stem available.') }}
                </p>
            </div>
        @endforeach
    </div>
</div>

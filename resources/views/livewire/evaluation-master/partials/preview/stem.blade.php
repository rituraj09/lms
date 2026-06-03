{{--
    partials/preview/stem.blade.php
    Read-only stem display including optional stem image.
    Requires Alpine `previewTab` in ancestor x-data.
--}}
<div class="preview-section mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="bg-primary bg-opacity-10 p-2 rounded">
            <i class="ri ri-question-line text-primary"></i>
        </div>
        <h6 class="mb-0 fw-semibold">Question Stem</h6>
    </div>

    {{-- Stem image --}}
    @if ($stemImagePath || $stemImageUpload)
        <div class="mb-3">
            @if ($stemImagePath)
                <img src="{{ Storage::url($stemImagePath) }}"
                     alt="Stem image"
                     class="img-fluid rounded shadow-sm"
                     style="max-height:260px; object-fit:contain;">
            @elseif ($stemImageUpload)
                <img src="{{ $stemImageUpload->temporaryUrl() }}"
                     alt="Stem image preview"
                     class="img-fluid rounded shadow-sm"
                     style="max-height:260px; object-fit:contain;">
            @endif
        </div>
    @endif

    {{-- Stem text --}}
    <div class="bg-light rounded p-4">
        @foreach ($languages as $langCode => $lang)
            <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                @php $text = $stem[$langCode] ?? ($stem[array_key_first($languages)] ?? ''); @endphp
                @if ($text)
                    <div class="fs-5 fw-medium">{!! $text !!}</div>
                @else
                    <p class="mb-0 text-muted fst-italic">No question stem available.</p>
                @endif
            </div>
        @endforeach
    </div>
</div>

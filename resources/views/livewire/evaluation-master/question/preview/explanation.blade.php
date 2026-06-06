{{--
    question/preview/explanation.blade.php
    Optional explanation section inside the preview card.
    Only renders when at least one language has content.
    Requires Alpine `previewTab` in ancestor x-data.
--}}
@php
    $hasExplanation = collect($languages)->keys()->contains(
        fn ($lang) => ! empty($explanation[$lang])
    );
@endphp

@if ($hasExplanation)
    <div class="preview-section">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div class="bg-warning bg-opacity-10 p-2 rounded">
                <i class="ri ri-lightbulb-fill text-warning"></i>
            </div>
            <h6 class="mb-0 fw-semibold">Explanation</h6>
        </div>

        <div class="alert alert-info border-0">
            @foreach ($languages as $langCode => $lang)
                <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                    @if (! empty($explanation[$langCode]))
                        {!! nl2br(e($explanation[$langCode])) !!}
                    @else
                        <em class="text-muted">No explanation available in {{ $lang['label'] }}.</em>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

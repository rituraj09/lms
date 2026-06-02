{{--
    partials/form/explanation.blade.php
    Optional multilingual explanation card.
    Requires Alpine `activeTab` from parent x-data scope.
--}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-lightbulb-fill text-warning me-2"></i>
            Explanation
            <span class="text-muted fw-normal small ms-1">(Optional)</span>
        </h6>
    </div>

    <div class="card-body p-4">
        @foreach ($languages as $langCode => $lang)
            <div x-show="activeTab === '{{ $langCode }}'">
                <textarea
                    wire:model="explanation.{{ $langCode }}"
                    class="form-control"
                    rows="3"
                    placeholder="Explain the correct answer in {{ $lang['label'] }}..."></textarea>
            </div>
        @endforeach
    </div>
</div>

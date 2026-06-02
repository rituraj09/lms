{{--
    partials/form/stem.blade.php
    Multilingual question stem card.
    Requires Alpine `activeTab` from parent x-data scope.
--}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-translate-2 me-2"></i>Question Stem
        </h6>

        {{-- Language tabs --}}
        <ul class="nav nav-pills nav-sm mb-0">
            @foreach ($languages as $langCode => $lang)
                <li class="nav-item">
                    <button type="button"
                        @click="activeTab = '{{ $langCode }}'"
                        class="nav-link"
                        :class="{ 'active': activeTab === '{{ $langCode }}' }">
                        {{ $lang['flag'] }} {{ $lang['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card-body p-4">
        @foreach ($languages as $langCode => $lang)
            <div x-show="activeTab === '{{ $langCode }}'" x-cloak>
                <label class="form-label fw-medium small mb-2">
                    {{ $lang['flag'] }} {{ $lang['label'] }} — Question Stem
                </label>
                <textarea wire:model="stem.{{ $langCode }}"
                    class="form-control"
                    rows="4"
                    placeholder="Enter question stem in {{ $lang['label'] }}..."></textarea>
            </div>
        @endforeach
    </div>
</div>

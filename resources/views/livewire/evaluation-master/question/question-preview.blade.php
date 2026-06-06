{{--
    question/preview.blade.php
    Preview & Publish view.
    Included by question-manager.blade.php when $view === 3.
--}}
@php
    $firstLanguage = collect(array_keys($languages))
        ->first(fn($lang) => !empty(trim($stem[$lang] ?? '')))
        ?? array_key_first($languages);
@endphp

<div class="container-fluid px-0"
    x-data="{ previewTab: '{{ $firstLanguage }}' }">

    {{-- ══ Page Header ══════════════════════════════════════ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="ri ri-eye-fill text-primary me-2"></i>Question Preview
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="#" wire:click.prevent="backToForm">Question Form</a>
                    </li>
                    <li class="breadcrumb-item active">Preview &amp; Publish</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-outline-secondary" wire:click="backToForm">
            <i class="ri ri-arrow-left-line me-1"></i> Back to Edit
        </button>
    </div>

    {{-- ══ Alert Messages ═══════════════════════════════════ --}}
    @include('livewire.evaluation-master.partials.alerts')

    <div class="row g-4">

        {{-- ── Left: Question Content ──────────────────────── --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">

                {{-- Card Header + language tabs --}}
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-file-copy-line text-primary me-2"></i>Question Content
                        </h6>
                        @if (count($languages) > 1)
                            <ul class="nav nav-pills nav-sm mb-0">
                               @foreach ($languages as $langCode => $lang)
                                    @if(!empty(trim($stem[$langCode] ?? '')))
                                        <li class="nav-item">
                                            <button type="button"
                                                @click="previewTab = '{{ $langCode }}'"
                                                class="nav-link"
                                                :class="{ 'active': previewTab === '{{ $langCode }}' }">
                                                {{ $lang['flag'] }} {{ $lang['label'] }}
                                            </button>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">

                    {{-- Stem --}}
                    @include('livewire.evaluation-master.question.preview.stem')

                    {{-- Options --}}
                    @include('livewire.evaluation-master.question.preview.options')

                    {{-- Explanation --}}
                    @include('livewire.evaluation-master.question.preview.explanation')

                </div>
            </div>
        </div>

        {{-- ── Right: Meta & Publishing ────────────────────── --}}
        <div class="col-lg-4">

            {{-- Score Summary --}}
            @include('livewire.evaluation-master.question.preview.score-summary')

            {{-- Quick Info --}}
            @include('livewire.evaluation-master.question.preview.quick-info')

            {{-- Status & Publish --}}
            @include('livewire.evaluation-master.question.preview.publish')

        </div>

    </div>
</div>

@push('styles')
<style>
    .preview-section { animation: fadeInUp 0.3s ease; }
    .option-preview-item { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .option-preview-item:hover { transform: translateX(5px); box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .status-option { transition: transform 0.2s ease; cursor: pointer; }
    .status-option:hover { transform: translateX(5px); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

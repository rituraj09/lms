<div class="card shadow-sm border-0 mb-4">

    {{-- ── Card Header + Language Tabs ────────────────────── --}}
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
{{--                <textarea wire:model="stem.{{ $langCode }}"--}}
{{--                    rows="4"--}}
{{--                    class="form-control @error('stem') is-invalid @enderror"--}}
{{--                    placeholder="Enter question stem in {{ $lang['label'] }}..."></textarea>--}}
                <div
                    wire:ignore
                    x-data="{
                        content: @js($stem[$langCode])
                    }"
                    x-init="
                    const quill = new Quill($refs.editor, {
                        theme: 'snow',
                        placeholder: 'Enter question stem in {{ $lang['label'] }}...',
                        modules: {
                            toolbar: fullToolbar,
                            syntax: true,
                            formula: true
                        }
                    });
                    if(content)
                    {
                       quill.root.innerHTML = content;
                    }
                    quill.on('selection-change', function(range) {
                        if (range === null) {
                            $wire.set('stem.{{ $langCode }}', quill.root.innerHTML);
                        }
                    });


                "
                >
                    <div x-ref="editor" style="height:250px"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     STEM IMAGE UPLOAD  (always visible, not mandatory)
     ═══════════════════════════════════════════════════════════ --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
        <i class="ri ri-image-line text-primary"></i>
        <h6 class="mb-0 fw-semibold text-dark">Stem Image</h6>
        <span class="badge bg-secondary fw-normal ms-1">Optional</span>
    </div>

    <div class="card-body p-4">

        {{-- ── Already-persisted image (edit mode) ─────────── --}}
        @if ($stemImagePath)
            <div class="mb-3">
                <p class="small text-muted mb-2">Current image:</p>
                <div class="position-relative d-inline-block">
                    <img src="{{ Storage::url($stemImagePath) }}"
                         alt="Stem image"
                         class="img-thumbnail rounded"
                         style="max-height:180px; object-fit:contain;">
                    <button type="button"
                        wire:click="removeStemImagePath"
                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0"
                        style="width:24px;height:24px;line-height:1;"
                        title="Remove image">
                        <i class="ri ri-close-line"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- ── New upload ───────────────────────────────────── --}}
        @if (! $stemImagePath)
            <div x-data="{ dragging: false }"
                 @dragover.prevent="dragging = true"
                 @dragleave.prevent="dragging = false"
                 @drop.prevent="dragging = false; $refs.stemFile.files = $event.dataTransfer.files; $refs.stemFile.dispatchEvent(new Event('change'))"
                 :class="dragging ? 'border-primary bg-primary bg-opacity-5' : 'border-secondary'"
                 class="upload-dropzone border border-2 border-dashed rounded-3 text-center p-4 position-relative"
                 style="cursor:pointer;"
                 @click="$refs.stemFile.click()">

                <input type="file"
                    x-ref="stemFile"
                    wire:model="stemImageUpload"
                    accept="image/jpeg,image/png,image/gif"
                    class="d-none" />

                <div wire:loading wire:target="stemImageUpload" class="text-muted small">
                    <span class="spinner-border spinner-border-sm me-1"></span> Uploading…
                </div>
                <div wire:loading.remove wire:target="stemImageUpload">
                    <i class="ri ri-upload-cloud-2-line fs-2 text-muted"></i>
                    <p class="mb-1 small fw-medium text-dark mt-1">Click or drag &amp; drop</p>
                    <p class="mb-0 small text-muted">JPEG, PNG, GIF — max 2 MB</p>
                </div>
            </div>
            @error('stemImageUpload')
                <div class="text-danger small mt-1">
                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                </div>
            @enderror
        @endif

        {{-- ── Preview of newly staged image ──────────────── --}}
        @if ($stemImageUpload)
            <div class="mt-3 d-flex align-items-start gap-3">
                <img src="{{ $stemImageUpload->temporaryUrl() }}"
                     alt="Preview"
                     class="img-thumbnail rounded"
                     style="max-height:140px; object-fit:contain;">
                <div>
                    <p class="small fw-medium mb-1 text-dark">{{ $stemImageUpload->getClientOriginalName() }}</p>
                    <p class="small text-muted mb-2">
                        {{ number_format($stemImageUpload->getSize() / 1024, 1) }} KB
                    </p>
                    <button type="button"
                        wire:click="removeStemImageUpload"
                        class="btn btn-outline-danger btn-sm">
                        <i class="ri ri-delete-bin-line me-1"></i>Remove
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>


{{--
    partials/group-form.blade.php
    Inline create / edit form for a question set group.
--}}
<div class="card shadow-sm border-primary border-2 mb-4">
    <div class="card-header bg-primary bg-opacity-5 border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-primary">
            <i class="ri ri-stack-line me-2"></i>
            {{ $editingGroupId ? 'Edit Group' : 'New Group' }}
        </h6>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">

            {{-- Title --}}
            <div class="col-md-6">
                <label class="form-label fw-medium small">Group Title <span class="text-danger">*</span></label>
                <input type="text" wire:model="groupTitle"
                    class="form-control @error('groupTitle') is-invalid @enderror"
                    placeholder="e.g. Section A — Logical Reasoning" />
                @error('groupTitle')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Category --}}
            <div class="col-md-6">
                <label class="form-label fw-medium small">Question Category <span class="text-danger">*</span></label>
                <select wire:model="groupCategory" class="form-select @error('groupCategory') is-invalid @enderror">
                    <option value="optional">Optional</option>
                    <option value="follow-up question">Follow-up Question</option>
                    <option value="open-text">Open Text</option>
                </select>
                @error('groupCategory')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>



            {{-- Instructions --}}
            <div class="col-12">
                <label class="form-label fw-medium small">Direction to solve <span
                        class="text-muted">(optional)</span></label>
                {{-- <textarea wire:model="groupInstructions" class="form-control" rows="2"
                    placeholder="Instructions shown to candidate before this group..."></textarea> --}}

                <div wire:ignore x-data="{
                    content: @js($groupInstructions)
                }" x-init="const quill = new Quill($refs.editor, {
                    theme: 'snow',
                    placeholder: 'Instructions shown to candidate before this group...',
                    modules: {
                        toolbar: fullToolbar,
                        syntax: true,
                        formula: true,
                        table: false,
                        'table-better': {
                            language: 'en_US',
                            menus: ['column', 'row', 'merge', 'table', 'cell', 'wrap', 'copy', 'delete'],
                            toolbarTable: true,
                
                        },
                        keyboard: {
                            bindings: QuillTableBetter.keyboardBindings
                        }
                    }
                });
                
                if (content) {
                    quill.root.innerHTML = content;
                }
                quill.on('selection-change', function(range) {
                    if (range === null) {
                        $wire.set('groupInstructions', quill.root.innerHTML);
                    }
                });">
                    <div x-ref="editor" style="height:250px"></div>
                </div>
            </div>

            {{-- Toggles --}}
            <div class="col-12">
                <div class="row g-2">
                    @foreach ([['model' => 'groupRandomize', 'id' => 'grp-rand', 'label' => 'Randomize Questions', 'hint' => 'Shuffle question order in this group'], ['model' => 'groupAllowMainBack', 'id' => 'grp-mainback', 'label' => 'Allow Main Backtrack', 'hint' => 'Candidate can revisit the main stimulus (Memory Recall = false)'], ['model' => 'groupAllowBack', 'id' => 'grp-back', 'label' => 'Allow Backtrack', 'hint' => 'Candidate can go back within group (Rapid Fire = false)'], ['model' => 'groupMainTimer', 'id' => 'grp-maintimer', 'label' => 'Main Timer', 'hint' => 'Group uses a single countdown timer (Memory Recall = true)']] as $toggle)
                        <div class="col-md-6">
                            <div class="form-check form-switch d-flex align-items-start gap-2 bg-light rounded p-2">
                                <input class="form-check-input mt-1 flex-shrink-0" type="checkbox"
                                    wire:model="{{ $toggle['model'] }}" id="{{ $toggle['id'] }}" />
                                <label class="form-check-label" for="{{ $toggle['id'] }}">
                                    <span class="small fw-medium d-block">{{ $toggle['label'] }}</span>
                                    <span class="small text-muted">{{ $toggle['hint'] }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
    <div class="card-footer bg-white py-3 d-flex gap-2">
        <button type="button" wire:click="saveGroup" class="btn btn-primary" wire:loading.attr="disabled"
            wire:target="saveGroup">
            <span wire:loading wire:target="saveGroup">
                <span class="spinner-border spinner-border-sm me-1"></span>Saving…
            </span>
            <span wire:loading.remove wire:target="saveGroup">
                <i class="ri ri-save-fill me-1"></i>
                {{ $editingGroupId ? 'Update Group' : 'Create Group' }}
            </span>
        </button>
        <button type="button" wire:click="cancelGroupForm" class="btn btn-outline-secondary">
            Cancel
        </button>
    </div>
</div>

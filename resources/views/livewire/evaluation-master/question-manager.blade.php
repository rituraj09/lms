{{--
    question-manager.blade.php
    Routing shell — delegates each view to a dedicated partial.
--}}
<div>

    {{-- ── VIEW 0: Datatable List ─────────────────────────── --}}
    @if ($view === 0)
        <livewire:datatable model="App\Models\EvaluationMaster\Question" title="Questions" :new-entry="true"
            :columns="[
                ['key' => 'code', 'label' => 'Code', 'sortable' => true, 'searchable' => true],
                ['key' => 'question_type.name', 'label' => 'Question Type', 'sortable' => true, 'searchable' => true],
                ['key' => 'stem_text', 'label' => 'Question Stem', 'searchable' => true],
                ['key' => 'admin_notes', 'label' => 'Admin Notes', 'sortable' => true, 'searchable' => true],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]" :actions="[
                [
                    'label' => 'View',
                    'icon' => 'icon-base ri ri-focus-2-line',
                    'event' => 'viewQuestion',
                    'class' => 'btn-outline-success',
                ],
                [
                    'label' => 'Edit',
                    'icon' => 'icon-base ri ri-edit-line',
                    'event' => 'edit',
                    'class' => 'btn-outline-primary',
                ],
                [
                    'label' => 'Delete',
                    'icon' => 'icon-base ri ri-delete-bin-4-line',
                    'event' => 'delete',
                    'class' => 'btn-outline-danger',
                    'confirm' => true,
                ],
            ]" />
    @endif

    {{-- ── VIEW 1: Create / Edit Form ────────────────────── --}}
    @if ($view === 1)
        @include('livewire.evaluation-master.question.question-form')
    @endif

    {{-- ── VIEW 3: Preview & Publish ─────────────────────── --}}
    @if ($view === 3)
        @include('livewire.evaluation-master.question.question-preview')
    @endif

    {{-- ── Global Loading Backdrop ───────────────────────── --}}
    <div wire:loading>
        @include('utilities.backdrop')
    </div>

</div>

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/highlight/highlight.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/table.css') }}">

    <style>
        .ql-container {
            min-height: 150px;
            height: auto !important;
        }

        .ql-editor {
            min-height: 150px;
            overflow-y: visible;
        }

        .ql-editor table,
        .ql-editor td,
        .ql-editor th {
            border: 1px solid #000 !important;
            border-collapse: collapse;
        }

        .ql-editor img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .upload-dropzone {
            transition: border-color .2s, background .2s;
        }

        .upload-dropzone:hover {
            border-color: var(--bs-primary) !important;
            background: rgba(var(--bs-primary-rgb), .03);
        }
    </style>
@endpush
@push('script')
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/highlight/highlight.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/table.js') }}"></script>
    <script>
        Quill.register({
            'modules/table-better': QuillTableBetter
        }, true);

        const fullToolbar = [
            [{
                    font: []
                },
                {
                    size: []
                }
            ],
            ['bold', 'italic', 'underline', 'strike'],
            ['table-better'],
            [{
                    color: []
                },
                {
                    background: []
                }
            ],
            [{
                    script: 'super'
                },
                {
                    script: 'sub'
                }
            ],
            [{
                    header: '1'
                },
                {
                    header: '2'
                },
                'blockquote',
                'code-block'
            ],
            [{
                    list: 'ordered'
                },
                {
                    indent: '-1'
                },
                {
                    indent: '+1'
                }
            ],
            [{
                direction: 'rtl'
            }, {
                align: []
            }],
            ['link', 'video', 'formula', 'table'],

            ['clean']
        ];
    </script>
@endpush

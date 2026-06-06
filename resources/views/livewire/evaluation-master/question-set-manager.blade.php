{{-- question-set/manager.blade.php — Step 1 router --}}
<div>

    {{-- ── VIEW 0: Datatable ───────────────────────────────── --}}
    @if ($view === 0)
        <livewire:datatable model="App\Models\EvaluationMaster\QuestionSet" title="Question Sets" :new-entry="true"
            :columns="[
                ['key' => 'code', 'label' => 'Code', 'sortable' => true, 'searchable' => true],
                ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'searchable' => true],
                ['key' => 'question_set_type', 'label' => 'Type', 'sortable' => true],
                ['key' => 'total_questions', 'label' => 'Questions', 'sortable' => true],
                ['key' => 'status', 'label' => 'Status', 'sortable' => true],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]" :actions="[
                [
                    'label' => 'Builder',
                    'icon' => 'icon-base ri ri-layout-grid-line',
                    'event' => 'openBuilder',
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

    {{-- ── VIEW 1: Set Details Form ───────────────────────── --}}
    @if ($view === 1)
        @include('livewire.evaluation-master.question-set.form')
    @endif

    @if ($view === 2)
        @include('livewire.evaluation-master.question-set.question-builder')
    @endif
    <div wire:loading>@include('utilities.backdrop')</div>
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

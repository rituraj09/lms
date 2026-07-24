<div class="container-fluid py-4">

    {{-- ───────────────── Header ───────────────── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="ri ri-question-line text-primary me-2"></i>
                Question Groups
            </h4>
            <p class="text-muted small mb-0">
                Manage and organize question groups efficiently.
            </p>
        </div>

        {{-- ✅ FIXED: Route name + Permission name --}}
        @can('system.question.create')
            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary shadow-sm">
                <i class="ri ri-add-line me-1"></i>
                Add New Group
            </a>
        @endcan
    </div>


    {{-- ───────────────── Filters Card ───────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="row g-3">

                {{-- Search --}}
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="ri ri-search-line text-muted"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="Search by title, code">
                    </div>
                </div>

                {{-- Category --}}
                <div class="col-md-3">
                    <select wire:model.live="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        <option value="single">Single</option>
                        <option value="multiple">Multiple</option>
                    </select>
                </div>

                {{-- Per Page --}}
                <div class="col-md-3">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>

            </div>

        </div>
    </div>


    {{-- Loading Indicator --}}
    <div wire:loading.flex class="align-items-center text-primary mb-3">
        <div class="spinner-border spinner-border-sm me-2"></div>
        Loading...
    </div>


    {{-- ───────────────── Groups List ───────────────── --}}
    @forelse ($groups as $group)

        <div class="card border-0 shadow-sm mb-3" wire:key="group-{{ $group->id }}">

            {{-- Group Header --}}
            <div class="card-body d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-start gap-3">

                    {{-- Toggle Button --}}
                    <button wire:click="toggleGroup({{ $group->id }})" class="btn btn-sm btn-light border">
                        @if (in_array($group->id, $expandedGroups))
                            <i class="ri ri-arrow-down-s-line"></i>
                        @else
                            <i class="ri ri-arrow-right-s-line"></i>
                        @endif
                    </button>

                    {{-- Group Info --}}
                    <div>
                        <h6 class="mb-1 fw-semibold">
                            {{ $group->group_title }} {{-- ✅ uses accessor --}}
                        </h6>

                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">

                            <span
                                class="badge
            {{ $group->questions_category === 'single' ? 'bg-primary-subtle text-primary' : 'bg-purple-subtle text-purple' }}">
                                {{ ucfirst($group->questions_category) }}
                            </span>

                            @if ($group->assessment_groups_count > 0)
                                <span class="badge bg-warning-subtle text-warning">
                                    <i class="ri ri-git-repository-private-line me-1"></i>
                                    In Assessment
                                </span>
                            @endif

                        </div>

                        <small class="text-muted">
                            {{ $group->group_code }} |
                            {{ $group->questions_count }} question(s) |
                            Created by {{ $group->createdBy?->name ?? '—' }}
                        </small>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2">

                    {{-- ✅ FIXED: Route name --}}
                    <a href="{{ route('admin.questions.edit', encrypt($group->id)) }}"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ri ri-edit-box-fill me-1"></i>
                        {{ $group->assessment_groups_count === 0 ? 'Edit' : 'View' }}
                    </a>

                    @if ($group->assessment_groups_count === 0)
                        <button wire:click="confirmDelete({{ $group->id }})" class="btn btn-sm btn-outline-danger">
                            <i class="ri ri-delete-bin-line me-1"></i>
                            Delete
                        </button>
                    @endif

                </div>

            </div>


            {{-- Questions Section --}}
            @if (in_array($group->id, $expandedGroups))
                <div class="card-body border-top bg-light">

                    @forelse ($group->questions as $question)
                        <div class="d-flex justify-content-between align-items-start py-2 border-bottom"
                         wire:key="question-{{ $question->id }}">

                            <div class="text-muted small d-flex">

                                {{-- Serial Number --}}
                                <span class="fw-semibold text-dark me-2">
                                    {{ $loop->iteration }}.
                                </span>

                                <div>
                                    <i class="ri-question-line text-primary me-2"></i>
                                    {!! data_get($question->question_content, 'stem.en') !!}
                                </div>

                            </div>

                            @if ($question->assessment_questions_count > 0)
                                <span class="badge bg-warning-subtle text-warning">
                                    <i class="ri ri-git-repository-private-line me-1"></i> Used
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri ri-lock-unlock-line me-1"></i> Available
                                </span>
                            @endif

                        </div>

                    @empty
                        <div class="text-center text-muted py-3">
                            No questions in this group yet.
                        </div>
                    @endforelse

                </div>
            @endif

        </div>

    @empty

        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="ri ri-folder-2-line display-6 mb-3"></i>
                <h6>No question groups found</h6>
                <p class="small mb-0">Click "Add New Group" to create one.</p>
            </div>
        </div>

    @endforelse



    {{-- Pagination --}}
    {{-- Pagination --}}
@if ($groups->hasPages())
    <div class="d-flex align-items-center justify-content-between mt-4 flex-wrap gap-2">

        {{-- Left: Showing X to Y of Z --}}
        <p class="text-muted small mb-0">
            Showing
            <span class="fw-semibold text-dark">{{ $groups->firstItem() }}</span>
            to
            <span class="fw-semibold text-dark">{{ $groups->lastItem() }}</span>
            of
            <span class="fw-semibold text-dark">{{ $groups->total() }}</span>
            results
        </p>

        {{-- Right: Pagination Controls --}}
        <nav aria-label="Question Groups Pagination">
            <ul class="pagination pagination-sm mb-0">

                {{-- Previous Button --}}
                <li class="page-item {{ $groups->onFirstPage() ? 'disabled' : '' }}">
                    @if ($groups->onFirstPage())
                        <span class="page-link">
                            <i class="ri ri-arrow-left-s-line" style="font-size:14px;"></i>
                        </span>
                    @else
                        <button type="button" class="page-link"
                                wire:click="previousPage"
                                wire:loading.attr="disabled">
                            <i class="ri ri-arrow-left-s-line" style="font-size:14px;"></i>
                        </button>
                    @endif
                </li>

                {{-- Page Numbers --}}
                @php
                    $current = $groups->currentPage();
                    $last    = $groups->lastPage();
                @endphp

                @for ($page = 1; $page <= $last; $page++)

                    @php
                        $diff = abs($page - $current);
                    @endphp

                    @if ($page === 1 || $page === $last || $diff <= 1)
                        {{-- Show page number --}}
                        <li class="page-item {{ $page === $current ? 'active' : '' }}">
                            @if ($page === $current)
                                <span class="page-link" style="font-size:13px;">
                                    {{ $page }}
                                </span>
                            @else
                                <button type="button"
                                        class="page-link"
                                        style="font-size:13px;"
                                        wire:click="gotoPage({{ $page }})">
                                    {{ $page }}
                                </button>
                            @endif
                        </li>

                    @elseif ($diff === 2)
                        {{-- Ellipsis --}}
                        <li class="page-item disabled">
                            <span class="page-link border-0 bg-transparent px-2"
                                  style="font-size:13px;">
                                …
                            </span>
                        </li>

                    @endif

                @endfor

                {{-- Next Button --}}
                <li class="page-item {{ !$groups->hasMorePages() ? 'disabled' : '' }}">
                    @if ($groups->hasMorePages())
                        <button type="button" class="page-link"
                                wire:click="nextPage"
                                wire:loading.attr="disabled">
                            <i class="ri ri-arrow-right-s-line" style="font-size:14px;"></i>
                        </button>
                    @else
                        <span class="page-link">
                            <i class="ri ri-arrow-right-s-line" style="font-size:14px;"></i>
                        </span>
                    @endif
                </li>

            </ul>
        </nav>

    </div>

@else
    {{-- All results fit on one page --}}
    <p class="text-muted small mt-3 mb-0">
        Showing all
        <span class="fw-semibold text-dark">{{ $groups->total() }}</span>
        {{ Str::plural('result', $groups->total()) }}
    </p>
@endif


</div>
    {{-- ─── Flash Messages ──────────────────────────────────────────────── --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4"
            role="alert">
            <i class="ri ri-checkbox-circle-line fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="ri ri-error-warning-line fs-5"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- ─── Delete Confirmation Modal ──────────────────────────────────── --}}
    @if ($confirmingDelete && $deletingGroup)
        {{-- Backdrop --}}
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>

        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;" aria-modal="true"
            aria-labelledby="deleteModalTitle">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">

                    {{-- Header --}}
                    <div class="modal-header bg-danger text-white border-0 pb-3">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="deleteModalTitle">
                            <i class="ri ri-delete-bin-line fs-5"></i>
                            Delete Question Group
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cancelDelete"
                            aria-label="Close">
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body py-4">

                        {{-- Group has questions — BLOCK deletion --}}
                        @if ($deletingGroup->questions_count > 0)
                            <p class="text-muted mb-3">
                                <strong class="text-dark">
                                    {{ $deletingGroup->group_title }} {{-- ✅ --}}
                                </strong>
                                currently contains
                                <span class="badge bg-warning text-dark">
                                    {{ $deletingGroup->questions_count }}
                                    {{ Str::plural('question', $deletingGroup->questions_count) }}
                                </span>.
                            </p>

                            {{-- ── Group is empty — ALLOW deletion ── --}}
                        @else
                            <p class="text-muted mb-0">
                                You are about to permanently delete the group
                                <br>
                                <strong class="text-dark fs-6">
                                    {{ $deletingGroup->group_title }} {{-- ✅ --}}
                                </strong>.
                                <br><br>
                                <span class="text-danger fw-medium">This action cannot be undone.</span>
                            </p>
                        @endif

                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer border-0 pt-0">

                        @if ($deletingGroup->questions_count > 0)
                            {{-- Only close button when deletion is blocked --}}
                            <button type="button" class="btn btn-secondary px-4" wire:click="cancelDelete">
                                <i class="ri ri-close-line me-1"></i>
                                Close
                            </button>
                        @else
                            {{-- Cancel + Confirm when deletion is allowed --}}
                            <button type="button" class="btn btn-outline-secondary px-4" wire:click="cancelDelete">
                                <i class="ri ri-close-line me-1"></i>
                                Cancel
                            </button>

                            <button type="button" class="btn btn-danger px-4" wire:click="deleteGroup"
                                wire:loading.attr="disabled" wire:target="deleteGroup">
                                <span wire:loading.remove wire:target="deleteGroup">
                                    <i class="ri ri-delete-bin-line me-1"></i>
                                    Yes, Delete
                                </span>
                                <span wire:loading wire:target="deleteGroup">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Deleting…
                                </span>
                            </button>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    @endif
</div>

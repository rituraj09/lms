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

        <a href="{{ route('admin.question-groups.create') }}" class="btn btn-primary shadow-sm">
            <i class="ri ri-add-line me-1"></i>
            Add New Group
        </a>
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
                            placeholder="Search by title, code or note...">
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

        <div class="card border-0 shadow-sm mb-3">

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
                            {{ $group->title }}
                        </h6>

                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">

                            {{-- Category Badge --}}
                            <span
                                class="badge
                                {{ $group->questions_category === 'single' ? 'bg-primary-subtle text-primary' : 'bg-purple-subtle text-purple' }}">
                                {{ ucfirst($group->questions_category) }}
                            </span>

                            {{-- Locked Badge --}}
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

                    <a href="{{ route('admin.question-groups.edit', $group->id) }}"
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
                        <div class="d-flex justify-content-between align-items-start py-2 border-bottom">

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
    <div class="mt-4">
        {{ $groups->links('pagination::bootstrap-5') }}
    </div>

</div>

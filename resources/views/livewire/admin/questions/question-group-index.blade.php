{{-- resources/views/livewire/admin/questions/question-group-index.blade.php --}}

<div>

    {{-- ── Filters Bar ───────────────────────────────────────────────── --}}
    <div class="mb-4 flex flex-col sm:flex-row gap-3">

        {{-- Search --}}
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="ri-search-line text-base leading-none"></i>
            </span>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search by title, code or note..."
                   class="w-full rounded-lg border border-gray-300 pl-9 pr-4 py-2
                          text-sm focus:border-indigo-500 focus:ring-1
                          focus:ring-indigo-500 focus:outline-none" />
        </div>

        {{-- Category Filter --}}
        <select wire:model.live="categoryFilter"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       focus:outline-none">
            <option value="">All Categories</option>
            <option value="single">Single</option>
            <option value="multiple">Multiple</option>
        </select>

        {{-- Per Page --}}
        <select wire:model.live="perPage"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       focus:outline-none">
            <option value="10">10 / page</option>
            <option value="25">25 / page</option>
            <option value="50">50 / page</option>
        </select>
    </div>

    {{-- ── Loading Indicator ─────────────────────────────────────────── --}}
    <div wire:loading.flex
         class="mb-3 items-center gap-2 text-sm text-indigo-600">
        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>
        Loading…
    </div>

    {{-- ── Groups Table ───────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white">

        @forelse ($groups as $group)
        <div class="border-b border-gray-100 last:border-0">

            {{-- Group Row --}}
            <div class="flex items-center gap-4 px-5 py-4
                        hover:bg-gray-50 transition">

                {{-- Expand Toggle --}}
                <button wire:click="toggleGroup({{ $group->id }})"
                        class="flex-shrink-0 text-gray-400 hover:text-indigo-600
                               transition">
                    @if (in_array($group->id, $expandedGroups))
                        <i class="ri-arrow-down-s-line text-xl leading-none"></i>
                    @else
                        <i class="ri-arrow-right-s-line text-xl leading-none"></i>
                    @endif
                </button>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">

                        {{-- Title --}}
                        <span class="font-semibold text-gray-800 truncate">
                            {{ $group->title }}
                        </span>

                        {{-- Category Badge --}}
                        <span class="inline-flex items-center rounded-full px-2 py-0.5
                                     text-xs font-medium
                                     {{ $group->questions_category === 'single'
                                         ? 'bg-blue-100 text-blue-700'
                                         : 'bg-purple-100 text-purple-700' }}">
                            {{ ucfirst($group->questions_category) }}
                        </span>

                        {{-- Locked Badge --}}
                        @if ($group->assessment_groups_count > 0)
                            <span class="inline-flex items-center gap-1 rounded-full
                                         bg-amber-100 px-2 py-0.5 text-xs
                                         font-medium text-amber-700">
                                <i class="ri-lock-line text-xs leading-none"></i>
                                In Assessment
                            </span>
                        @endif
                    </div>

                    {{-- Meta --}}
                    <p class="mt-0.5 text-xs text-gray-400">
                        {{ $group->group_code }} &middot;
                        {{ $group->questions_count }} question(s) &middot;
                        Created by {{ $group->createdBy?->name ?? '—' }}
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">

                    {{-- Edit (only if NOT locked) --}}
                    @if ($group->assessment_groups_count === 0)
                        <a href="{{ route('admin.question-master.edit', $group->id) }}"
                           class="inline-flex items-center gap-1 rounded-lg
                                  border border-gray-200 px-3 py-1.5 text-xs
                                  font-medium text-gray-600 hover:border-indigo-400
                                  hover:text-indigo-600 transition">
                            <i class="ri-pencil-line text-sm leading-none"></i>
                            Edit
                        </a>
                    @else
                        {{-- View / Add Question even if locked --}}
                        <a href="{{ route('admin.question-master.edit', $group->id) }}"
                           class="inline-flex items-center gap-1 rounded-lg
                                  border border-gray-200 px-3 py-1.5 text-xs
                                  font-medium text-gray-600 hover:border-indigo-400
                                  hover:text-indigo-600 transition">
                            <i class="ri-eye-line text-sm leading-none"></i>
                            View / Add Q
                        </a>
                    @endif

                    {{-- Delete (only if NOT locked) --}}
                    @if ($group->assessment_groups_count === 0)
                        <button wire:click="confirmDelete({{ $group->id }})"
                                class="inline-flex items-center gap-1 rounded-lg
                                       border border-red-200 px-3 py-1.5 text-xs
                                       font-medium text-red-500
                                       hover:border-red-400 hover:text-red-700
                                       transition">
                            <i class="ri-delete-bin-line text-sm leading-none"></i>
                            Delete
                        </button>
                    @endif
                </div>
            </div>

            {{-- ── Questions Accordion ──────────────────────────────── --}}
            @if (in_array($group->id, $expandedGroups))
            <div class="bg-gray-50 border-t border-gray-100 px-6 pb-4 pt-3">

                @forelse ($group->questions as $question)
                <div class="flex items-center gap-3 py-2.5 border-b
                             border-gray-100 last:border-0">

                    {{-- Question Icon --}}
                    <div class="flex-shrink-0 flex h-7 w-7 items-center
                                 justify-center rounded-full bg-indigo-50">
                        <i class="ri-question-line text-sm text-indigo-400 leading-none"></i>
                    </div>

                    {{-- Question Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">
                            {{ $question->question_content['question_text'] ?? '(No text)' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $question->question_code }} &middot;
                            {{ ucfirst($question->answer_category) }}
                            @if ($question->assessment_questions_count > 0)
                                &middot;
                                <span class="inline-flex items-center gap-0.5
                                              text-amber-600 font-medium">
                                    <i class="ri-lock-line text-xs leading-none"></i>
                                    Used in assessment
                                </span>
                            @endif
                        </p>
                    </div>

                    {{-- Edit Question --}}
                    @if ($question->assessment_questions_count === 0)
                        <a href="{{ route('admin.question-master.edit', $group->id) }}#q{{ $question->id }}"
                           class="inline-flex items-center gap-1 text-xs
                                  font-medium text-indigo-600 hover:underline
                                  flex-shrink-0">
                            <i class="ri-pencil-line text-xs leading-none"></i>
                            Edit
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs
                                     text-gray-400 flex-shrink-0">
                            <i class="ri-lock-line text-xs leading-none"></i>
                            Locked
                        </span>
                    @endif
                </div>
                @empty
                    <div class="flex items-center gap-2 py-3 text-gray-400">
                        <i class="ri-file-list-3-line text-base leading-none"></i>
                        <p class="text-sm">No questions in this group yet.</p>
                    </div>
                @endforelse
            </div>
            @endif

        </div>
        @empty
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16
                         text-center text-gray-400">
                <div class="flex h-16 w-16 items-center justify-center
                             rounded-full bg-gray-100 mb-4">
                    <i class="ri-folder-open-line text-3xl text-gray-300 leading-none"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">
                    No question groups found.
                </p>
                <p class="text-xs mt-1 text-gray-400">
                    Try adjusting your search or create a new group.
                </p>
            </div>
        @endforelse
    </div>

    {{-- ── Pagination ─────────────────────────────────────────────────── --}}
    <div class="mt-4">
        {{ $groups->links() }}
    </div>

    {{-- ── Delete Confirmation Modal ──────────────────────────────────── --}}
    @if ($confirmingDelete)
    <div class="fixed inset-0 z-50 flex items-center justify-center
                bg-black/40 backdrop-blur-sm">
        <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

            {{-- Modal Header --}}
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex h-10 w-10 items-center
                             justify-center rounded-full bg-red-100">
                    <i class="ri-error-warning-line text-xl text-red-600 leading-none"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">
                        Delete Question Group
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Are you sure? This will permanently delete the group
                        and all its questions. This action cannot be undone.
                    </p>
                </div>
            </div>

            {{-- Modal Actions --}}
            <div class="mt-6 flex justify-end gap-3">

                {{-- Cancel --}}
                <button wire:click="cancelDelete"
                        class="inline-flex items-center gap-1.5 rounded-lg
                               border border-gray-200 px-4 py-2 text-sm
                               font-medium text-gray-600 hover:bg-gray-50
                               transition">
                    <i class="ri-close-line text-base leading-none"></i>
                    Cancel
                </button>

                {{-- Confirm Delete --}}
                <button wire:click="deleteGroup"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 rounded-lg
                               bg-red-600 px-4 py-2 text-sm font-medium
                               text-white hover:bg-red-700 disabled:opacity-50
                               transition">

                    {{-- Normal State --}}
                    <span wire:loading.remove wire:target="deleteGroup"
                          class="inline-flex items-center gap-1.5">
                        <i class="ri-delete-bin-line text-base leading-none"></i>
                        Yes, Delete
                    </span>

                    {{-- Loading State --}}
                    <span wire:loading wire:target="deleteGroup"
                          class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 animate-spin" fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        Deleting…
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

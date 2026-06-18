{{-- resources/views/livewire/admin/questions/question-group-form.blade.php --}}

<div class="space-y-6">

    {{-- ── Locked Banner ──────────────────────────────────────────────── --}}
    @if ($isLocked)
    <div class="flex items-start gap-3 rounded-xl border border-amber-200
                bg-amber-50 px-4 py-3">
        <i class="ri-lock-line text-lg text-amber-500 flex-shrink-0 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-amber-800">
                This group is linked to an assessment.
            </p>
            <p class="text-xs text-amber-600 mt-0.5">
                Group details (title, category, content) cannot be changed.
                You can still add new questions to the group.
            </p>
        </div>
    </div>
    @endif

    {{-- ── Flash Message ──────────────────────────────────────────────── --}}
    @if (session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3
                text-sm text-green-700 flex items-center gap-2">
        <i class="ri-checkbox-circle-line text-green-600 text-base"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Group Details Card ─────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="border-b border-gray-100 px-6 py-4 flex items-center gap-2">
            <i class="ri-folder-3-line text-gray-500"></i>
            <h2 class="text-base font-semibold text-gray-800">
                Group Details
            </h2>
        </div>

        <div class="px-6 py-5 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Group Code --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Group Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="group_code"
                           {{ $isLocked ? 'disabled' : '' }}
                           class="w-full rounded-lg border border-gray-300
                                  px-3 py-2 text-sm focus:border-indigo-500
                                  focus:ring-1 focus:ring-indigo-500
                                  focus:outline-none disabled:bg-gray-100
                                  disabled:cursor-not-allowed" />
                    @error('group_code')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Questions Category <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="questions_category"
                            {{ $isLocked ? 'disabled' : '' }}
                            class="w-full rounded-lg border border-gray-300
                                   px-3 py-2 text-sm focus:border-indigo-500
                                   focus:ring-1 focus:ring-indigo-500
                                   focus:outline-none disabled:bg-gray-100
                                   disabled:cursor-not-allowed">
                        <option value="single">Single Answer</option>
                        <option value="multiple">Multiple Answer</option>
                    </select>
                    @error('questions_category')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Title <span class="text-red-500">*</span>
                </label>
                <textarea wire:model="title"
                          rows="2"
                          {{ $isLocked ? 'disabled' : '' }}
                          class="w-full rounded-lg border border-gray-300
                                 px-3 py-2 text-sm focus:border-indigo-500
                                 focus:ring-1 focus:ring-indigo-500
                                 focus:outline-none disabled:bg-gray-100
                                 disabled:cursor-not-allowed resize-none"></textarea>
                @error('title')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Admin Note --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Admin Note
                </label>
                <input type="text"
                       wire:model="admin_note"
                       class="w-full rounded-lg border border-gray-300
                              px-3 py-2 text-sm focus:border-indigo-500
                              focus:ring-1 focus:ring-indigo-500
                              focus:outline-none" />
                @error('admin_note')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Save Button --}}
        @if (! $isLocked)
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end">
            <button wire:click="saveGroup"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg
                           bg-indigo-600 px-5 py-2 text-sm font-medium
                           text-white hover:bg-indigo-700 disabled:opacity-50
                           transition">

                <span wire:loading.remove wire:target="saveGroup"
                      class="inline-flex items-center gap-2">
                    <i class="ri-check-line text-base"></i>
                    {{ $groupId ? 'Update Group' : 'Save Group' }}
                </span>

                <span wire:loading wire:target="saveGroup"
                      class="inline-flex items-center gap-2">
                    <i class="ri-loader-4-line animate-spin text-base"></i>
                    Saving…
                </span>
            </button>
        </div>
        @endif
    </div>

    {{-- ── Questions Section ──────────────────────────────────────────── --}}
    @if ($groupId)
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <i class="ri-questionnaire-line text-gray-500"></i>
                Questions
                <span class="ml-1 rounded-full bg-indigo-100 px-2 py-0.5
                              text-xs font-semibold text-indigo-700">
                    {{ $questions->count() }}
                </span>
            </h2>

            {{-- Add Question --}}
            @if (! $showQuestionForm)
            <button wire:click="addNewQuestion"
                    class="inline-flex items-center gap-1.5 rounded-lg
                           bg-indigo-600 px-3 py-1.5 text-xs font-medium
                           text-white hover:bg-indigo-700 transition">
                <i class="ri-add-line text-sm"></i>
                Add Question
            </button>
            @endif
        </div>

        {{-- Inline Question Form --}}
        @if ($showQuestionForm)
        <div class="border-b border-indigo-100 bg-indigo-50/40 px-6 py-5">
            @livewire('admin.questions.question-form',
                [
                    'questionGroupId' => $groupId,
                    'questionId'      => $editingQuestionId,
                ],
                key('question-form-' . ($editingQuestionId ?? 'new'))
            )
        </div>
        @endif

        {{-- Questions List --}}
        <div class="divide-y divide-gray-100">
            @forelse ($questions as $question)
            <div class="flex items-start gap-4 px-6 py-4
                        hover:bg-gray-50 transition"
                 id="q{{ $question->id }}">

                {{-- Icon --}}
                <div class="flex-shrink-0 mt-0.5 flex h-8 w-8 items-center
                             justify-center rounded-full bg-indigo-50">
                    <i class="ri-question-line text-indigo-500 text-base"></i>
                </div>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">
                        {{ $question->question_content['question_text'] ?? '(No question text)' }}
                    </p>
                    <div class="mt-1 flex flex-wrap gap-2 text-xs text-gray-400">
                        <span>{{ $question->question_code }}</span>
                        <span>&middot;</span>
                        <span>{{ ucfirst($question->answer_category) }}</span>

                        @if ($question->difficultyLevel)
                            <span>&middot;</span>
                            <span>{{ $question->difficultyLevel->name }}</span>
                        @endif

                        @if ($question->isUsedInAssessment())
                            <span class="inline-flex items-center gap-1
                                          text-amber-600 font-medium">
                                <i class="ri-lock-line text-xs"></i>
                                In Assessment
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex-shrink-0 flex items-center gap-2">
                    @if (! $question->isUsedInAssessment())
                        <button wire:click="editQuestion({{ $question->id }})"
                                class="inline-flex items-center gap-1 text-xs
                                       font-medium text-indigo-600 hover:underline">
                            <i class="ri-pencil-line text-xs"></i>
                            Edit
                        </button>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                            <i class="ri-lock-line text-xs"></i>
                            Locked
                        </span>
                    @endif
                </div>
            </div>
            @empty
                <div class="flex flex-col items-center justify-center py-10
                             text-gray-400">
                    <i class="ri-file-list-3-line text-4xl mb-2 opacity-40"></i>
                    <p class="text-sm">No questions yet. Add your first question.</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- ── Back Button ────────────────────────────────────────────────── --}}
    <div>
        <a href="{{ route('admin.questions.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500
                  hover:text-gray-700 transition">
            <i class="ri-arrow-left-line text-base"></i>
            Back to Question Groups
        </a>
    </div>

</div>

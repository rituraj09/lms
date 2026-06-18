{{-- resources/views/livewire/admin/questions/question-form.blade.php --}}

<div class="space-y-5">

    {{-- Locked Banner --}}
    @if ($isLocked)
    <div class="flex items-center gap-2 rounded-lg border border-amber-200
                bg-amber-50 px-4 py-2.5 text-sm text-amber-700">
        <i class="ri-lock-line text-base"></i>
        This question is used in an assessment and cannot be edited.
    </div>
    @endif

    {{-- General Errors --}}
    @if ($errors->has('locked'))
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5
                text-sm text-red-700 flex items-center gap-2">
        <i class="ri-error-warning-line text-base"></i>
        {{ $errors->first('locked') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Question Code --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">
                Question Code <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   wire:model="question_code"
                   {{ $isLocked ? 'disabled' : '' }}
                   class="w-full rounded-lg border border-gray-300 px-3 py-2
                          text-sm focus:border-indigo-500 focus:ring-1
                          focus:ring-indigo-500 focus:outline-none
                          disabled:bg-gray-100 disabled:cursor-not-allowed" />
            @error('question_code')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Answer Category --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">
                Answer Category <span class="text-red-500">*</span>
            </label>
            <select wire:model.live="answer_category"
                    {{ $isLocked ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-gray-300 px-3 py-2
                           text-sm focus:border-indigo-500 focus:ring-1
                           focus:ring-indigo-500 focus:outline-none
                           disabled:bg-gray-100 disabled:cursor-not-allowed">
                <option value="optional">Optional (Multiple Choice)</option>
                <option value="open_text">Open Text</option>
            </select>
            @error('answer_category')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Primary Skill --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">
                Primary Skill <span class="text-red-500">*</span>
            </label>
            <select wire:model="primary_skill_id"
                    {{ $isLocked ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-gray-300 px-3 py-2
                           text-sm focus:border-indigo-500 focus:ring-1
                           focus:ring-indigo-500 focus:outline-none
                           disabled:bg-gray-100 disabled:cursor-not-allowed">
                <option value="">Select Primary Skill</option>
                @foreach ($primarySkills as $skill)
                    <option value="{{ $skill['id'] }}">{{ $skill['name'] }}</option>
                @endforeach
            </select>
            @error('primary_skill_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Sub Skill --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">
                Sub Skill <span class="text-red-500">*</span>
            </label>
            <select wire:model="sub_skill_id"
                    {{ $isLocked ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-gray-300 px-3 py-2
                           text-sm focus:border-indigo-500 focus:ring-1
                           focus:ring-indigo-500 focus:outline-none
                           disabled:bg-gray-100 disabled:cursor-not-allowed">
                <option value="">Select Sub Skill</option>
                @foreach ($subSkills as $skill)
                    <option value="{{ $skill['id'] }}">{{ $skill['name'] }}</option>
                @endforeach
            </select>
            @error('sub_skill_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Difficulty Level --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">
                Difficulty Level <span class="text-red-500">*</span>
            </label>
            <select wire:model="difficulty_level_id"
                    {{ $isLocked ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-gray-300 px-3 py-2
                           text-sm focus:border-indigo-500 focus:ring-1
                           focus:ring-indigo-500 focus:outline-none
                           disabled:bg-gray-100 disabled:cursor-not-allowed">
                <option value="">Select Difficulty</option>
                @foreach ($difficultyLevels as $level)
                    <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                @endforeach
            </select>
            @error('difficulty_level_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Age Group --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">
                Age Group <span class="text-red-500">*</span>
            </label>
            <select wire:model="age_group_id"
                    {{ $isLocked ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-gray-300 px-3 py-2
                           text-sm focus:border-indigo-500 focus:ring-1
                           focus:ring-indigo-500 focus:outline-none
                           disabled:bg-gray-100 disabled:cursor-not-allowed">
                <option value="">Select Age Group</option>
                @foreach ($ageGroups as $age)
                    <option value="{{ $age['id'] }}">{{ $age['name'] }}</option>
                @endforeach
            </select>
            @error('age_group_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Question Text --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">
            Question Text <span class="text-red-500">*</span>
        </label>
        <textarea wire:model="question_content.question_text"
                  rows="3"
                  {{ $isLocked ? 'disabled' : '' }}
                  class="w-full rounded-lg border border-gray-300 px-3 py-2
                         text-sm focus:border-indigo-500 focus:ring-1
                         focus:ring-indigo-500 focus:outline-none resize-none
                         disabled:bg-gray-100 disabled:cursor-not-allowed"></textarea>
        @error('question_content.question_text')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Options Builder --}}
    @if ($answer_category === 'optional')
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">

        <div class="flex items-center justify-between">
            <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                Answer Options
            </h4>
            @if (! $isLocked)
            <button wire:click="addOption"
                    type="button"
                    class="inline-flex items-center gap-1 text-xs font-medium
                           text-indigo-600 hover:text-indigo-800 transition">
                <i class="ri-add-circle-line text-sm"></i>
                Add Option
            </button>
            @endif
        </div>

        @forelse ($question_content['options'] as $index => $option)
        <div class="flex items-center gap-3 bg-white rounded-lg border
                    border-gray-200 px-3 py-2">

            {{-- Correct toggle --}}
            <button wire:click="setCorrectOption({{ $index }})"
                    type="button"
                    {{ $isLocked ? 'disabled' : '' }}
                    class="flex-shrink-0 transition">
                @if ($option['is_correct'])
                    <i class="ri-checkbox-circle-fill text-green-500 text-lg"></i>
                @else
                    <i class="ri-checkbox-blank-circle-line text-gray-300 hover:text-green-400 text-lg"></i>
                @endif
            </button>

            {{-- Option Text --}}
            <input type="text"
                   wire:model="question_content.options.{{ $index }}.text"
                   placeholder="Option {{ $index + 1 }}"
                   {{ $isLocked ? 'disabled' : '' }}
                   class="flex-1 text-sm border-0 bg-transparent
                          focus:outline-none focus:ring-0
                          disabled:cursor-not-allowed" />

            {{-- Remove --}}
            @if (! $isLocked)
            <button wire:click="removeOption({{ $index }})"
                    type="button"
                    class="flex-shrink-0 text-gray-300 hover:text-red-400 transition">
                <i class="ri-close-line text-base"></i>
            </button>
            @endif
        </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-2">
                No options added yet. Click "Add Option" to begin.
            </p>
        @endforelse
    </div>
    @endif

    {{-- Explanation --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">
            Explanation
        </label>
        <textarea wire:model="explaination"
                  rows="2"
                  {{ $isLocked ? 'disabled' : '' }}
                  class="w-full rounded-lg border border-gray-300 px-3 py-2
                         text-sm focus:border-indigo-500 focus:ring-1
                         focus:ring-indigo-500 focus:outline-none resize-none
                         disabled:bg-gray-100 disabled:cursor-not-allowed"></textarea>
    </div>

    {{-- Admin Notes --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">
            Admin Notes
        </label>
        <input type="text"
               wire:model="admin_notes"
               {{ $isLocked ? 'disabled' : '' }}
               class="w-full rounded-lg border border-gray-300 px-3 py-2
                      text-sm focus:border-indigo-500 focus:ring-1
                      focus:ring-indigo-500 focus:outline-none
                      disabled:bg-gray-100 disabled:cursor-not-allowed" />
    </div>

    {{-- Action Buttons --}}
    @if (! $isLocked)
    <div class="flex items-center justify-between pt-2 border-t border-gray-100">

        {{-- Cancel --}}
        <button wire:click="$dispatch('closeQuestionForm')"
                type="button"
                class="text-sm text-gray-500 hover:text-gray-700 transition">
            Cancel
        </button>

        <div class="flex items-center gap-2">

            {{-- Save & Create New --}}
            <button wire:click="saveAndCreateNew"
                    wire:loading.attr="disabled"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg border
                           border-indigo-300 px-4 py-2 text-sm font-medium
                           text-indigo-700 hover:bg-indigo-50
                           disabled:opacity-50 transition">

                <span wire:loading.remove wire:target="saveAndCreateNew">
                    <i class="ri-save-line text-sm"></i>
                    Save & Add Another
                </span>

                <span wire:loading wire:target="saveAndCreateNew">
                    <i class="ri-loader-4-line animate-spin text-sm"></i>
                    Saving…
                </span>
            </button>

            {{-- Save --}}
            <button wire:click="saveQuestion"
                    wire:loading.attr="disabled"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg
                           bg-indigo-600 px-4 py-2 text-sm font-medium
                           text-white hover:bg-indigo-700
                           disabled:opacity-50 transition">

                <span wire:loading.remove wire:target="saveQuestion">
                    <i class="ri-check-line text-sm"></i>
                    {{ $questionId ? 'Update Question' : 'Save Question' }}
                </span>

                <span wire:loading wire:target="saveQuestion">
                    <i class="ri-loader-4-line animate-spin text-sm"></i>
                    Saving…
                </span>
            </button>
        </div>
    </div>
    @endif

</div>

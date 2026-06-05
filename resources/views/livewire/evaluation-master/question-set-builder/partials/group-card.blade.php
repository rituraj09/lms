{{--
    partials/group-card.blade.php
    One group accordion card.
    Variables: $group (array), $groupIdx (int)
--}}
@php
    $assigned = \App\Models\EvaluationMaster\QuestionQuestionSetGroup::with('question.question_type')
        ->where('question_set_group_id', $group['id'])
        ->where('status', 'active')
        ->orderBy('order')
        ->get();
@endphp

<div class="card shadow-sm border-0 mb-3 group-card" wire:key="group-{{ $group['id'] }}">

    {{-- Group header --}}
    <div class="card-header bg-white border-bottom py-3"
        x-data="{ open: true }">

        <div class="d-flex align-items-center gap-3 flex-wrap">

            {{-- Collapse toggle --}}
            <button type="button" @click="open = !open"
                class="btn btn-sm btn-light rounded-circle p-0 flex-shrink-0"
                style="width:32px;height:32px;">
                <i class="ri" :class="open ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
            </button>

            <span class="badge bg-success fw-bold" style="width:28px;height:28px;line-height:16px;">
                {{ $groupIdx + 1 }}
            </span>

            <div class="flex-grow-1">
                <span class="fw-semibold text-dark">{{ $group['title'] }}</span>
                <div class="d-flex gap-2 mt-1 flex-wrap">
                    <span class="badge bg-secondary-subtle text-secondary small">{{ $group['question_category'] }}</span>
                    @if ($group['randomize_questions'])
                        <span class="badge bg-info-subtle text-info small"><i class="ri ri-shuffle-line me-1"></i>Random</span>
                    @endif
                    @if ($group['main_timer'])
                        <span class="badge bg-warning-subtle text-warning small"><i class="ri ri-timer-line me-1"></i>Timer</span>
                    @endif
                    @if (! $group['allow_backtrack'])
                        <span class="badge bg-danger-subtle text-danger small">No Backtrack</span>
                    @endif
                </div>
            </div>

            <span class="badge bg-primary-subtle text-primary px-2 py-1">
                {{ $group['question_count'] }} question{{ $group['question_count'] !== 1 ? 's' : '' }}
            </span>

            {{-- Group actions --}}
            <div class="d-flex gap-1">
                <button type="button" wire:click="openPicker({{ $group['id'] }})"
                    class="btn btn-sm btn-outline-success"
                    title="Add questions to this group">
                    <i class="ri ri-add-large-line me-1"></i>Add Questions
                </button>
                <button type="button" wire:click="openEditGroup({{ $group['id'] }})"
                    class="btn btn-sm btn-outline-primary" title="Edit group">
                    <i class="ri ri-edit-line"></i>
                </button>
                <button type="button"
                    wire:click="deleteGroup({{ $group['id'] }})"
                    wire:confirm="Delete this group and remove all its questions?"
                    class="btn btn-sm btn-outline-danger" title="Delete group">
                    <i class="ri ri-delete-bin-fill"></i>
                </button>
            </div>
        </div>

        {{-- Question rows --}}
        <div x-show="open" x-collapse class="mt-3">

            @if ($assigned->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="ri ri-question-line fs-3 d-block mb-1"></i>
                    <span class="small">No questions yet. Click <strong>Add Questions</strong> to pick from the library.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th style="width:190px;">Max Score</th>
                                <th style="width:190px;">Override</th>
                                <th style="width:190px;">Timer (s)</th>
                                <th style="width:190px;">Neg. Mark</th>
                                <th style="width:180px;">Status</th>
                                <th style="width:90px;">Order</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assigned as $pivot)
                                <tr class="question-row" wire:key="qrow-{{ $group['id'] }}-{{ $pivot->question_id }}">
                                    <td class="text-muted small">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="font-monospace small fw-medium">{{ $pivot->question->code }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border small">
                                            {{ $pivot->question->question_type->slug ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ number_format($pivot->question->max_score, 2) }}</td>
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm"
                                            value="{{ $pivot->score_override }}"
                                            @change="$wire.updateQuestionSetting({{ $group['id'] }}, {{ $pivot->question_id }}, 'score_override', $event.target.value)"
                                            step="0.01" min="0" placeholder="—" />
                                    </td>
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm"
                                            value="{{ $pivot->timer }}"
                                            @change="$wire.updateQuestionSetting({{ $group['id'] }}, {{ $pivot->question_id }}, 'timer', $event.target.value)"
                                            min="1" placeholder="—" />
                                    </td>
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm"
                                            value="{{ $pivot->negative_mark }}"
                                            @change="$wire.updateQuestionSetting({{ $group['id'] }}, {{ $pivot->question_id }}, 'negative_mark', $event.target.value)"
                                            step="0.01" min="0" />
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm"
                                            @change="$wire.updateQuestionSetting({{ $group['id'] }}, {{ $pivot->question_id }}, 'status', $event.target.value)">
                                            <option value="active"   {{ $pivot->status === 'active'   ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $pivot->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button"
                                                wire:click="moveQuestionUp({{ $group['id'] }}, {{ $pivot->question_id }})"
                                                class="btn btn-xs btn-light border px-1 py-0" title="Move up">
                                                <i class="ri ri-arrow-up-s-line"></i>
                                            </button>
                                            <button type="button"
                                                wire:click="moveQuestionDown({{ $group['id'] }}, {{ $pivot->question_id }})"
                                                class="btn btn-xs btn-light border px-1 py-0" title="Move down">
                                                <i class="ri ri-arrow-down-s-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button"
                                            wire:click="removeQuestionFromGroup({{ $group['id'] }}, {{ $pivot->question_id }})"
                                            wire:confirm="Remove this question from the group?"
                                            class="btn btn-xs btn-outline-danger px-1 py-0" title="Remove">
                                            <i class="ri ri-close-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>

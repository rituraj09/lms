<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $assessment->title }}</h2>
            <span class="badge bg-info">Preview Mode</span>
            @if($assessment->shuffle_sections)
                <span class="badge bg-warning ms-2"><i class="ri ri-shuffle-line"></i> Sections Shuffled</span>
            @endif
        </div>

        <!-- Language Switcher -->
        @if(count($availableLanguages) > 1)
            <div class="btn-group" role="group">
                @foreach($availableLanguages as $lang)
                    <button type="button"
                            class="btn btn-sm @if($language === $lang) btn-primary @else btn-outline-primary @endif"
                            wire:click="changeLanguage('{{ $lang }}')">
                        {{ strtoupper($lang) }}
                    </button>
                @endforeach
            </div>
        @endif

        <a href="{{ route('admin.assessment-masters.list') }}" class="btn btn-outline-secondary">
            <i class="ri ri-close-line"></i> Close
        </a>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            @if($currentSection)
                <!-- Section Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    {{ 'Section ' . ($currentSectionIndex + 1) . ' of ' . count($sections) }}
                                </h5>
                            </div>
                            <div>
                                @if($currentSection['shuffle_question'])
                                    <span class="badge bg-warning">
                                        <i class="ri ri-shuffle-line"></i> Questions Shuffled
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Instructions -->
                        @if($currentSection['instructions'])
                            <div class="alert alert-info mb-4">
                                <h6 class="alert-heading">
                                    <i class="ri ri-information-line"></i> Instructions
                                </h6>
                                <p class="mb-0">{{ $currentSection['instructions'] }}</p>
                                @if($currentSection['group_timer'] > 0)
                                    <hr class="my-2">
                                    <small class="text-muted">
                                        <i class="ri ri-time-line"></i> Section Time: {{ $currentSection['group_timer'] }} seconds
                                    </small>
                                @endif
                            </div>
                        @endif

                        <!-- Question Group Content (if multiple category) -->
                        @if($currentSection['question_group'] &&
                            $currentSection['question_group']->questions_category === 'multiple' &&
                            $currentSection['allow_back_to_group_question'])

                            <div class="alert alert-secondary mb-4">
                                <h6 class="alert-heading">
                                    <i class="ri ri-book-line"></i> {{ $this->getText($currentSection['question_group']->group_content, 'title') }}
                                </h6>
                                <div class="text-muted">
                                    {!! $this->getText($currentSection['question_group']->group_content, 'content') !!}
                                </div>
                                @if($currentSection['group_timer'] > 0)
                                    <hr class="my-2">
                                    <small class="text-muted">
                                        <i class="ri ri-time-line"></i> Group Time: {{ $currentSection['group_timer'] }} seconds
                                    </small>
                                @endif
                            </div>
                        @endif

                        <!-- Current Question -->
                        @if($currentQuestion)
                            <div class="question-container mb-4">
                                <!-- Question Number & Timer -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="mb-0">
                                        Question {{ $currentQuestionIndex + 1 }} of {{ count($currentSection['questions']) }}
                                    </h6>
                                    <div>
                                        @if($currentQuestion['question_timer'] > 0)
                                            <small class="badge bg-info">
                                                <i class="ri ri-time-line"></i> {{ $currentQuestion['question_timer'] }}s
                                            </small>
                                        @endif
                                        @if($currentQuestion['negative_mark'] > 0)
                                            <small class="badge bg-danger ms-2">
                                                -{{ $currentQuestion['negative_mark'] }} marks
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Question Stem -->
                                <div class="question-stem mb-4 p-3 bg-light rounded">
                                    <p class="mb-0">
                                        {!! $this->getText($currentQuestion['question_content'], 'stem') !!}
                                    </p>
                                    @if($currentQuestion['question_content']['image'] ?? null)
                                        <div class="mt-3">
                                            <img src="{{ $currentQuestion['question_content']['image'] }}"
                                                 alt="Question image"
                                                 class="img-fluid rounded"
                                                 style="max-width: 400px;">
                                        </div>
                                    @endif
                                </div>

                                <!-- Options -->
                                <div class="options mb-4">
                                    @php
                                        $options = $currentQuestion['question_content']['options'] ?? [];
                                    @endphp

                                    @foreach($options as $index => $option)
                                        <div class="form-check mb-3 p-3 border rounded
                                                    @if($option['is_correct']) bg-light-success @endif">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   id="option_{{ $index }}"
                                                   name="question_option"
                                                   disabled>
                                            <label class="form-check-label w-100 cursor-pointer"
                                                   for="option_{{ $index }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        @if($option['option_type'] === 'text')
                                                            {!! $option['text'][$language] ?? $option['text']['en'] ?? '' !!}
                                                        @else
                                                            <img src="{{ $option['image_path'] }}"
                                                                 alt="Option"
                                                                 class="img-fluid rounded"
                                                                 style="max-width: 200px;">
                                                        @endif
                                                    </div>
                                                    @if($option['is_correct'])
                                                        <span class="badge bg-success ms-2">
                                                            <i class="ri ri-check-line"></i> Correct
                                                        </span>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Explanation -->
                                @if($this->getText($currentQuestion['question_content'], 'explanation'))
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">
                                            <i class="ri ri-lightbulb-line"></i> Explanation
                                        </h6>
                                        {!! $this->getText($currentQuestion['question_content'], 'explanation') !!}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-outline-secondary"
                                    wire:click="previousQuestion"
                                    @if($currentQuestionIndex === 0 && $currentSectionIndex === 0) disabled @endif
                                    @if(!$currentSection['allow_back_to_previous_question']) disabled @endif>
                                <i class="ri ri-arrow-left-line"></i> Previous
                            </button>

                            <div>
                                <button class="btn btn-outline-primary"
                                        wire:click="toggleAnswered">
                                    @if($this->isQuestionAnswered($currentSectionIndex, $currentQuestionIndex))
                                        <i class="ri-checkbox-circle-fill"></i> Mark as Unanswered
                                    @else
                                        <i class="ri-checkbox-blank-circle-line"></i> Mark as Answered
                                    @endif
                                </button>
                            </div>

                            <button class="btn btn-primary"
                                    wire:click="nextQuestion"
                                    @if($currentQuestionIndex === count($currentSection['questions']) - 1 &&
                                        $currentSectionIndex === count($sections) - 1) disabled @endif>
                                Next <i class="ri ri-arrow-right-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Sidebar - Question Navigator -->
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="ri ri-list-check-3"></i> Questions
                    </h6>
                </div>

                <div class="card-body p-0" style="max-height: 70vh; overflow-y: auto;">
                    @foreach($sections as $sectionIdx => $section)
                        <div class="px-3 py-2 border-bottom">
                            <small class="text-muted fw-bold d-block mb-2">
                                Section {{ $sectionIdx + 1 }}
                            </small>

                            <div class="questions-grid">
                                @foreach($section['questions'] as $questionIdx => $question)
                                    @php
                                        $isActive = $sectionIdx === $currentSectionIndex && $questionIdx === $currentQuestionIndex;
                                        $isAnswered = $this->isQuestionAnswered($sectionIdx, $questionIdx);
                                    @endphp

                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary rounded-circle p-0 m-1"
                                            style="width: 35px; height: 35px;"
                                            wire:click="goToQuestion({{ $sectionIdx }}, {{ $questionIdx }})"
                                    @class([
                                        'bg-primary text-white' => $isActive,
                                        'bg-success text-white' => !$isActive && $isAnswered,
                                        'bg-light' => !$isActive && !$isAnswered,
                                    ])"
                                    title="Question {{ $questionIdx + 1 }}">
                                    {{ $questionIdx + 1 }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary Stats -->
                <div class="card-footer bg-light">
                    <small class="d-block mb-2">
                        <span class="badge bg-success">{{ count($answeredQuestions) }} Answered</span>
                        <span class="badge bg-light text-dark">
                            {{ array_sum(array_map(fn($s) => count($s['questions']), $sections)) - count($answeredQuestions) }}
                            Unanswered
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-success {
        background-color: #d4edda !important;
    }

    .questions-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .form-check-label {
        cursor: pointer;
    }

    .question-container {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .sticky-top {
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 991px) {
        .sticky-top {
            position: relative;
            top: 0 !important;
            margin-top: 2rem;
        }
    }
</style>

{{--
    question/preview/quick-info.blade.php
    Right-column quick reference card inside the preview view.
--}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-information-line text-info me-2"></i>Quick Information
        </h6>
    </div>

    <div class="card-body p-4">

        <div class="mb-2">
            <small class="text-muted d-block">Question Code</small>
            <strong>{{ $code }}</strong>
        </div>

        @if ($questionTypeId)
            <div class="mb-2">
                <small class="text-muted d-block">Question Type</small>
                <strong>
                    {{ collect($questionTypes)->firstWhere('id', $questionTypeId)['name'] ?? '—' }}
                </strong>
            </div>
        @endif

        @if ($difficultyLevelId)
            <div class="mb-2">
                <small class="text-muted d-block">Difficulty Level</small>
                <strong>
                    {{ collect($difficultyLevels)->firstWhere('id', $difficultyLevelId)['name'] ?? '—' }}
                </strong>
            </div>
        @endif

        @if ($ageGroupId)
            <div>
                <small class="text-muted d-block">Age Group</small>
                <strong>
                    {{ collect($ageGroups)->firstWhere('id', $ageGroupId)['name'] ?? '—' }}
                </strong>
            </div>
        @endif

    </div>
</div>

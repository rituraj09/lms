{{--
    partials/form/basic-info.blade.php
    Code, Question Type, Primary Skill, Sub Skill,
    Difficulty Level, Age Group fields.
--}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-information-line text-primary me-2"></i>Basic Information
        </h6>
    </div>

    <div class="card-body p-4">
        <div class="row g-3">

            {{-- Question Code --}}
            <div class="col-md-4">
                <label class="form-label fw-medium small">
                    Question Code <span class="text-danger">*</span>
                </label>
               <div class="form-control bg-light">
                    {{ $code }}
                </div>

                <input type="hidden" wire:model="code">
            </div>

            {{-- Question Type --}}
            <div class="col-md-4">
                <label class="form-label fw-medium small">
                    Question Type <span class="text-danger">*</span>
                </label>
                <select wire:model="questionTypeId"
                    class="form-select @error('questionTypeId') is-invalid @enderror">
                    <option value="">— Select Type —</option>
                    @foreach ($questionTypes as $type)
                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                    @endforeach
                </select>
                @error('questionTypeId')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Primary Skill --}}
            <div class="col-md-4">
                <label class="form-label fw-medium small">
                    Primary Skill <span class="text-danger">*</span>
                </label>
                <select wire:model="primarySkillTypeId"
                    class="form-select @error('primarySkillTypeId') is-invalid @enderror">
                    <option value="">— Select Skill —</option>
                    @foreach ($primarySkillTypes as $skill)
                        <option value="{{ $skill['id'] }}">{{ $skill['name'] }}</option>
                    @endforeach
                </select>
                @error('primarySkillTypeId')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Sub Skill --}}
            <div class="col-md-4">
                <label class="form-label fw-medium small">
                    Sub Skill <span class="text-danger">*</span>
                </label>
                <select wire:model="subSkillTypeId"
                    class="form-select @error('subSkillTypeId') is-invalid @enderror">
                    <option value="">— Select Sub Skill —</option>
                    @foreach ($subSkillTypes as $skill)
                        <option value="{{ $skill['id'] }}">{{ $skill['name'] }}</option>
                    @endforeach
                </select>
                @error('subSkillTypeId')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Difficulty Level --}}
            <div class="col-md-4">
                <label class="form-label fw-medium small">
                    Difficulty Level <span class="text-danger">*</span>
                </label>
                <select wire:model="difficultyLevelId"
                    class="form-select @error('difficultyLevelId') is-invalid @enderror">
                    <option value="">— Select Difficulty —</option>
                    @foreach ($difficultyLevels as $level)
                        <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                    @endforeach
                </select>
                @error('difficultyLevelId')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Age Group --}}
            <div class="col-md-4">
                <label class="form-label fw-medium small">
                    Age Group <span class="text-danger">*</span>
                </label>
                <select wire:model="ageGroupId"
                    class="form-select @error('ageGroupId') is-invalid @enderror">
                    <option value="">— Select Age Group —</option>
                    @foreach ($ageGroups as $group)
                        <option value="{{ $group['id'] }}">{{ $group['name'] }}</option>
                    @endforeach
                </select>
                @error('ageGroupId')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>
    </div>
</div>

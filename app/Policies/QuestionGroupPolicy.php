<?php
// app/Policies/QuestionGroupPolicy.php

namespace App\Policies;

use App\Models\Admin;
use App\Models\QuestionMaster\QuestionGroup;

class QuestionGroupPolicy
{
    public function viewAny(Admin $user): bool
    {
        return $user->isAdmin();
    }

    public function view(Admin $user, QuestionGroup $group): bool
    {
        return $user->isAdmin();
    }

    public function create(Admin $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Cannot edit core content if linked to an assessment group.
     */
    public function update(Admin $user, QuestionGroup $group): bool
    {
        return $user->isAdmin() && ! $group->isLinkedToAssessment();
    }

    /**
     * Cannot delete if linked to an assessment group.
     */
    public function delete(Admin $user, QuestionGroup $group): bool
    {
        return $user->isAdmin() && ! $group->isLinkedToAssessment();
    }

    /**
     * Can always add a new question to the group
     * even if linked, as long as admin.
     */
    public function addQuestion(Admin $user, QuestionGroup $group): bool
    {
        return $user->isAdmin();
    }
}

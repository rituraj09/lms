<?php
// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\QuestionMaster\Question;
use App\Models\QuestionMaster\QuestionGroup;
use App\Policies\QuestionPolicy;
use App\Policies\QuestionGroupPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        QuestionGroup::class => QuestionGroupPolicy::class,
        Question::class      => QuestionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

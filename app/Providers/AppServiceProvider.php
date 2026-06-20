<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Admin\Questions\QuestionGroupIndex;
use App\Livewire\Admin\Questions\QuestionGroupForm;
use App\Livewire\Admin\Questions\QuestionForm;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Super Admin bypasses ALL permission & organisation checks ──
        Gate::before(function ($user, $ability) {
        if ($user instanceof \App\Models\Admin
            && $user->hasRole('super_admin')) {
            return true;
        }

        return null;
    });
        Livewire::component('admin.questions.question-group-index', QuestionGroupIndex::class);
        Livewire::component('admin.questions.question-group-form',  QuestionGroupForm::class);
        Livewire::component('admin.questions.question-form',        QuestionForm::class);
    }
}

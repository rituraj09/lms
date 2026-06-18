<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Admin\Questions\QuestionGroupIndex;
use App\Livewire\Admin\Questions\QuestionGroupForm;
use App\Livewire\Admin\Questions\QuestionForm;

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
        Livewire::component('admin.questions.question-group-index', QuestionGroupIndex::class);
        Livewire::component('admin.questions.question-group-form',  QuestionGroupForm::class);
        Livewire::component('admin.questions.question-form',        QuestionForm::class);
    }
}

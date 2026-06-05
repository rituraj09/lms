<?php
use App\Livewire\EvaluationMaster\QuestionManager;
use App\Livewire\EvaluationMaster\QuestionSetManager;
use App\Livewire\EvaluationMaster\QuestionSetBuilder;

Route::group(['middleware' => 'redirect.auth:admin'], function ($router) {
   $router->livewire('login', 'admin.login')->name('login');
});

Route::group(['middleware' => 'redirect.notauth:admin'], function ($router) {
    $router->livewire('home', 'admin.home')->name('home');
    $router->livewire('role', 'admin.role')->name('role');
    $router->livewire('designation', 'admin.designation')->name('designation');
    $router->livewire('organisation', 'admin.organisation')->name('organisation');
    $router->livewire('employee', 'admin.employee')->name('employee');



    Route::get('/questions', QuestionManager::class)->name('questions');
    Route::get('/questions/{questionId}', QuestionManager::class)->name('questions.edit');

    Route::get('/question-sets',                 QuestionSetManager::class)->name('question-sets.index');
    Route::get('/question-sets/{setId}/builder', QuestionSetBuilder::class)->name('question-sets.builder');
});

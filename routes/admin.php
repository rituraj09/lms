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


    $router->livewire(
    'question-groups',
    'evaluation-master.question-group-index'
    )->name('question-groups.index');

    $router->livewire(
        'question-groups/create',
        'evaluation-master.question-group-form'
    )->name('question-groups.create');

    $router->livewire(
        'question-groups/{groupId}/edit',
        'evaluation-master.question-group-form'
    )->name('question-groups.edit');


});

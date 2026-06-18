<?php
//routes/admin.php
use App\Livewire\Admin\Questions\QuestionGroupIndex;
use App\Livewire\Admin\Questions\QuestionGroupForm;

Route::group(['middleware' => 'redirect.auth:admin'], function ($router) {
   $router->livewire('login', 'admin.login')->name('login');
});

Route::group(['middleware' => 'redirect.notauth:admin'], function ($router) {
    $router->livewire('home', 'admin.home')->name('home');
    $router->livewire('role', 'admin.role')->name('role');
    $router->livewire('designation', 'admin.designation')->name('designation');
    $router->livewire('organisation', 'admin.organisation')->name('organisation');
    $router->livewire('employee', 'admin.employee')->name('employee');


    Route::get('/question-groups', QuestionGroupIndex::class)->name('question-groups');
    Route::get('/question-groups/create', QuestionGroupForm::class)->name('question-groups.create');
    Route::get('/question-groups/{groupId}/edit', QuestionGroupForm::class)->name('question-groups.edit');





});

<?php

Route::group(['middleware' => 'redirect.auth:admin'], function ($router) {
   $router->livewire('login', 'admin.login')->name('login');
});

Route::group(['middleware' => 'redirect.notauth:admin'], function ($router) {
    $router->livewire('home', 'admin.home')->name('home');
    $router->livewire('role', 'admin.role')->name('role');
    $router->livewire('designation', 'admin.designation')->name('designation');
    $router->livewire('organisation', 'admin.organisation')->name('organisation');
    $router->livewire('employee', 'admin.employee')->name('employee');



    $router->livewire('agegroup', 'admin.master.agegroup')->name('agegroup');
    $router->livewire('difficulty-level', 'admin.master.difficultylevel')->name('difficulty-level');
    $router->livewire('primary-skill-type', 'admin.master.primaryskill')->name('primary-skill-type');
    $router->livewire('sub-skill-type', 'admin.master.subskill')->name('sub-skill-type');
    $router->livewire('question-type', 'admin.master.questiontype')->name('question-type');
});

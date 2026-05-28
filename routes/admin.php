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


    $router->livewire('mcq', 'admin.questionmaster.mcq')->name('mcq');
});

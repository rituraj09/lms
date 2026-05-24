<?php

Route::group(['middleware' => 'redirect.auth:admin'], function ($router) {
   $router->livewire('login', 'admin.login')->name('login');
});

Route::group(['middleware' => 'redirect.notauth:admin'], function ($router) {
    //admin pages
});

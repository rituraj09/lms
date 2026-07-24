<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoRequestController;

Route::view('/', 'website.index')->name('home');

Route::view('/cognitive', 'website.cognitive')
    ->name('cognitive');

Route::view('/life-skill', 'website.life-skill')
    ->name('life-skill');
Route::view('/leadership-skill', 'website.leadership')
    ->name('leadership-skill');
Route::view('/solutions', 'website.solutions')
    ->name('solutions');

Route::view('/use-cases', 'website.use-cases')
    ->name('use-cases');

Route::view('/about', 'website.about')
    ->name('about');

Route::view('/resources', 'website.resources')
    ->name('resources');

Route::view('/contact', 'website.contact')
    ->name('contact');

Route::post('/demo-request', [DemoRequestController::class, 'store'])
    ->name('demo-request.store');

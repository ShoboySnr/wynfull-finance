<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::view('/login', 'auth.login')->name('login');

Route::view('/dashboard/client', 'dashboards.client')->name('dashboard.client');
Route::view('/dashboard/coach',  'dashboards.coach')->name('dashboard.coach');

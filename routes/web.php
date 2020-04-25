<?php
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('/', 'GeneralController@homepage');
Route::get('/workspace', 'GeneralController@workspace')->middleware('auth');
Route::get('accept-invitation', 'Auth\RegisterController@showRegistrationForm')->middleware('signed')->name('accept-invitation');
Route::get('/markdown-guide', 'GeneralController@markdownGuide');
Route::get('/help', 'GeneralController@help');
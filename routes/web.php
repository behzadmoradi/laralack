<?php
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('/', 'GeneralController@homepage');
Route::get('/workspace', 'GeneralController@workspace')->middleware('auth');
Route::get('accept-invitation', 'Auth\RegisterController@showRegistrationForm')->middleware('signed')->name('accept-invitation');
//TODO
// this route needs to be removed
Route::get('/mail-tester', 'GeneralController@mailTester');

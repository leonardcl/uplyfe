<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/signup', function () {
    return view('signup');
});

Route::get('/chat', function () {
    return view('chat');
});

Route::get('/exercise', function () {
    return view('exercise');
});

Route::get('/health-check', function () {
    return view('healthcheck');
});

Route::get('/full-analysis', function () {
    return view('fullanalysis');
});

Route::get('/recipe', function () {
    return view('recipe');
});

Route::get('/features', function () {
    return view('features');
});

Route::get('/how-it-works', function () {
    return view('howitworks');
});

Route::get('/testimonials', function () {
    return view('testimonials');
});

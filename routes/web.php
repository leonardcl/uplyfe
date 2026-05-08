<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Models\User;

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

Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::get('/signup', function () {
    return view('signup');
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

Route::resource('users', UserController::class)->only(['store']);

Route::middleware(['checklogin'])->group(function () {
    Route::get('/health-check', function () {
        return view('healthcheck');
    });

    Route::get('/recipe', function () {
        return view('recipe');
    });
    
    Route::get('/exercise', function () {
        $sessionUser = session('user');
        $user = null;
    
        if ($sessionUser && isset($sessionUser->id)) {
            $user = User::find($sessionUser->id);
            if ($user !== null) {
                session(['user' => $user]);
            }
        }
    
        return view('exercise', ['user' => $user]);
    });
    
    Route::get('/chat', function () {
        return view('chat');
    });

    Route::get('/full-analysis', function () {
        return view('fullanalysis');
    });
    
    Route::get('/profile', function () {
        return view('profile');
    });

    Route::get('/favorite-recipes', function () {
        return view('favoriterecipes');
    });

    Route::resource('users', UserController::class)->only(['update']);
});
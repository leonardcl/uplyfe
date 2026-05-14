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

Route::get('/logout', function () {
    session()->forget('user');
    session()->flush();

    return redirect('/login');
});

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
        $sessionUser = session('user');
        $user = null;

        if ($sessionUser && isset($sessionUser->id)) {
            $user = User::find($sessionUser->id);

            if ($user !== null) {
                session(['user' => $user]);
            }
        }

        return view('healthcheck', ['user' => $user]);
    });

    Route::get('/recipe', function () {
        $sessionUser = session('user');
        $user = null;

        if ($sessionUser && isset($sessionUser->id)) {
            $user = User::find($sessionUser->id);

            if ($user !== null) {
                session(['user' => $user]);
            }
        }

        return view('recipe', ['user' => $user]);
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
        $sessionUser = session('user');
        $user = null;
    
        if ($sessionUser && isset($sessionUser->id)) {
            $user = User::find($sessionUser->id);
            if ($user !== null) {
                session(['user' => $user]);
            }
        }

        return view('chat', ['user' => $user]);
    });

    Route::get('/full-analysis', function () {
        $user = User::find(session('user')->id);
        session(['user' => $user]);

        return view('fullanalysis', ['user' => $user]);
    });
    
    Route::get('/profile', function () {
        $user = User::find(session('user')->id);
        session(['user' => $user]);
    
        return view('profile', ['user' => $user]);
    });

    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/favorite-recipes', function () {
        $user = User::find(session('user')->id);
        session(['user' => $user]);

        return view('favoriterecipes', ['user' => $user]);
    });

    Route::resource('users', UserController::class)->only(['update']);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SignupController;
// Route::get('/chatvue', function () {
//     return view('chatVue');
// });
Route::get('/signup', [SignupController::class, 'index'])->name('signup.index');
Route::post('/signup', [SignupController::class, 'create'])->name('signup.create');

Auth::routes();

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('chat');
    Route::get('/getMessages/{userId}', [MessageController::class, 'getMessages']);
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{UserController};


//User controllers group routing
Route::controller(UserController::class)->middleware('auth', 'user', 'verified')->group(function () {
Route::get('user/dashboard', 'dashboard')->name('user.dashboard');



});

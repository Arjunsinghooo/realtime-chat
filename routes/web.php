<?php

use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    $user = request()->user();

    return 'Hello ' . $user->name . ', your ID is ' . $user->id;
})->middleware('auth:web');
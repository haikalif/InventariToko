<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main.index');
});

Route::get('/products', function () {
    return view('product.index');
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('beranda.index');
});

Route::get('/products', function () {
    return view('product.index');
});

Route::get('/categories', function () {
    return view('kategoriProduk.index');
});

Route::get('/suppliers', function () {
    return view('pemasok.index');
});

Route::get('/sales/create', function () {
    return view('sales.create');
});

Route::get('/sales', function () {
    return view('sales.index');
});

Route::get('/stock-movements', function () {
    return view('stockMovements.index');
});

Route::get('/purchase-orders', function () {
    return view('purchaseOrders.index');
});

Route::get('/users', function () {
    return view('users.index');
});

Route::get('/profile', function () {
    return view('profile.index');
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

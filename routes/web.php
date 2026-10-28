<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// AUTH ROUTE
Route::get('/login', function () {
    return view('auth.login');
});



// ADMIN ROUTE
Route::get('/admin', function () {
    return view('admin.index');
});

// KARYWAN ROUTE 
Route::get('/karyawan', function () {
    return view('karyawan.index');
});
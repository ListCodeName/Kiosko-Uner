<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('panel.index');
});

Route::get('/showcase', function () {
    return view('showcase.index');
});

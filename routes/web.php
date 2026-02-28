<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

Route::get('/', function () {
    $host = Request::getHost();

    if (str_contains($host, 'pro')) {
        return redirect('/admin');
    }
    return redirect('/app');
});


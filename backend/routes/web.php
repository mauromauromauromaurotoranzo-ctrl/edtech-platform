<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'EdTech Platform API',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});

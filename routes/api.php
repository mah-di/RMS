<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->group(fn () => require __DIR__.'/../routes/apis/v1.php');

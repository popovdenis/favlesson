<?php

use Illuminate\Support\Facades\Route;
use Modules\Term\Http\Controllers\TermController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('terms', TermController::class)->names('term');
});

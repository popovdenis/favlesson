<?php

use Illuminate\Support\Facades\Route;
use Modules\Term\Http\Controllers\TermController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('terms', TermController::class)->names('term');
});

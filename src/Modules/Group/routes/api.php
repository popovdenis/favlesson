<?php

use Illuminate\Support\Facades\Route;
use Modules\Group\Http\Controllers\GroupController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('groups', GroupController::class)->names('group');
});

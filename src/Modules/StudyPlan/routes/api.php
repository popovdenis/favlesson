<?php

use Illuminate\Support\Facades\Route;
use Modules\StudyPlan\Http\Controllers\StudyPlanController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('studyplans', StudyPlanController::class)->names('studyplan');
});

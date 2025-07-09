<?php

use Illuminate\Support\Facades\Route;
use Modules\StudyPlan\Http\Controllers\StudyPlanController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('studyplans', StudyPlanController::class)->names('studyplan');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\StudyPlan\Http\Controllers\StudyPlanController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/lesson-slots', [StudyPlanController::class, 'index'])->name('lessons.slots');
});

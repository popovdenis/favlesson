<?php

use Illuminate\Support\Facades\Route;
use Modules\Group\Http\Controllers\GroupController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('groups', GroupController::class)->names('group');
});

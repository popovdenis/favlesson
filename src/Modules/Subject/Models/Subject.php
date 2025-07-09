<?php

namespace Modules\Subject\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\StudyPlan\Models\StudyPlan;
use Modules\User\Models\User;

class Subject extends Model
{
    protected $fillable = [
        'title',
    ];

    public function studyPlans()
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}

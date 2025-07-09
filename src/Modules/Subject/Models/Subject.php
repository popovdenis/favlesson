<?php

namespace Modules\Subject\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\StudyPlan\Models\StudyPlan;

class Subject extends Model
{
    protected $fillable = [
        'title',
    ];

    public function studyPlans()
    {
        return $this->hasMany(StudyPlan::class);
    }
}

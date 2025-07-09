<?php

namespace Modules\Group\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\StudyPlan\Models\StudyPlan;

class Group extends Model
{
    protected $fillable = [
        'title',
    ];

    public function studyPlans()
    {
        return $this->hasMany(StudyPlan::class);
    }
}

<?php

namespace Modules\StudyPlan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Group\Models\Group;
use Modules\Subject\Models\Subject;

class StudyPlan extends Model
{
    protected $fillable = [
        'group_id',
        'subject_id',
        'year',
        'hours_per_year',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

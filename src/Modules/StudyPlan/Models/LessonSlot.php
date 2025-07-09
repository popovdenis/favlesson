<?php

namespace Modules\StudyPlan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Group\Models\Group;
use Modules\Subject\Models\Subject;
use Modules\Term\Models\Term;
use Modules\User\Models\User;

class LessonSlot extends Model
{
    protected $fillable = [
        'term_id',
        'group_id',
        'subject_id',
        'teacher_id',
        'date',
        'slot_order',
        'comment',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

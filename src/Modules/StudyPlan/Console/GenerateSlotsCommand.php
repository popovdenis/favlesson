<?php

namespace Modules\StudyPlan\Console;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Modules\Base\Framework\DataObject;
use Modules\Group\Models\Group;
use Modules\StudyPlan\Models\LessonSlot;
use Modules\StudyPlan\Models\StudyPlan;
use Modules\Subject\Models\Subject;
use Modules\Term\Models\Term;
use Modules\User\Models\User;

class GenerateSlotsCommand extends Command
{
    private DataObject $dataObject;

    private $lessonDuration;
    private $dayStartTime;
    private $shortBreak;
    private $longBreak;
    private $longAfter;
    private $maxLessonsPerDay;

    public function __construct(DataObject $dataObject)
    {
        parent::__construct();
        $this->dataObject = $dataObject;

        $this->lessonDuration = config('school.lesson_duration_minutes', 45);
        $this->dayStartTime = config('school.day_start_time', '08:30');
        $this->shortBreak = config('school.short_break_minutes');
        $this->longBreak = config('school.long_break_minutes');
        $this->longAfter = config('school.long_breaks_after_slots', []);
        $this->maxLessonsPerDay = config('school.max_lessons_per_day', 6);
    }

    protected $signature = 'school:generate-slots
        {--term= : Term ID}
        {--group= : Group ID}
        {--subject= : Subject ID}
        {--teacher= : Teacher ID}
        {--clear=1 : Clear existing slots first}';

    protected $description = 'Generate lesson slots for specified terms, groups, subjects and teachers.';

    public function handle()
    {
        $termId    = $this->option('term');
        $groupId   = $this->option('group');
        $subjectId = $this->option('subject');
        $teacherId = $this->option('teacher');
        $clear     = (int) $this->option('clear');

        if ($clear) {
            $this->info('Clearing existing slots...');
            LessonSlot::query()
                ->when($termId, fn($q) => $q->where('term_id', $termId))
                ->when($groupId, fn($q) => $q->where('group_id', $groupId))
                ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
                ->when($teacherId, fn($q) => $q->where('teacher_id', $teacherId))
                ->delete();
        }

        $terms = $termId
            ? Term::where('id', $termId)->get()
            : Term::whereYear('start_date', now()->year)->get();

        $subjects = $subjectId
            ? Subject::where('id', $subjectId)->get()
            : Subject::all();

        $groups = $groupId
            ? Group::where('id', $groupId)->get()
            : Group::all();

        $teachers = $teacherId
            ? User::where('id', $teacherId)->role('teacher')->get()
            : User::role('teacher')->get();

        foreach ($terms as $term) {
            foreach ($groups as $group) {
                $plans = StudyPlan::where('group_id', $group->id)
                    ->whereIn('subject_id', $subjects->pluck('id'))
                    ->where('year', $term->start_date->format('Y'))
                    ->get();

                if ($plans->isEmpty()) {
                    $this->warn("No study plans for group {$group->title} in term {$term->title}");
                    continue;
                }

                // Filter teachers
                $eligibleTeachers = $teachers->filter(function (User $teacher) use ($plans) {
                    return $teacher->subjects->pluck('id')->intersect($plans->pluck('subject_id'))->isNotEmpty();
                });

                if ($eligibleTeachers->isEmpty()) {
                    $this->warn("No eligible teachers for group {$group->title}");
                    continue;
                }

                $this->info("Generating for {$group->title} / {$term->title}");
                $this->generate($term, $group, $eligibleTeachers, $plans);
            }
        }

        $this->info('Slots generated successfully.');
    }

    public function generate(Term $term, Group $group, $teachers, $studyPlans): void
    {
        $academicDays = $this->getAcademicDays($term);
        $weeks = $academicDays->groupBy(fn($date) => $date->copy()->startOfWeek()->format('Y-m-d'));

        $weeklyDistribution = $this->calculateWeeklyDistribution($studyPlans, $academicDays->count());

        $teacherMap = collect();

        foreach ($teachers as $teacher) {
            foreach ($teacher->subjects as $subject) {
                $teacherMap[$subject->id] = $teacher;
            }
        }

        foreach ($weeks as $week) {
            foreach ($week as $day) {
                $dailySlots = $this->generateDailySlots($day, $weeklyDistribution, $teacherMap);

                foreach ($dailySlots as $slot) {
                    LessonSlot::create([
                        'term_id'     => $term->id,
                        'group_id'    => $group->id,
                        'subject_id'  => $slot['subject_id'],
                        'teacher_id'  => $slot['teacher_id'],
                        'date'        => $slot['date'],
                        'slot_order'  => $slot['slot_order'],
                        'start_time'  => $slot['start_time'],
                        'end_time'    => $slot['end_time'],
                    ]);
                }
            }
        }
    }

    protected function generateDailySlots($day, DataObject $weeklyDistribution, $teacherMap): array
    {
        $subjectList = collect($weeklyDistribution->getData())
            ->filter(fn($item) => $item->getData('remaining') > 0)
            ->sortByDesc(fn($item) => $item->getData('remaining'))
            ->keys()
            ->take($this->maxLessonsPerDay)
            ->toArray();

        $startTime = Carbon::createFromFormat('H:i', $this->dayStartTime);
        $slots = [];
        $slotIndex = 1;

        foreach ($subjectList as $subjectId) {
            $teacher = $teacherMap[$subjectId] ?? null;

            if (!$weeklyDistribution->hasData($subjectId)) {
                continue;
            }

            $subject = $weeklyDistribution->getData($subjectId);
            $remaining = $subject->getData('remaining');

            if (!$teacher || $remaining <= 0) {
                continue;
            }

            $endTime = $startTime->copy()->addMinutes($this->lessonDuration);

            $slots[] = [
                'subject_id'  => $subjectId,
                'teacher_id'  => $teacher->id,
                'date'        => $day->toDateString(),
                'slot_order'  => $slotIndex,
                'start_time'  => $startTime->format('H:i'),
                'end_time'    => $endTime->format('H:i'),
            ];

            $subject->setData('remaining', --$remaining);

            $break = in_array($slotIndex, $this->longAfter) ? $this->longBreak : $this->shortBreak;
            $startTime = $endTime->copy()->addMinutes($break);
            $slotIndex++;
        }

        return $slots;
    }

    protected function getAcademicDays(Term $term)
    {
        return collect(CarbonPeriod::create($term->start_date, '1 day', $term->end_date))
            ->filter(fn(Carbon $d) => $d->isWeekday())
            ->values();
    }

    protected function calculateWeeklyDistribution($studyPlans, int $totalDays): DataObject
    {
        $totalWeeks = intdiv($totalDays, 5);
        $result = $this->dataObject->create();

        foreach ($studyPlans as $plan) {
            $totalLessons = intdiv($plan->hours_per_year, $this->lessonDuration);
            $perWeek = $totalWeeks > 0 ? intdiv($totalLessons, $totalWeeks) : 0;

            $weeklySlot = $this->dataObject->create([
                'total'     => $totalLessons,
                'per_week'  => $perWeek,
                'remaining' => $totalLessons,
            ]);
            $result->setData($plan->subject_id, $weeklySlot);
        }

        return $result;
    }
}

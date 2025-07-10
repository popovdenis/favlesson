<?php

namespace Modules\StudyPlan\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Modules\Base\Framework\DataObject;
use Modules\Group\Models\Group;
use Modules\StudyPlan\Contracts\LessonSlotRepositoryInterface;
use Modules\Term\Models\Term;

class LessonSlotService
{
    private $lessonDuration;
    private $dayStartTime;
    private $shortBreak;
    private $longBreak;
    private $longAfter;
    private $maxLessonsPerDay;

    public function __construct(
        private readonly DataObject $dataObject,
        private readonly LessonSlotRepositoryInterface $lessonSlotRepository,
    )
    {
        $this->lessonDuration   = (int)config('school.lesson_duration_minutes', 45);
        $this->dayStartTime     = config('school.day_start_time', '08:30');
        $this->shortBreak       = (int)config('school.short_break_minutes');
        $this->longBreak        = (int)config('school.long_break_minutes');
        $this->longAfter        = (array)config('school.long_breaks_after_slots', []);
        $this->maxLessonsPerDay = config('school.max_lessons_per_day', 6);
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
                    $this->lessonSlotRepository->create([
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

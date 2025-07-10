<?php

namespace Modules\StudyPlan\Console;

use Illuminate\Console\Command;

use Modules\Base\Contracts\SearchCriteriaInterface;
use Modules\Group\Contracts\GroupRepositoryInterface;
use Modules\StudyPlan\Contracts\LessonSlotRepositoryInterface;
use Modules\StudyPlan\Contracts\StudyPlanRepositoryInterface;
use Modules\StudyPlan\Services\LessonSlotService;
use Modules\Subject\Contracts\SubjectRepositoryInterface;
use Modules\Term\Contracts\TermRepositoryInterface;
use Modules\User\Contracts\TeacherRepositoryInterface;
use Modules\User\Models\User;

class GenerateSlotsCommand extends Command
{
    public function __construct(
        private readonly TermRepositoryInterface $termRepository,
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly SubjectRepositoryInterface $subjectRepository,
        private readonly TeacherRepositoryInterface $teacherRepository,
        private readonly SearchCriteriaInterface $searchCriteria,
        private readonly StudyPlanRepositoryInterface $studyPlanRepository,
        private readonly LessonSlotRepositoryInterface $lessonSlotRepository,
        private readonly LessonSlotService $lessonSlotService
    )
    {
        parent::__construct();
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
            $searchCriteria = clone $this->searchCriteria;
            $searchCriteria->setWhen([
                $termId => fn($q) => $q->where('term_id', $termId),
                $groupId => fn($q) => $q->where('group_id', $groupId),
                $subjectId => fn($q) => $q->where('subject_id', $subjectId),
                $teacherId => fn($q) => $q->where('teacher_id', $teacherId),
            ]);
            $this->lessonSlotRepository->deleteByQuery($searchCriteria);
        }

        $terms = $termId
            ? $this->termRepository->getById($termId)
            : $this->termRepository->getByStartDate(now()->year);

        $subjects = $subjectId
            ? $this->subjectRepository->getById($subjectId)
            : $this->subjectRepository->getAll();

        $groups = $groupId
            ? $this->groupRepository->getById($groupId)
            : $this->groupRepository->getAll();

        $teachers = $teacherId
            ? $this->teacherRepository->getById($teacherId)
            : $this->teacherRepository->getAll();

        foreach ($terms as $term) {
            foreach ($groups as $group) {
                $searchCriteria = clone $this->searchCriteria;
                $searchCriteria->setFilters([
                    'group_id' => $group->id,
                    'subject_id' => $subjects->pluck('id'),
                    'year' => $term->start_date->format('Y')
                ]);
                $plans = $this->studyPlanRepository->getList($searchCriteria);

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
                $this->lessonSlotService->generate($term, $group, $eligibleTeachers, $plans);
            }
        }

        $this->info('Slots generated successfully.');
    }
}

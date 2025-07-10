<?php

namespace Modules\StudyPlan\Models;

use Modules\Base\Contracts\SearchCriteriaInterface;
use Modules\StudyPlan\Contracts\LessonSlotRepositoryInterface;

class LessonSlotRepository implements LessonSlotRepositoryInterface
{
    public function create(array $data)
    {
        return LessonSlot::create($data);
    }

    public function getById($entityId)
    {
        return LessonSlot::findOrFail($entityId);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return LessonSlot::all();
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $query = LessonSlot::query();

        if ($searchCriteria->getFilters()) {
            foreach ($searchCriteria->getFilters() as $field => $value) {
                if (is_array($value) || $value instanceof \Illuminate\Support\Collection) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query->get();
    }

    public function deleteByQuery(SearchCriteriaInterface $searchCriteria)
    {
        $query = LessonSlot::query();

        if ($searchCriteria->getWhen()) {
            foreach ($searchCriteria->getWhen() as $field => $value) {
                $query->when($field, $value);
            }
        }

        $query->delete();
    }
}

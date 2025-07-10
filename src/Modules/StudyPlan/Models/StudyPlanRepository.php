<?php

namespace Modules\StudyPlan\Models;

use Modules\Base\Contracts\SearchCriteriaInterface;
use Modules\StudyPlan\Contracts\StudyPlanRepositoryInterface;

class StudyPlanRepository implements StudyPlanRepositoryInterface
{
    public function create(array $data)
    {
        return StudyPlan::create($data);
    }

    public function getById($entityId)
    {
        return StudyPlan::findOrFail($entityId);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return StudyPlan::all();
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $query = StudyPlan::query();

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
}

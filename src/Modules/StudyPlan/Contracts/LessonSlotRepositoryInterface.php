<?php

namespace Modules\StudyPlan\Contracts;

use Modules\Base\Contracts\BaseRepositoryInterface;
use Modules\Base\Contracts\SearchCriteriaInterface;

interface LessonSlotRepositoryInterface extends BaseRepositoryInterface
{
    public function getList(SearchCriteriaInterface $searchCriteria);

    public function deleteByQuery(SearchCriteriaInterface $searchCriteria);
}

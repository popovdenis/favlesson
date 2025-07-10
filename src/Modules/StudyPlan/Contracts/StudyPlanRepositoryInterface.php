<?php

namespace Modules\StudyPlan\Contracts;

use Modules\Base\Contracts\BaseRepositoryInterface;
use Modules\Base\Contracts\SearchCriteriaInterface;

interface StudyPlanRepositoryInterface extends BaseRepositoryInterface
{
    public function getList(SearchCriteriaInterface $searchCriteria);
}

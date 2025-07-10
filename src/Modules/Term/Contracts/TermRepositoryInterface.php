<?php

namespace Modules\Term\Contracts;

use Modules\Base\Contracts\BaseRepositoryInterface;

interface TermRepositoryInterface extends BaseRepositoryInterface
{
    public function getByStartDate($startDate);
}

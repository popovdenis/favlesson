<?php
declare(strict_types=1);

namespace Modules\Base\Framework;

use Modules\Base\Contracts\SearchCriteriaInterface;

/**
 * Class SearchCriteria
 *
 * @package Modules\Base\Framework
 */
class SearchCriteria extends DataObject implements SearchCriteriaInterface
{
    public function setWhen(array $filters)
    {
        return $this->setData(self::WHEN, $filters);
    }

    public function getWhen()
    {
        return $this->getData(self::WHEN);
    }

    public function setFilters(array $filters)
    {
        return $this->setData(self::FILTERS, $filters);
    }

    public function getFilters()
    {
        return $this->getData(self::FILTERS);
    }

    public function setWith(array $filters)
    {
        return $this->setData(self::WITH, $filters);
    }

    public function getWith()
    {
        return $this->getData(self::WITH);
    }

    public function setWhereHas(array $filters)
    {
        return $this->setData(self::WHERE_HAS, $filters);
    }

    public function setWhere($closure)
    {
        return $this->setData(self::WHERE, $closure);
    }

    public function getWhere()
    {
        return $this->getData(self::WHERE);
    }

    public function getWhereHas()
    {
        return $this->getData(self::WHERE_HAS);
    }

    public function setSorts(array $sorts)
    {
        return $this->setData(self::SORTS, $sorts);
    }

    public function getSorts()
    {
        return $this->getData(self::SORTS);
    }

    public function setPage(int $page)
    {
        return $this->setData(self::PAGE, $page);
    }

    public function getPage()
    {
        return $this->getData(self::PAGE, 1);
    }

    public function setPageSize(int $pageSize)
    {
        return $this->setData(self::PAGE_SIZE, $pageSize);
    }

    public function getPageSize()
    {
        return $this->getData(self::PAGE_SIZE, 20);
    }
}

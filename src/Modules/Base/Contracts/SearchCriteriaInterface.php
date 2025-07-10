<?php

namespace Modules\Base\Contracts;

/**
 * Interface SarchCriteriaInterface
 *
 * @package Modules\Base\Conracts
 */
interface SearchCriteriaInterface
{
    const WHEN = 'when';
    const FILTERS = 'filters';
    const WITH = 'with';
    const WHERE = 'where';
    const WHERE_HAS = 'where_has';
    const SORTS = 'sorts';
    const PAGE = 'page';
    const PAGE_SIZE = 'page_size';

//    protected int $page = 1;
//    protected int $pageSize = 20;

    public function setWhen(array $filters);

    public function getWhen();

    public function setFilters(array $filters);

    public function getFilters();

    public function setWith(array $filters);

    public function getWith();

    public function setWhereHas(array $filters);

    public function setWhere($closure);

    public function getWhere();

    public function getWhereHas();

    public function setSorts(array $sorts);

    public function getSorts();

    public function setPage(int $page);

    public function getPage();

    public function setPageSize(int $pageSize);

    public function getPageSize();
}

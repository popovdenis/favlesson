<?php

namespace Modules\Base\Contracts;

interface BaseRepositoryInterface
{
    public function create(array $data);

    public function getById($entityId);

    public function getAll();
}

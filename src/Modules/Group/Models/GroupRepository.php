<?php

namespace Modules\Group\Models;

use Modules\Group\Contracts\GroupRepositoryInterface;

class GroupRepository implements GroupRepositoryInterface
{
    public function create(array $data)
    {
        return Group::create($data);
    }

    public function getById($entityId)
    {
        return Group::findOrFail($entityId);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Group::all();
    }
}

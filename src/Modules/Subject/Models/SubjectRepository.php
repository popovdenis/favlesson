<?php

namespace Modules\Subject\Models;

use Modules\Subject\Contracts\SubjectRepositoryInterface;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function create(array $data)
    {
        return Subject::create($data);
    }

    public function getById($entityId)
    {
        return Subject::findOrFail($entityId);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Subject::all();
    }
}

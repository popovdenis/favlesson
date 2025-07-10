<?php

namespace Modules\User\Models;

use Modules\User\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function getById($entityId)
    {
        return User::findOrFail($entityId);
    }

    public function getAll()
    {
        return User::all();
    }
}

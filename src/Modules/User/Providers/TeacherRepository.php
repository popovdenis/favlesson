<?php

namespace Modules\User\Providers;

use Modules\User\Contracts\TeacherRepositoryInterface;
use Modules\User\Models\User;
use Modules\User\Models\UserRepository;

class TeacherRepository extends UserRepository implements TeacherRepositoryInterface
{
    public function getById($entityId)
    {
        return User::where('id', $entityId)->role('teacher')->get();
    }

    public function getAll()
    {
        return User::role('teacher')->get();
    }
}

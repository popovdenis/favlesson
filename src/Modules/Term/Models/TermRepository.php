<?php
declare(strict_types=1);

namespace Modules\Term\Models;

use Modules\Term\Contracts\TermRepositoryInterface;

class TermRepository implements TermRepositoryInterface
{
    public function create(array $data)
    {
        return Term::create($data);
    }

    public function getById($entityId)
    {
        return Term::findOrFail($entityId);
    }

    public function getByStartDate($startDate)
    {
        return Term::whereYear('start_date', $startDate)->get();
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Term::all();
    }
}

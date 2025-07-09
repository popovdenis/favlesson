<?php

namespace Modules\Term\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Term\Models\Term;

class NoTermOverlap implements ValidationRule
{
    protected ?int $currentId;

    public function __construct(?int $currentId = null)
    {
        $this->currentId = $currentId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = request()->input('start_date');
        $end = request()->input('end_date');

        if (!$start || !$end) {
            return;
        }

        $overlapExists = Term::where(function ($query) use ($start, $end) {
            $query
                ->where('start_date', '<=', $end)
                ->where('end_date', '>=', $start);
        })
            ->when($this->currentId, fn ($q) => $q->where('id', '!=', $this->currentId))
            ->exists();

        if ($overlapExists) {
            $fail('This term overlaps with an existing term.');
        }
    }
}

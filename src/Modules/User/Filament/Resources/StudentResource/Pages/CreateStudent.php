<?php

namespace Modules\User\Filament\Resources\StudentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\User\Filament\Resources\StudentResource;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
}

<?php

namespace Modules\Group\Filament\Resources\GroupResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Group\Filament\Resources\GroupResource;

class ListGroup extends ListRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace Modules\Term\Filament\Resources\TermResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Term\Filament\Resources\TermResource;

class ListTerms extends ListRecords
{
    protected static string $resource = TermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

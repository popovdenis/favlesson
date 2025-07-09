<?php

namespace Modules\Term\Filament\Resources\TermResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Term\Filament\Resources\TermResource;

class EditTerm extends EditRecord
{
    protected static string $resource = TermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

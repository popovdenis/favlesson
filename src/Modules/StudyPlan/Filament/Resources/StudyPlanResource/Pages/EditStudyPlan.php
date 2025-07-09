<?php

namespace Modules\StudyPlan\Filament\Resources\StudyPlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\StudyPlan\Filament\Resources\StudyPlanResource;

class EditStudyPlan extends EditRecord
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace Modules\StudyPlan\Filament\Resources\StudyPlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\StudyPlan\Filament\Resources\StudyPlanResource;

class ListStudyPlans extends ListRecords
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

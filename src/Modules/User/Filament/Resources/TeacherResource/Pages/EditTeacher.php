<?php

namespace Modules\User\Filament\Resources\TeacherResource\Pages;

use Filament\Actions;
use Modules\User\Filament\Resources\TeacherResource;
use Modules\User\Filament\Resources\UserResource\Pages\EditUser;

class EditTeacher extends EditUser
{
    protected static string $resource = TeacherResource::class;
    protected static ?string $title = 'Edit Teacher';
    protected static ?string $breadcrumb = 'Edit Teacher';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record->id]);
    }
}

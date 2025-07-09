<?php

namespace Modules\User\Filament\Resources;

use Modules\User\Filament\Resources\StudentResource\RelationManagers;
use Filament\Resources\Resource;
use Modules\User\Models\User;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Members';
    protected static ?string $breadcrumb = 'Members';
    protected static ?string $navigationLabel = 'Students';
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->role('student')
            /*->with(['subscription.plan'])*/;
    }

    public static function getPages(): array
    {
        return [
            'index'  => StudentResource\Pages\ListStudents::route('/'),
            'create' => StudentResource\Pages\CreateStudent::route('/create'),
            'edit'   => StudentResource\Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}

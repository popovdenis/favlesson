<?php

namespace Modules\User\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Modules\User\Filament\Resources\TeacherResource\RelationManagers;
use Modules\User\Models\User;

class TeacherResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Members';
    protected static ?string $breadcrumb = 'Members';
    protected static ?string $navigationLabel = 'Teachers';
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return UserResource::form($form)->schema(array_merge(
            UserResource::form($form)->getComponents(),
            [
                Select::make('subjects')
                    ->label('Subjects')
                    ->multiple()
                    ->relationship('subjects', 'title')
                    ->preload()
                    ->searchable(),
            ]
        ));
    }

    public static function table(Table $table): Table
    {
        return UserResource::table($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->role('teacher');
    }

    public static function getPages(): array
    {
        return [
            'index'  => TeacherResource\Pages\ListTeachers::route('/'),
            'create' => TeacherResource\Pages\CreateTeacher::route('/create'),
            'edit'   => TeacherResource\Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}

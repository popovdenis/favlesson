<?php

namespace Modules\Group\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Group\Filament\Resources\GroupResource\RelationManagers\StudyPlansRelationManager;
use Modules\Group\Models\Group;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;
    protected static ?string $navigationGroup = 'School';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Groups';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StudyPlansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => GroupResource\Pages\ListGroup::route('/'),
            'create' => GroupResource\Pages\CreateGroup::route('/create'),
            'edit'   => GroupResource\Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}

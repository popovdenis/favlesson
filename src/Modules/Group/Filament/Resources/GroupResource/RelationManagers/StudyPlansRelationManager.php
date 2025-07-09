<?php

namespace Modules\Group\Filament\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class StudyPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'studyPlans';
    protected static ?string $recordTitleAttribute = 'subject_id';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')->sortable(),
                Tables\Columns\TextColumn::make('subject.title')->label('Subject'),
                Tables\Columns\TextColumn::make('hours_per_year')->label('Hours per Year'),
            ])
            ->defaultSort('year', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'title')
                    ->required(),

                Forms\Components\Select::make('year')
                    ->label('Year')
                    ->options(
                        collect(range(now()->year, now()->year + 5))
                            ->mapWithKeys(fn ($year) => [$year => $year])
                            ->toArray()
                    )
                    ->default(now()->year)
                    ->required(),

                Forms\Components\TextInput::make('hours_per_year')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }
}

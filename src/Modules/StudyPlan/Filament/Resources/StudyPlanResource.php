<?php

namespace Modules\StudyPlan\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\StudyPlan\Models\StudyPlan;

class StudyPlanResource extends Resource
{
    protected static ?string $model = StudyPlan::class;
    protected static ?string $navigationGroup = 'Education';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $label = 'Study Plan';
    protected static ?string $navigationLabel = 'Study Plans';
    protected static ?string $pluralLabel = 'Study Plans';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('group_id')
                ->label('Group')
                ->relationship('group', 'title')
                ->required(),

            Forms\Components\Select::make('subject_id')
                ->label('Subject')
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

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('group.title')->label('Group')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('subject.title')->label('Subject')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('year')->sortable(),
            Tables\Columns\TextColumn::make('hours_per_year')->label('Hours/year'),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => StudyPlanResource\Pages\ListStudyPlans::route('/'),
            'create' => StudyPlanResource\Pages\CreateStudyPlan::route('/create'),
            'edit'   => StudyPlanResource\Pages\EditStudyPlan::route('/{record}/edit'),
        ];
    }
}

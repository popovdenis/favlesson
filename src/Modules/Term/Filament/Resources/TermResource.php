<?php

namespace Modules\Term\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Term\Models\Term;
use Modules\Term\Rules\NoTermOverlap;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;
    protected static ?string $navigationGroup = 'Education';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Terms';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),

            Forms\Components\DatePicker::make('start_date')
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->required()
                ->after('start_date')
                ->rule(fn ($get, $context) => new NoTermOverlap($context->model?->id ?? null)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
        ])
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
            'index'  => TermResource\Pages\ListTerms::route('/'),
            'create' => TermResource\Pages\CreateTerm::route('/create'),
            'edit'   => TermResource\Pages\EditTerm::route('/{record}/edit'),
        ];
    }
}

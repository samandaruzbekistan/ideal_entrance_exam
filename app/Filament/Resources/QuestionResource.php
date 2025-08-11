<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'All questions';
    protected static ?string $navigationGroup = 'Tests control';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('question')->label('Savol'),
                Forms\Components\FileUpload::make('image')->image(),
                Forms\Components\Select::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Sinf'),
                Forms\Components\Repeater::make('answers')
                    ->label('Javoblar')
                    ->relationship('answers')
                    ->schema([
                        Forms\Components\TextInput::make('answer')
                            ->label('Javob matni')
                            ->required(),
                        Forms\Components\Checkbox::make('is_correct')
                            ->label('To‘g‘ri javob')
                            ->default(false),
                    ])
                    ->minItems(2)
                    ->maxItems(6)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('question')->label('Savol')->limit(50),
                TextColumn::make('schoolClass.name')->label('Sinf'),
                TextColumn::make('correct_answer')
                    ->label('To‘g‘ri javob')
                    ->getStateUsing(function ($record) {
                        $correctAnswer = $record->answers()->where('is_correct', true)->first();
                        return $correctAnswer ? $correctAnswer->answer : '-';
                    }),
            ])
            ->filters([
                SelectFilter::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Sinf'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}

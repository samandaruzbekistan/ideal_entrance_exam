<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use App\Models\SchoolClass;
use App\Models\Question;
use Filament\Notifications\Notification;

class QuestionForm extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string $view = 'filament.pages.question-form';
    protected static ?string $navigationLabel = 'Add question';
    protected static ?string $navigationGroup = 'Tests control';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'school_class_id' => null,
            'questions' => '',
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Savol')
                ->schema([
                    Textarea::make('questions')
                        ->label('Questions text')
                        ->required(),

                    Select::make('school_class_id')
                        ->options(
                            SchoolClass::all()->mapWithKeys(function ($class) {
                                return [
                                    $class->id => $class->name . ' (' . $class->grade->name . ')'
                                ];
                            })
                        )
                        ->label('Classes')
                        ->searchable()
                        ->required(),

                ]),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $question = Question::create([
            'questions' => $data['questions'],
            'school_class_id' => $data['school_class_id'],
        ]);

        Notification::make()
            ->title('Savol muvaffaqiyatli qo‘shildi!')
            ->success()
            ->send();

        $this->form->fill([]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('data'),
        ];
    }
}

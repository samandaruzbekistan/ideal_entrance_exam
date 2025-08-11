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
                        ->label('LaTeX')
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

        $startWord = '\hline';
        $endWord = '\\\\ \hline \end{tabular}';

        $latex = str_replace(["\r", "\n"], '', $data['questions']);
        $latex = str_replace("{\dots}", '...', $latex);
        $latex = str_replace("\\textit{ }", '', $latex);
        $latex = preg_replace('/\\\\mathrm\{(.*?)\}/', '$1', $latex); // \mathrm{} ni olib tashlash
        $latex = preg_replace('/\\\\frac\{\\\\mathrm\{(.*?)\}\}\{\\\\mathrm\{(.*?)\}\}/', '\\frac{$1}{$2}', $latex); // \frac{}{}ni tozalash
        $latex = str_replace("\\textit{}", '', $latex);
        $latex = str_replace("\\textit{", '', $latex);
        $latex = str_replace("\\textbf{}", '', $latex);
        $latex = str_replace("\\textbf{ }", '', $latex);
        $latex = str_replace("\\textbf{", '', $latex);
        $latex = str_replace("\\_", '_', $latex);
        $latex = str_replace("\\%", '%', $latex);
        $latex = preg_replace('/\s*\\\\newline\s*/', '', $latex);

        $startPos = strpos($latex, $startWord);
        $endPos = strpos($latex, $endWord);

        $startPos += strlen($startWord); // Move to the end of the start word
        $extractedText = substr($latex, $startPos, $endPos - $startPos);
        $extractedText = str_replace("\newline","", trim($extractedText));
        $array = explode("\\\\ \hline",$extractedText);

        foreach ($array as $value) {
            $quiz_array = explode("&", $value);
            $question = Question::create([
                'question' => $quiz_array[0],
                'school_class_id' => $data['school_class_id'],
            ]);
            $question->answers()->create([
                'answer' => $quiz_array[1],
                'is_correct' => 1,
            ]);
            $question->answers()->create([
                'answer' => $quiz_array[2],
                'is_correct' => 0,
            ]);
            $question->answers()->create([
                'answer' => $quiz_array[3],
                'is_correct' => 0,
            ]);
            $question->answers()->create([
                'answer' => $quiz_array[4],
                'is_correct' => 0,
            ]);
        }

        Notification::make()
            ->title('Questions added successfully!')
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

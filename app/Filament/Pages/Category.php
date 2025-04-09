<?php

namespace App\Filament\Pages;

use App\Models\Category as ModelsCategory;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Category extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-square-3-stack-3d';
    protected static ?string $navigationGroup = 'Inventory Management';

    protected static string $view = 'filament.pages.category';

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')->live(true),
                    Textarea::make('description'),
                ]),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $form = $this->form($this->makeForm());

        ModelsCategory::create($form->getState());

        $form->fill();

        Notification::make()
            ->title('Category Created')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ModelsCategory::query())
            ->columns([
                TextColumn::make('name')
                    ->width('20%')
                    ->searchable(),
                TextColumn::make('description')
                    ->placeholder('No description.')
                    ->alignStart()
            ]);
    }
}

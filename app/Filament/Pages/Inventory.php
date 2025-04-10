<?php

namespace App\Filament\Pages;

use App\Filament\Imports\InventoryImporter;
use App\Filament\Widgets\InventoryTableWidget;
use App\Filament\Widgets\ProductCostingCardWidget;
use App\Filament\Widgets\ProductCostingTableWidget;
use App\Models\Ingredient;
use App\Models\Inventory as InventoryModel;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;

class Inventory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-tag';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?string $navigationLabel = "Raw Material";

    protected static string $view = 'filament.pages.inventory';
    protected static ?string $title = 'Inventory';

    protected static ?int $navigationSort = 2;

    protected function getHeaderWidgets(): array
    {
        return [
            InventoryTableWidget::class
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            ProductCostingCardWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): array|int|string
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(InventoryModel::class)
                ->label('Add Inventory')
                ->modalHeading('Add Inventory')
                ->action(function (array $data) {

                    // Ensure ingredient name is available
                    if (!isset($data['ingredient_name'])) {
                        throw new \Exception("Ingredient name is required.");
                    }

                    // Create or get the ingredient
                    $ingredient = Ingredient::firstOrCreate(
                        ['name' => $data['ingredient_name']],
                        [
                            'unit_of_measure' => $data['unit_of_measure'] ?? null,
                            'price' => $data['price'] ?? null
                        ],
                    );

                    $baseUnit = Ingredient::find($ingredient->id);

                    // Manually create the Inventory record
                    InventoryModel::create([
                        'ingredient_id'   => $ingredient->id,
                        'quantity'        => $data['quantity'],
                        'reorder_level'   => $data['reorder_level'],
                    ]);

                    Notification::make()
                        ->title('Raw Material Added')
                        ->success()
                        ->body('The raw material has been added successfully.')
                        ->send();

                    $this->dispatch('refreshTable');
                })
                ->form([
                    Section::make()->schema([
                        TextInput::make('ingredient_name')->label('Raw Material Name'),
                        TextInput::make('price')->label('Price'),
                        TextInput::make('unit_of_measure')->label('Unit of Measurement'),
                        TextInput::make('quantity')->label('Quantity'),
                        TextInput::make('reorder_level')->label('Reorder Level'),
                    ])->columns(2)
                ])
                ->createAnother(false),
            ImportAction::make('import')
                ->label('Import')
                ->importer(InventoryImporter::class)
        ];
    }
}

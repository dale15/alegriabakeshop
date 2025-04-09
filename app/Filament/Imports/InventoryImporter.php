<?php

namespace App\Filament\Imports;

use App\Models\Ingredient;
use App\Models\Inventory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class InventoryImporter extends Importer
{
    protected static ?string $model = Inventory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name'),
            ImportColumn::make('price'),
            ImportColumn::make('unit_of_measure')->label('Unit of Measure'),
            ImportColumn::make('quantity')->label('Quantity in Stock'),
            ImportColumn::make('reorder_level')->label('Reorder Level')
        ];
    }

    public function resolveRecord(): ?Inventory
    {
        if (isset($this->data['quantity'])) {
            $ingredient = Ingredient::firstOrCreate(
                ['name' => $this->data['name']],
                [
                    'quantity_in_stock' => $this->data['quantity'],
                    'unit_of_measure' => $this->data['unit_of_measure'] ?? 'pcs',
                    'price' => $this->data['price'],
                ],
            );
        }

        return Inventory::firstOrCreate(
            ['ingredient_id' => $ingredient->id],
            [
                'ingredient_id' => $ingredient->id,
                'quantity' => $this->data['quantity'],
                'reorder_level' => $this->data['reorder_level']
            ],
        );
    }

    public function fillRecord(): void
    {
        // Prevents auto-filling non-existent fields into Inventory
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your inventory import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}

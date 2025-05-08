<?php

namespace App\Filament\Imports;

use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name'),
            ImportColumn::make('sku')->label('SKU'),
            ImportColumn::make('category_name')->label('Category'),
            ImportColumn::make('cost_price'),
            ImportColumn::make('selling_price'),
        ];
    }

    public function resolveRecord(): ?Product
    {

        $category = Category::firstOrCreate([
            'name' => $this->data['category_name'],
        ], [
            'description' => $this->data['description'] ?? null,
        ]);

        return Product::firstOrNew([
            'name' => $this->data['name'],
        ], [
            'name' => $this->data['name'],
            'sku' => $this->data['sku'],
            'category_id' => $category->id,
            'selling_price' => $this->data['selling_price'],
            'cost_price' => $this->data['cost_price'],
            'is_box' => 0,
        ]);
    }

    public function fillRecord(): void
    {
        // Prevents auto-filling non-existent fields into Inventory
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}

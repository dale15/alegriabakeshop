<?php

namespace App\Filament\Widgets;

use App\Models\Inventory;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class InventoryTableWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Inventory::query())
            ->heading('')
            ->columns([
                TextColumn::make('ingredient.name')->label('Raw Material')->searchable()->sortable(),
                TextColumn::make('ingredient.price')->label('Price')->money('PHP'),
                TextColumn::make('quantity')
                    ->label('In Stock')
                    ->formatStateUsing(fn($record) => "{$record->quantity} {$record->ingredient->unit_of_measure}"),
                TextColumn::make(name: 'status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record): string => match (true) {
                        $record->quantity <= $record->reorder_level => 'danger', // Low stock
                        $record->quantity <= ($record->reorder_level * 1.5) => 'warning', // Getting low
                        default => 'success', // Sufficient stock
                    })
                    ->getStateUsing(fn($record) => match (true) {
                        $record->quantity <= $record->reorder_level => 'Low on Stock',
                        $record->quantity <= ($record->reorder_level * 1.5) => 'Running Low',
                        default => 'In Stock',
                    })
            ])
            ->defaultPaginationPageOption(5);
    }

    protected function getListeners(): array
    {
        return [
            'refreshTable' => '$refresh',
        ];
    }
}

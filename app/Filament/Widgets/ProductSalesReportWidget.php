<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\SaleItem;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProductSalesReportWidget extends BaseWidget
{
    public ?int $productId = null;


    public function getTableQuery(): Builder|Relation|null
    {
        return SaleItem::query()->with('sale');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->getTableQuery())
            ->columns([
                TextColumn::make('sale_id')->label('Sale ID'),
                TextColumn::make('product.name')->label('Product'),  // <-- product name here
                TextColumn::make('quantity')->label('Qty'),
                TextColumn::make('price')->label('Unit Price')->money('PHP'),
                TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(fn($record) => $record->price * $record->quantity)
                    ->money('PHP'),
                TextColumn::make('created_at')->label('Date')->date('Y-m-d'),
            ])
            ->filters([
                SelectFilter::make('id')
                    ->label('Select Product')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable(),
            ]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Filament\Exports\ProductReportsExporter;
use App\Models\SaleItem;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ProductSalesReportWidget extends BaseWidget
{
    public ?int $productId = null;

    public function getTableQuery(): Builder
    {
        return SaleItem::query()
            ->selectRaw('MIN(id) as id, product_id, 
                SUM(quantity) as total_quantity, 
                SUM(total) as total_sales, 
                SUM(total_cost_price) as total_cost,
                SUM(total) - SUM(cost_price) as gross_profit')
            ->with('product')
            ->groupBy('product_id')
            ->orderByRaw('MIN(id) ASC');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Products')
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export')
                    ->action(function () {
                        return Excel::download(new ProductReportsExporter, 'product-sales-report.xlsx');
                    })
            ])
            ->query(fn() => $this->getTableQuery())
            ->columns([
                TextColumn::make('product.name')->label('Product'),
                TextColumn::make('total_quantity')->label('Total Sold'),
                TextColumn::make('total_sales')->label('Net Sales')->money('PHP'),
                TextColumn::make('total_cost')->label('Cost of Goods')->money('PHP'),
                TextColumn::make('gross_profit')->label('Gross Profit')->money('PHP'),
            ]);
    }
}

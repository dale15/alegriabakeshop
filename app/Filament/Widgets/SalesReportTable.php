<?php

namespace App\Filament\Widgets;

use App\Exports\SaleItemsReportExport;
use App\Models\SaleItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportTable extends BaseWidget
{
    public ?bool $showTotalSales = true; // <--- store filter state here

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getGroupedSalesQuery())
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->default(now()),
                        DatePicker::make('created_at')
                            ->default(now()),
                    ])->columnSpan(2)->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('sale_items.created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_at'],
                                fn(Builder $query, $date): Builder => $query->whereDate('sale_items.created_at', '<=', $date),
                            );
                    }),

            ])
            ->filtersFormColumns(2)
            ->columns([
                TextColumn::make('sale_date')
                    ->label('Date')
                    ->date(),
                TextColumn::make('total_sales')
                    ->label('Gross Sales')
                    ->money('PHP'),
                TextColumn::make('cost_price')
                    ->label('Cost of Goods')
                    ->money('PHP'),
                TextColumn::make('total_discount')
                    ->label('Total Discount')
                    ->money('PHP'),
                TextColumn::make('gross_profit')
                    ->label('Net Sales')
                    ->money('PHP'),
            ])
            ->headerActions([
                Action::make('Export Sales')
                    ->button()
                    ->action(fn() => Excel::download(new SaleItemsReportExport, 'sale_items_report.xlsx'))
            ])
            ->defaultPaginationPageOption(5);
    }

    protected function getGroupedSalesQuery(): Builder
    {
        // Create a subquery as a fake Eloquent model
        return SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')  // join sales table
            ->selectRaw('DATE(sale_items.created_at) as sale_date, 
                SUM(sale_items.total) as total_sales,
                SUM(sale_items.total_cost_price) as cost_price,
                SUM(sales.total_discount) as total_discount,
                SUM(sale_items.total) - SUM(total_cost_price) - SUM(sales.total_discount) as gross_profit
                ')
            ->groupByRaw('DATE(sale_items.created_at)')
            ->orderByRaw('DATE(sale_items.created_at) DESC');
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->sale_date;
    }
}

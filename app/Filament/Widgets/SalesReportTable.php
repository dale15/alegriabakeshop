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
use Illuminate\Support\Facades\DB;

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
        $discountSubquery = DB::table('sales')
            ->selectRaw('DATE(created_at) as sale_date, SUM(total_discount) as total_discount')
            ->groupByRaw('DATE(created_at)');

        return SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->joinSub($discountSubquery, 'daily_discounts', function ($join) {
                $join->on(DB::raw('DATE(sale_items.created_at)'), '=', 'daily_discounts.sale_date');
            })
            ->selectRaw('
                DATE(sale_items.created_at) as sale_date,
                SUM(sale_items.total) as total_sales,
                SUM(sale_items.total_cost_price) as cost_price,
                daily_discounts.total_discount,
                SUM(sale_items.total) - SUM(sale_items.total_cost_price) - daily_discounts.total_discount as gross_profit
            ')
            ->groupByRaw('DATE(sale_items.created_at), daily_discounts.total_discount')
            ->orderByRaw('DATE(sale_items.created_at) DESC');
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->sale_date;
    }
}

<?php

namespace App\Exports;

use App\Models\SaleItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class SaleItemsReportExport implements FromQuery, WithHeadings, WithMapping
{

    public function query()
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
            ->orderByRaw('DATE(sale_items.created_at)');
    }

    /**
     * Map the data for each row in the export.
     */
    public function map($saleItem): array
    {
        return [
            $saleItem->sale_date,
            $saleItem->total_sales,
            $saleItem->cost_price,
            $saleItem->gross_profit,
        ];
    }

    /**
     * Return the column headings for the export.
     */
    public function headings(): array
    {
        return ['Sales Date', 'Gross Sales', 'Cost of Goods', 'Total Discount', 'Gross Profit'];
    }
}

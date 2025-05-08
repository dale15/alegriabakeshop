<?php

namespace App\Exports;

use App\Models\SaleItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SaleItemsReportExport implements FromQuery, WithHeadings, WithMapping
{

    public function query()
    {
        return SaleItem::query()
            ->selectRaw('DATE(created_at) as sale_date, 
                SUM(total) as total_sales,
                SUM(cost_price) as cost_price,
                SUM(total) - SUM(cost_price) as gross_profit')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)');
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
        return ['Sales Date', 'Gross Sales', 'Cost of Goods', 'Gross Profit'];
    }
}

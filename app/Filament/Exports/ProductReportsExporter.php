<?php

namespace App\Filament\Exports;

use App\Models\SaleItem;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class ProductReportsExporter implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return SaleItem::query()
            ->selectRaw('MIN(id) as id, product_id, 
                SUM(quantity) as total_quantity, 
                SUM(total) as total_sales, 
                SUM(total_cost_price) as total_cost,
                SUM(total) - SUM(total_cost_price) as gross_profit')
            ->with('product')
            ->groupBy('product_id')
            ->orderByRaw('MIN(id) ASC');
    }

    public function map($row): array
    {
        return [
            $row->product->name ?? 'N/A',
            $row->total_quantity,
            $row->total_sales,
            $row->total_cost,
            $row->gross_profit,
        ];
    }

    public function headings(): array
    {
        return [
            'Product',
            'Total Sold',
            'Net Sales',
            'Cost of Goods',
            'Gross Profit',
        ];
    }
}

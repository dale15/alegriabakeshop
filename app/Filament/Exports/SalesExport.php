<?php

namespace App\Filament\Exports;

use App\Models\SaleItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $saleItems = SaleItem::select('DATE(created_at) as sale_date')
            ->groupByRaw('DATE(created_at')
            ->get();

        return $saleItems;
    }

    public function headings(): array
    {
        return [
            'Sale Date',
            // Add your headings here
        ];
    }
}

<?php

namespace App\Filament\Exports;

use Filament\Tables\Actions\ExportAction;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportExporter extends ExportAction
{
    public function handle()
    {
        $query = $this->getQuery();

        // Get the grouped data from the query
        $data = $query->get();

        // Export the data as grouped
        return Excel::download(new SalesExport($data), 'grouped_sales_data.xlsx');
    }
}

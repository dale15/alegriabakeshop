<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class SalesReportOverview extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    protected static string $view = 'filament.pages.sales-report-overview';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}

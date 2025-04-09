<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\SalesPerProductChart;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class SalesOverview extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-bag';

    protected static string $view = 'filament.pages.sales-overview';

    public $selectedSaleId;
    public $saleItems = [];

    public function mount()
    {
        $this->selectedSaleId = null;
    }

    public function loadSaleItems($saleId)
    {
        $this->selectedSaleId = $saleId;
        $this->saleItems = SaleItem::where('sale_id', $saleId)->get();
    }

    protected function table(Table $table): Table
    {
        return $table
            ->query(Sale::query())
            ->columns([
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('PHP'),
                TextColumn::make('note')
                    ->label('Notes')
                    ->placeholder('No Notes.')
                    ->limit(20)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Sales Date')
            ])
            ->defaultPaginationPageOption(5)
            ->actions([
                Action::make('viewItems')
                    ->label('View')
                    ->icon('heroicon-s-eye')
                    ->action(fn(Sale $record) => $this->loadSaleItems($record->id))
            ], ActionsPosition::BeforeCells);
    }

    protected function getFooterWidgets(): array
    {
        return [
            SalesChart::class,
            SalesPerProductChart::class
        ];
    }

    public function getFooterWidgetsColumns(): array|int|string
    {
        return 2;
    }
}

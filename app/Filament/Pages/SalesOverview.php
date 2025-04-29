<?php

namespace App\Filament\Pages;

use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_at'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->filtersFormColumns(2)
            ->columns([
                TextColumn::make('sales_id'),
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

    // protected function getFooterWidgets(): array
    // {
    //     return [
    //         SalesChart::class,
    //         SalesPerProductChart::class
    //     ];
    // }

    public function getFooterWidgetsColumns(): array|int|string
    {
        return 2;
    }
}

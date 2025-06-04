<x-filament-panels::page>
    @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @livewire(\App\Filament\Widgets\SalesChart::class)
        @livewire(\App\Filament\Widgets\SalesPerProductChart::class)
    </div>

    <div class="mt-6">
        @livewire('sales-reports-overview')
    </div>
</x-filament-panels::page>
<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-3">
        <form wire:submit="create">
            <div class="space-y-2 mb-4">
                {{ $this->form }}
            </div>

            <x-filament::button type="submit">
                Submit
            </x-filament::button>
        </form>

        <div class="md:col-span-2">
            {{ $this->table }}
        </div>
    </div>

</x-filament-panels::page>
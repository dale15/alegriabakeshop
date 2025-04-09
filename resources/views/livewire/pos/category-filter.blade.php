<div class="relative" x-data="{ open: @entangle('open') }">
    <button id="category-dropdown"
        class="border rounded-lg px-4 py-2 flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
        x-on:click="open = !open">
        <span>{{ $this->selectedCategoryName }}</span>

        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-lg" x-show="open" @click.away="open = false"
        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95">

        <div class="py-1">
            <a wire:click="selectCategory()"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                All Categories
            </a>
            @foreach ($categories as $category)
                <a wire:click="selectCategory('{{ $category->id }}')"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
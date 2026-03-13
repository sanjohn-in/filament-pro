<x-filament::page>

<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">

@foreach($categories as $category)

<div 
    class="relative p-7 rounded-xl shadow text-white overflow-hidden "
    style="background-image: url('{{ asset('storage/' . $category->cover_image) }}'); 
           background-size: cover; 
           background-position: center;"
>

    <!-- dark overlay for readability -->
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative z-10">
        <h1 class="font-bold text-lg">
            {{ $category->type }}
        </h1>

        <p class="text-sm opacity-90">
            {{ $category->date }}
        </p>

        <x-filament::button
            wire:click="selectCategory({{ $category->id }})"
            class="mt-3 w-full"
        >
            {{ __('messages.open') }}
        </x-filament::button>
    </div>

</div>

@endforeach

</div>

</x-filament::page>
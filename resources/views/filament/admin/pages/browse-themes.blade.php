@php
    use App\Models\Admin\Theme;
    use App\Filament\Admin\Resources\Themes\ThemeResource;
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Category Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $this->mainCategory->bride_name }} & {{ $this->mainCategory->groom_name }}
            </h1>
            {{-- {{ $this->mainCategory }} --}}
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                {{ __('messages.choose_theme_description') }}
            </p>
        </div>

        <!-- Themes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($themes as $theme)
                <div class="group relative bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                    <!-- Image Container -->
                    <div class="relative w-full h-80 overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img 
                            src="{{ !empty($theme['image_url']) ? (str_contains($theme['image_url'], 'http') ? $theme['image_url'] : asset('storage/' . $theme['image_url'])) : 'https://via.placeholder.com/400x500?text=No+Image' }}"
                            alt="{{ $theme['name'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                        
                        <!-- Badge - Free or Price -->
                        <div class="absolute top-4 right-4">
                            @if($theme['is_free'])
                                <span class="inline-block bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                    {{ __('messages.free') }}
                                </span>
                            @else
                                <span class="inline-block bg-pink-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                    ${{ number_format($theme['price'], 2) }}
                                </span>
                            @endif
                        </div>

                        <!-- Selected Badge -->
                        @if($this->userSelectedTheme == $theme['id'])
                            <div class="absolute top-4 left-4">
                                <span class="inline-block bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                    ✓ {{ __('messages.selected') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <!-- Title -->
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $theme['name'] }}
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ Str::limit(strip_tags($theme['description'] ?? ''), 100) ?: 'No description available' }}
                        </p>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <!-- View Button -->
                            <a 
                               href="{{ url('events/' . $this->mainCategory->slug . '/template/' . $theme['id'])  }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex-1 inline-flex items-center justify-center bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-1.5 px-3 text-sm rounded transition-colors"
                            >
                         
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ __('messages.view') }}
                            </a>

                            @if(auth()->check() && auth()->user()->name === 'admin')

                                <a 
                                    href="{{ ThemeResource::getUrl('edit', ['record' => $theme['id']]) }}"
                                    class="flex-1 inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white font-semibold py-1.5 px-3 text-sm rounded transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    {{ __('Edit') }}
                                </a>
                            @endif

                            <!-- Select/Purchase Button -->
                            @php
                                $isPurchased = $theme['is_free'] || in_array($theme['id'], $this->userPurchases);
                                $isSelected = $this->userSelectedTheme == $theme['id'];
                            @endphp

                            @if($isPurchased)
                                @if($isSelected)
                                    <button 
                                        disabled
                                        class="flex-1 bg-blue-500 text-white font-semibold py-1.5 px-3 text-sm rounded cursor-not-allowed opacity-75"
                                    >
                                        ✓ {{ __('messages.selected') }}
                                    </button>
                                @else
                                    <button 
                                        wire:click="selectTheme({{ $theme['id'] }})"
                                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1.5 px-3 text-sm rounded transition-colors"
                                    >
                                        {{ __('messages.select') }}
                                    </button>
                                @endif
                            @else
                                <button 
                                    wire:click="purchaseTheme({{ $theme['id'] }})"
                                    class="flex-1 bg-pink-500 hover:bg-pink-600 text-white font-semibold py-1.5 px-3 text-sm rounded transition-colors flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ __('messages.buy') }} - ${{ number_format($theme['price'], 2) }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 text-lg">
                        {{ __('messages.no_themes_available') }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Livewire Notification Script -->
    <script>
        Livewire.on('notify', (payload) => {
            const data = Array.isArray(payload) ? payload[0] : payload;
            const { type, message } = data || {};
            
            // You can use your preferred notification library here
            // Example: Toastr, SweetAlert, etc.
            console.log(`[${type}] ${message}`);
            
            // Simple alert for demo
            if (type === 'success') {
                alert(message);
            } else if (type === 'error') {
                alert(message);
            }
        });
    </script>

    <x-filament-actions::modals />
</x-filament-panels::page>
`
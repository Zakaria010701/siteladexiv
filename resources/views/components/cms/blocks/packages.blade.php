<div class="cms-block py-4">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $content['category_id'] ? \App\Models\Category::find($content['category_id'])->name : 'Pakete' }} Pakete</h2>
            @if($content['gender'] === 'female' || $content['gender'] === 'male')
                <p class="text-sm text-gray-600">({{ $content['gender'] === 'female' ? 'Damen' : 'Herren' }})</p>
            @endif
        </div>
        @php
            $cart = session('cart', []);
            $category_id = $content['category_id'] ?? 1; // Default to category 1 if not set
            $gender = $content['gender'] ?? 'all';
            $category = \App\Models\Category::find($category_id);
            $allPackages = collect();

            if ($category) {
                $allPackages = $category->packages()->withTrashed()->get();
            }

            // Debug information
            $debug = [
                'category_id' => $category_id,
                'category_name' => $category ? $category->name : 'Not found',
                'packages_count' => $allPackages->count(),
                'packages' => $allPackages->pluck('name')->toArray()
            ];

            // Pagination logic - show first 8 packages by default
            $initialLimit = 8;
            $packages = $allPackages->take($initialLimit);
            $showLoadMore = $allPackages->count() > $initialLimit;

        @endphp

        {{-- Debug Output --}}
        @if(config('app.debug'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                <h3 class="font-bold">Debug Info:</h3>
                <pre>{{ json_encode($debug, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif


        @if(isset($content['category_id']))
            @if($allPackages->count() > 0)
                <!-- Filter and Search Section -->

                    </div>
                </div>
                <!-- Professional Packages Table -->
                <div class="bg-white border-2 border-blue-600 rounded-lg overflow-hidden shadow-lg mx-auto max-w-4xl" style="border: 2px solid #2563eb !important; margin: 0 auto; padding: 0; width: 100%; display: block;">
                    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-blue-600 scrollbar-track-blue-100" style="padding: 0; -webkit-overflow-scrolling: touch;">
                        <table class="w-full min-w-full mx-auto" style="margin: 0 auto; min-width: 500px; max-width: 900px;">
                            <thead>
                                <tr class="bg-blue-50">
                                    <th class="px-2 py-3 text-center text-sm font-bold text-blue-900 min-w-20 sm:min-w-24 border-r-2 border-blue-600">
                                        Paket Name
                                    </th>
                                    <th class="px-1 py-3 text-center text-xs sm:text-sm font-semibold text-blue-900 min-w-12 sm:min-w-16 border-r-2 border-blue-600">
                                        <span class="text-xs sm:text-sm">Anzahl</span>
                                    </th>
                                    <th class="px-1 py-3 text-center text-xs font-semibold text-blue-900 border-r-2 border-blue-600" style="max-width: 120px;">
                                        <span class="hidden sm:inline">Preis pro Paket</span>
                                        <span class="sm:hidden">Paketpreis</span>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-semibold text-blue-900 min-w-12 sm:min-w-16 border-l-2 border-blue-600">
                                        Kaufen
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allPackages as $package)
                                    <tr class="hover:bg-blue-25 transition-colors" style="border-bottom: 1px solid #2563eb !important;">
                                        <td class="px-1 sm:px-2 py-3 align-top border-r-2 border-blue-600" style="max-width: 100px; min-width: 60px;">
                                            <div class="text-xs font-medium text-gray-900 break-words">{{ $package->name }}</div>
                                            @if($package->services->count() > 0)
                                                <div class="text-xs text-gray-500 mt-1 break-words">
                                                    @foreach($package->services as $service)
                                                        • {{ $service->name }}<br>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-1 py-3 text-center align-top border-r-2 border-blue-600" style="min-width: 40px;">
                                            <div class="text-xs font-semibold text-gray-900">{{ $package->services->count() }}</div>
                                        </td>
                                        <td class="px-1 py-3 text-center align-top border-r-2 border-blue-600" style="min-width: 40px;">
                                            <div class="text-xs font-semibold text-gray-900">{{ number_format($package->price, 0, ',', '.') }}€</div>
                                        </td>
                                        <td class="px-1 sm:px-2 py-3 text-center align-top" style="background-color: #f8f9fa; border: 1px solid #2563eb; min-width: 40px;">
                                            <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="item_type" value="package">
                                                <input type="hidden" name="item_id" value="{{ $package->id }}">
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-2 sm:px-4 py-2 sm:py-3 rounded-md transition-colors border border-blue-600 hover:border-blue-700 flex items-center justify-center w-full" style="background-color: #2563eb !important; color: white !important; display: flex !important; visibility: visible !important; min-height: 36px;">
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" style="display: block !important;">
                                                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM4 4a1 1 0 011-1h12.652a.5.5 0 01.489.395l2.5 10A.5.5 0 0120 14v1a1 1 0 01-1 1H4a1 1 0 010-2h12.764l-2.333-9.333H5a1 1 0 01-1-1z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Cart Overview (shown on all screen sizes) -->
                <div class="mt-6 bg-white border-2 border-blue-600 p-4 mx-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM4 4a1 1 0 011-1h12.652a.5.5 0 01.489.395l2.5 10A.5.5 0 0120 14v1a1 1 0 01-1 1H4a1 1 0 010-2h12.764l-2.333-9.333H5a1 1 0 01-1-1z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Warenkorb</h3>
                    </div>

                    <div id="cart-items" class="space-y-2 mb-3">
                        <div class="text-sm text-gray-500 italic">Noch nichts ausgewählt</div>
                    </div>

                    <div class="border-t-2 border-blue-600 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-medium text-gray-700">Gesamt:</span>
                            <span id="cart-total" class="text-base font-bold text-green-600">0,00 €</span>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-center text-gray-500">Keine Pakete verfügbar in dieser Kategorie.</p>
            @endif
        @else
            <p class="text-center text-gray-500">Bitte wählen Sie eine Kategorie aus.</p>
        @endif
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 PACKAGES PAGE LOADED - Initializing...');

    // Initialize cart overview on page load
    updateCartOverview();

    function updateCartOverview() {
        @if(session('cart') && count(session('cart')) > 0)
            fetch('{{ route('cart.details') }}')
            .then(response => response.json())
            .then(data => {
                if (data.items && data.items.length > 0) {
                    displayCartItems(data.items, data.total);
                } else {
                    displayEmptyCart();
                }
            })
            .catch(error => {
                console.error('Error loading cart:', error);
                displayEmptyCart();
            });
        @else
            displayEmptyCart();
        @endif
    }

    function displayCartItems(items, total) {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');

        let itemsHtml = '';
        items.forEach(item => {
            itemsHtml += `
                <div class="flex justify-between items-center py-0.5 border-b border-blue-300 last:border-b-0">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 text-xs truncate">${item.name}</div>
                        <div class="text-xs text-gray-500">${item.type}</div>
                    </div>
                    <div class="text-right ml-1 flex-shrink-0">
                        <div class="font-semibold text-green-600 text-xs">${parseFloat(item.price).toFixed(2)} €</div>
                        <button onclick="removeFromCart('${item.cart_key}')" class="text-red-500 hover:text-red-700 text-xs">
                            Entfernen
                        </button>
                    </div>
                </div>
            `;
        });

        cartItemsContainer.innerHTML = itemsHtml;
        cartTotalElement.textContent = parseFloat(total).toFixed(2) + ' €';
    }

    function displayEmptyCart() {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');

        cartItemsContainer.innerHTML = '<div class="text-gray-500 italic">Noch nichts ausgewählt</div>';
        cartTotalElement.textContent = '0,00 €';
    }

    // Make remove function global
    window.removeFromCart = function(cartKey) {
        // Extract item info from cart key
        const parts = cartKey.split('_');
        const itemType = parts[0];
        const itemId = parts[1];

        fetch('{{ route("cart.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                item_type: itemType,
                item_id: itemId,
                package_type: parts.length > 2 ? parts[2] : null
            })
        }).then(() => {
            updateCartOverview();
            location.reload(); // Refresh to sync with session
        }).catch(error => {
            console.error('Error removing item:', error);
        });
    };
});
</script>
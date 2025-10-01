<div class="cms-block py-4">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $content['category_id'] ? \App\Models\Category::find($content['category_id'])->name : 'Preise' }} Preise</h2>
            @if($content['gender'] === 'female' || $content['gender'] === 'male')
                <p class="text-sm text-gray-600">({{ $content['gender'] === 'female' ? 'Damen' : 'Herren' }})</p>
            @endif
        </div>
        @php
            $cart = session('cart', []);
            $category_id = $content['category_id'] ?? 1; // Default to category 1 if not set
            $category = \App\Models\Category::find($category_id);
            $services = $category ? $category->services()->get() : collect();

            // Debug info (remove this later)
            $debug_info = [
                'category_id' => $category_id,
                'category_found' => $category ? true : false,
                'services_count' => $services->count(),
                'available_categories' => \App\Models\Category::pluck('name', 'id')->toArray()
            ];
        @endphp

        @if(isset($content['category_id']))
            @if($services->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block bg-white border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 w-12">
                                        <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">
                                        Behandlung
                                    </th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500" style="min-width: 320px;">
                                        Preise & Aktionen
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($services as $service)
                                    @php $originalSixPackPrice = $service->price * 6; @endphp
                                    @php $sixPackPrice = round($originalSixPackPrice * 0.92, 0); @endphp
                                    <tr class="hover:bg-gray-50" style="position: relative;">
                                        <td class="px-4 py-4 text-center">
                                            <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                                            @if($service->description)
                                                <div class="text-sm text-gray-600 mt-1">{{ $service->description }}</div>
                                            @endif
                                            <div class="text-xs text-gray-500 mt-1">{{ $service->short_code }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2" style="min-width: 280px; display: flex !important; visibility: visible !important;">
                                                {{-- Normalpreis Button --}}
                                                @if(in_array('service_' . $service->id, $cart))
                                                    <span class="inline-flex items-center px-3 py-2 rounded-md text-sm bg-green-100 text-green-800">
                                                        Im Warenkorb
                                                    </span>
                                                    <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="item_type" value="service">
                                                        <input type="hidden" name="item_id" value="{{ $service->id }}">
                                                        <button type="submit" class="inline-flex items-center px-3 py-2 rounded-md text-sm text-red-700 bg-red-50 hover:bg-red-100">
                                                            Entfernen
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="item_type" value="service">
                                                        <input type="hidden" name="item_id" value="{{ $service->id }}">
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600">
                                                            Normalpreis {{ $service->price }} €
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Paketpreis Button --}}
                                                @if(in_array('package_' . $service->id . '_6x', $cart))
                                                    <span class="inline-flex items-center px-3 py-2 rounded-md text-sm bg-blue-100 text-blue-800">
                                                        Paket im Warenkorb
                                                    </span>
                                                    <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="item_type" value="package">
                                                        <input type="hidden" name="item_id" value="{{ $service->id }}">
                                                        <input type="hidden" name="package_type" value="6x">
                                                        <input type="hidden" name="package_price" value="{{ $sixPackPrice }}">
                                                        <button type="submit" class="inline-flex items-center px-3 py-2 rounded-md text-sm text-red-700 bg-red-50 hover:bg-red-100">
                                                            Paket entfernen
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="item_type" value="package">
                                                        <input type="hidden" name="item_id" value="{{ $service->id }}">
                                                        <input type="hidden" name="package_type" value="6x">
                                                        <input type="hidden" name="package_price" value="{{ $sixPackPrice }}">
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 border border-gray-600">
                                                            Paketpreis {{ $sixPackPrice }} €
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-3">
                    @foreach($services as $service)
                        @php $originalSixPackPrice = $service->price * 6; @endphp
                        @php $sixPackPrice = round($originalSixPackPrice * 0.92, 0); @endphp

                        <div class="bg-white border border-gray-200 mx-4 rounded-lg overflow-hidden">
                            <div class="p-4">
                                <div class="flex items-start space-x-3">
                                    <input type="checkbox" class="w-4 h-4 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-medium text-gray-900">{{ $service->name }}</h3>
                                        @if($service->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $service->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">{{ $service->short_code }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-center space-x-2" style="gap: 8px;">
                                    {{-- Always show both buttons --}}
                                    <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="item_type" value="service">
                                        <input type="hidden" name="item_id" value="{{ $service->id }}">
                                        <button type="submit" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700" style="display: inline-flex !important; visibility: visible !important;">
                                            Normalpreis {{ $service->price }} €
                                        </button>
                                    </form>

                                    <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="item_type" value="package">
                                        <input type="hidden" name="item_id" value="{{ $service->id }}">
                                        <input type="hidden" name="package_type" value="6x">
                                        <input type="hidden" name="package_price" value="{{ $sixPackPrice }}">
                                        <button type="submit" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-white bg-gray-600 hover:bg-gray-700" style="display: inline-flex !important; visibility: visible !important;">
                                            Paketpreis {{ $sixPackPrice }} €
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Overview (shown on all screen sizes) -->
                <div class="mt-6 bg-white border border-gray-200 p-4 mx-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM4 4a1 1 0 011-1h12.652a.5.5 0 01.489.395l2.5 10A.5.5 0 0120 14v1a1 1 0 01-1 1H4a1 1 0 010-2h12.764l-2.333-9.333H5a1 1 0 01-1-1z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Warenkorb</h3>
                    </div>

                    <div id="cart-items" class="space-y-2 mb-3">
                        <div class="text-sm text-gray-500 italic">Noch nichts ausgewählt</div>
                    </div>

                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-medium text-gray-700">Gesamt:</span>
                            <span id="cart-total" class="text-base font-bold text-green-600">0,00 €</span>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-center text-gray-500">No services available in this category.</p>
            @endif
        @else
            <p class="text-center text-gray-500">Please select a category.</p>
        @endif
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
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
                <div class="flex justify-between items-center py-0.5 border-b border-gray-100 last:border-b-0">
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
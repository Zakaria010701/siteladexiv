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
            $allServices = $category ? $category->services()->get() : collect();


            // Pagination logic - show first 8 services by default
            $initialLimit = 8;
            $services = $allServices->take($initialLimit);
            $showLoadMore = $allServices->count() > $initialLimit;

        @endphp

        @if(isset($content['category_id']))
            @if($allServices->count() > 0)
                <!-- Filter and Search Section -->

                    </div>
                </div>
                <!-- Professional Pricing Table -->
                <div class="bg-white border-2 border-blue-600 rounded-lg overflow-hidden shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-blue-50">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-blue-900 min-w-48 border-r border-blue-300">
                                        Name
                                    </th>
                                    <th class="px-4 py-4 text-center text-sm font-semibold text-blue-900 min-w-24 border-r border-blue-300">
                                        1<br>Behandlung
                                    </th>
                                    <th colspan="3" class="px-4 py-4 text-center text-sm font-semibold text-blue-900 border-r border-blue-300">
                                        Preis pro Behandlung beim Kauf von Paketen
                                    </th>
                                    <th class="px-4 py-4 text-center text-sm font-semibold text-blue-900 min-w-32">
                                        Kaufen
                                    </th>
                                </tr>
                                <tr class="bg-blue-25 border-b-2 border-blue-600">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 border-r border-blue-300">
                                        &nbsp;
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-blue-700 border-r border-blue-300">
                                        &nbsp;
                                    </th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-blue-900 border-r border-blue-300">
                                        3
                                    </th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-blue-900 border-r border-blue-300">
                                        6
                                    </th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-blue-900 border-r border-blue-300">
                                        8
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-blue-700">
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-blue-200">
                                @foreach($allServices as $service)
                                    @php
                                        $singlePrice = $service->price;
                                        $package3Price = round($singlePrice * 3 * 0.95, 0); // 5% discount
                                        $package6Price = round($singlePrice * 6 * 0.92, 0); // 8% discount
                                        $package8Price = round($singlePrice * 8 * 0.90, 0); // 10% discount
                                    @endphp
                                    <tr class="hover:bg-blue-25 transition-colors">
                                        <td class="px-6 py-4 align-top border-r border-blue-300">
                                            <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                                            @if($service->description)
                                                <div class="text-xs text-gray-600 mt-1">{{ $service->description }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center align-top border-r border-blue-300">
                                            <div class="text-sm font-semibold text-gray-900">{{ number_format($singlePrice, 0, ',', '.') }}€</div>
                                        </td>
                                        <td class="px-4 py-4 text-center align-top border-r border-blue-300">
                                            <div class="text-sm font-semibold text-green-700">{{ number_format($package3Price, 0, ',', '.') }}€</div>
                                            <div class="text-xs text-gray-500">{{ number_format($singlePrice * 3, 0, ',', '.') }}€</div>
                                        </td>
                                        <td class="px-4 py-4 text-center align-top border-r border-blue-300">
                                            <div class="text-sm font-semibold text-green-700">{{ number_format($package6Price, 0, ',', '.') }}€</div>
                                            <div class="text-xs text-gray-500">{{ number_format($singlePrice * 6, 0, ',', '.') }}€</div>
                                        </td>
                                        <td class="px-4 py-4 text-center align-top border-r border-blue-300">
                                            <div class="text-sm font-semibold text-green-700">{{ number_format($package8Price, 0, ',', '.') }}€</div>
                                            <div class="text-xs text-gray-500">{{ number_format($singlePrice * 8, 0, ',', '.') }}€</div>
                                        </td>
                                        <td class="px-4 py-4 text-center align-top">
                                            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors border border-blue-600 hover:border-blue-700">
                                                Kaufen
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-4">
                    @foreach($allServices as $service)
                        @php
                            $singlePrice = $service->price;
                            $package3Price = round($singlePrice * 3 * 0.95, 0); // 5% discount
                            $package6Price = round($singlePrice * 6 * 0.92, 0); // 8% discount
                            $package8Price = round($singlePrice * 8 * 0.90, 0); // 10% discount
                        @endphp
                        <div class="bg-white border-2 border-blue-600 rounded-lg p-4 shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                            @if($service->description)
                                <p class="text-sm text-gray-600 mb-4">{{ $service->description }}</p>
                            @endif

                            <!-- Header for mobile -->
                            <div class="mb-3">
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="text-center p-2 bg-gray-50 rounded border">
                                        <div class="font-medium text-gray-900">1 Behandlung</div>
                                        <div class="font-semibold text-gray-900">{{ number_format($singlePrice, 0, ',', '.') }}€</div>
                                    </div>
                                    <div class="text-center"></div>
                                </div>
                                <div class="bg-blue-50 border border-blue-300 rounded p-2 mt-2 text-center">
                                    <div class="text-sm font-semibold text-blue-900 mb-2">Preis pro Behandlung beim Kauf von Paketen</div>
                                    <div class="flex justify-center space-x-8">
                                        <div class="font-semibold text-blue-900">3</div>
                                        <div class="font-semibold text-blue-900">6</div>
                                        <div class="font-semibold text-blue-900">8</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-sm mt-2">
                                    <div class="text-center p-2 bg-green-50 rounded border">
                                        <div class="font-medium text-green-900 text-xs">3 Paket</div>
                                        <div class="font-semibold text-green-700">{{ number_format($package3Price, 0, ',', '.') }}€</div>
                                        <div class="text-xs text-gray-500">{{ number_format($singlePrice * 3, 0, ',', '.') }}€</div>
                                    </div>
                                    <div class="text-center p-2 bg-green-50 rounded border">
                                        <div class="font-medium text-green-900 text-xs">6 Paket</div>
                                        <div class="font-semibold text-green-700">{{ number_format($package6Price, 0, ',', '.') }}€</div>
                                        <div class="text-xs text-gray-500">{{ number_format($singlePrice * 6, 0, ',', '.') }}€</div>
                                    </div>
                                    <div class="text-center p-2 bg-green-50 rounded border">
                                        <div class="font-medium text-green-900 text-xs">8 Paket</div>
                                        <div class="font-semibold text-green-700">{{ number_format($package8Price, 0, ',', '.') }}€</div>
                                        <div class="text-xs text-gray-500">{{ number_format($singlePrice * 8, 0, ',', '.') }}€</div>
                                    </div>
                                </div>
                            </div>

                                <div class="text-center pt-2">
                                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-md transition-colors border border-blue-600 hover:border-blue-700">
                                        Kaufen
                                    </button>
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
    console.log('🚀 SHOP PAGE LOADED - Initializing...');

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

    // Pagination Functionality
    let currentLimit = {{ $initialLimit }};

    // Initialize search and filter on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📋 DOM CONTENT LOADED - Starting initialization...');

        // Show initial 8 services
        console.log('📋 Showing initial 8 services on page load');
        setServiceVisibility(8);

        try {
            initializePagination();
            console.log('✅ Pagination initialized successfully');
        } catch (error) {
            console.error('❌ Error initializing pagination:', error);
        }

        try {
            updateCartOverview();
            console.log('✅ Cart overview initialized successfully');
        } catch (error) {
            console.error('❌ Error initializing cart overview:', error);
        }
    });

    function initializePagination() {
        try {
            console.log('🔧 INITIALIZING PAGINATION');

            // Items per page dropdown
            const itemsPerPageSelect = document.getElementById('items-per-page');

            if (itemsPerPageSelect) {
                console.log('✅ Items per page dropdown found');
                itemsPerPageSelect.addEventListener('change', function() {
                    const newLimit = parseInt(this.value);
                    setPaginationLimit(newLimit);
                });
                console.log('✅ Pagination event listener attached');
            } else {
                console.error('❌ Items per page dropdown not found!');
            }

            console.log('✅ Pagination setup complete');
        } catch (error) {
            console.error('❌ Error in initializePagination:', error);
        }
    }

    function setPaginationLimit(newLimit) {
        console.log('🔄 PAGINATION CHANGED TO:', newLimit);
        currentLimit = newLimit;
        setServiceVisibility(currentLimit);
    }

    function setServiceVisibility(limit) {
        console.log(`🔧 SETTING VISIBILITY TO: ${limit}`);

        let shownCount = 0;
        document.querySelectorAll('.service-row').forEach((row, index) => {
            if (shownCount < limit) {
                row.style.display = 'table-row';
                shownCount++;
            } else {
                row.style.display = 'none';
            }
        });

        shownCount = 0;
        document.querySelectorAll('.service-card').forEach((card, index) => {
            if (shownCount < limit) {
                card.classList.remove('hidden');
                shownCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        console.log(`✅ Visibility set to ${limit} services`);
    }

    function updateServicesDisplay() {
        console.log('🟡 UPDATE SERVICES DISPLAY CALLED');
        console.log('Current limit:', currentLimit);
        console.log('Filtered services:', filteredServices.length);

        try {
            // Force show all services if "Alle" is selected
            if (currentLimit >= 47) {
                console.log('🔴 SHOWING ALL SERVICES (Alle selected)');
                setServiceVisibility(999); // Show all
                console.log('🔴 ALL SERVICES SHOULD NOW BE VISIBLE');
                return;
            }

            // Show only services up to current limit
            console.log(`🟡 Showing up to ${currentLimit} services`);
            setServiceVisibility(currentLimit);
            console.log(`🟡 Display update complete`);
        } catch (error) {
            console.error('❌ Error in updateServicesDisplay:', error);
        }
    }
});
</script>
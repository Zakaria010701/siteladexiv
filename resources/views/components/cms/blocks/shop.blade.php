<div class="cms-block py-4">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">
                {{ $content['category_id'] ? \App\Models\Category::find($content['category_id'])->name : 'Preise' }}
                Preise
            </h2>
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
<div class="bg-white rounded-lg overflow-hidden shadow-lg mx-auto max-w-4xl"
 style="border: 2px solid #138cc8 !important; margin: 0 auto; padding: 0; width: 100%; display: block;">
    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-blue-600 scrollbar-track-blue-100"
         style="padding: 0; -webkit-overflow-scrolling: touch;">
        <table class="w-full min-w-full mx-auto"
               style="margin: 0 auto; min-width: 500px; max-width: 900px; border-collapse: collapse; border: 2px solid #138cc8 !important;">
            <thead>
                <tr style="background-color: #f2f2f2; border-bottom: 2px solid #138cc8 !important;">
                    <th rowspan="2" 
                        class="px-2 py-3 text-center text-sm font-bold text-blue-900 align-middle"
                        style="border-right: 2px solid #138cc8 !important;">
                        Name
                    </th>
                    <th rowspan="2" 
                        class="px-2 py-3 text-center text-sm font-bold text-blue-900 align-middle"
                        style="border-right: 2px solid #138cc8 !important;">
                        1<br>Behandlung
                    </th>
                    <th colspan="3" 
                        class="px-2 py-3 text-center text-sm font-bold text-blue-900"
                        style="border-left: 2px solid #138cc8 !important; border-right: 2px solid #138cc8 !important; border-bottom: 2px solid #138cc8 !important;">
                        Preis pro Behandlung beim Kauf von Paketen
                    </th>
                    <th rowspan="2" 
                        class="px-2 py-3 text-center text-sm font-bold text-blue-900 align-middle"
                        style="border-left: 2px solid #138cc8 !important;">
                        
                    </th>
                </tr>
                <tr style="background-color: #f2f2f2; border-bottom: 2px solid #138cc8 !important;">
                    <th class="px-2 py-2 text-center text-sm font-bold text-blue-900"
                        style="border-right: 2px solid #138cc8 !important;">3</th>
                    <th class="px-2 py-2 text-center text-sm font-bold text-blue-900"
                        style="border-right: 2px solid #138cc8 !important;">6</th>
                    <th class="px-2 py-2 text-center text-sm font-bold text-blue-900"
                        style="border-right: 2px solid #138cc8 !important;">8</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allServices as $service)
                    @php
                        $singlePrice = $service->price;
                        $package3Price = round($singlePrice * 0.95, 0); // 5% discount per treatment for 3-pack
                        $package6Price = round($singlePrice * 0.92, 0); // 8% discount per treatment for 6-pack
                        $package8Price = round($singlePrice * 0.90, 0); // 10% discount per treatment for 8-pack
                    @endphp
                    <tr class="hover:bg-blue-25 transition-colors"
                            style="border-bottom: 1px solid #138cc8 !important;">
                        <td class="px-1 sm:px-2 py-3 align-top" style="border-right: 2px solid #138cc8 !important; max-width: 100px; min-width: 60px;">
                            <div class="text-xs font-medium text-gray-900 break-words">{{ $service->name }}</div>
                            @if($service->description)
                                <div class="text-xs text-gray-500 mt-1 break-words">{{ $service->description }}</div>
                            @endif
                        </td>
                        <td class="px-1 py-3 text-center align-top" style="border-right: 2px solid #138cc8 !important; min-width: 40px;">
                            <div class="text-xs font-semibold text-gray-900 cursor-pointer hover:bg-blue-50 transition-colors rounded p-1" onclick="showPriceModal('{{ $service->name }}', '{{ $service->description }}', '{{ number_format($singlePrice, 0, ',', '.') }}', '1', '{{ $service->id }}')">{{ number_format($singlePrice, 0, ',', '.') }}€</div>
                        </td>
                        <td class="px-1 py-3 text-center align-top" style="border-right: 2px solid #138cc8 !important; min-width: 40px;">
                            <div class="text-xs font-semibold text-gray-900 cursor-pointer hover:bg-blue-50 transition-colors rounded p-1" onclick="showPriceModal('{{ $service->name }}', '{{ $service->description }}', '{{ number_format($package3Price, 0, ',', '.') }}', '3', '{{ $service->id }}')">{{ number_format($package3Price, 0, ',', '.') }}€</div>
                        </td>
                        <td class="px-1 py-3 text-center align-top" style="border-right: 2px solid #138cc8 !important; min-width: 40px;">
                            <div class="text-xs font-semibold text-gray-900 cursor-pointer hover:bg-blue-50 transition-colors rounded p-1" onclick="showPriceModal('{{ $service->name }}', '{{ $service->description }}', '{{ number_format($package6Price, 0, ',', '.') }}', '6', '{{ $service->id }}')">{{ number_format($package6Price, 0, ',', '.') }}€</div>
                        </td>
                        <td class="px-1 py-3 text-center align-top" style="border-right: 2px solid #138cc8 !important; min-width: 40px;">
                            <div class="text-xs font-semibold text-gray-900 cursor-pointer hover:bg-blue-50 transition-colors rounded p-1" onclick="showPriceModal('{{ $service->name }}', '{{ $service->description }}', '{{ number_format($package8Price, 0, ',', '.') }}', '8', '{{ $service->id }}')">{{ number_format($package8Price, 0, ',', '.') }}€</div>
                        </td>
                        <td class="px-1 sm:px-2 py-3 text-center align-top"
                            style="background-color: #f8f9fa; border: 1px solid #138cc8; min-width: 40px;">
                            <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="item_type" value="service">
                                <input type="hidden" name="item_id" value="{{ $service->id }}">
                                <button type="button"
                                        onclick="showCartInstructionModal()"
                                        class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-2 sm:px-4 py-2 sm:py-3 rounded-md transition-colors border border-amber-600 hover:border-amber-700 flex items-center justify-center w-full"
                                        style="background-color: #138cc8 !important; color: white !important; display: flex !important; min-height: 36px; border-color: #138cc8 !important;">
                                    <img src="/images/shopping-cart.png" alt="Warenkorb" class="w-5 h-5" style="display: block !important; filter: brightness(0) invert(1);">
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
                        <div id="cart-overview" class="mt-6 bg-white border-2 border-blue-600 p-4 mx-4 rounded-lg hidden" style="border-color: #138cc8 !important;">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <img src="/images/shopping-cart.png" alt="Warenkorb" class="w-5 h-5 mr-2" style="display: block !important; filter: brightness(0) invert(1);">
                                    <h3 class="text-lg font-medium text-gray-900">Warenkorb</h3>
                                </div>
                                <button onclick="hideCartOverview()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div id="cart-items" class="space-y-2 mb-3">
                                <div class="text-sm text-gray-500 italic">Noch nichts ausgewählt</div>
                            </div>

                            <div class="border-t-2 pt-3" style="border-color: #138cc8 !important;">
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
    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 SHOP PAGE LOADED - Initializing...');

        // Initialize cart overview on page load




        // Pagination Functionality
        let currentLimit = {{ $initialLimit }};

        // Initialize search and filter on page load
        document.addEventListener('DOMContentLoaded', function () {
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
                    itemsPerPageSelect.addEventListener('change', function () {
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

    // Modal functions for shopping cart instruction
    function showCartInstructionModal() {
        document.getElementById('cart-instruction-modal').classList.remove('hidden');
    }

    function hideCartInstructionModal() {
        document.getElementById('cart-instruction-modal').classList.add('hidden');
    }

    // Modal functions for price selection
    function showPriceModal(serviceName, serviceDescription, price, packageCount, serviceId) {
        document.getElementById('price-modal').classList.remove('hidden');
        document.getElementById('modal-service-name').textContent = serviceName;
        document.getElementById('modal-service-description').textContent = serviceDescription || '';
        document.getElementById('modal-price').textContent = price + ' €';
        document.getElementById('modal-package-count').textContent = packageCount;

        // Calculate and show total price
        const totalPrice = parseFloat(price) * parseInt(packageCount);
        document.getElementById('modal-total-price').textContent = totalPrice.toFixed(0) + ' €';

        // Store the service info for adding to cart
        window.selectedService = {
            id: serviceId,
            name: serviceName,
            price: price,
            packageCount: packageCount
        };
    }

    function hidePriceModal() {
        document.getElementById('price-modal').classList.add('hidden');
    }

    function addToCartFromModal() {
        console.log('🛒 Starting addToCartFromModal...');
        if (window.selectedService) {
            const service = window.selectedService;
            console.log('📦 Selected service:', service);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('❌ CSRF token not found!');
                return;
            }

            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_type: 'service',
                    item_id: service.id,
                    package_type: service.packageCount
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Item added successfully with cart data:', data);

                if (data.success && data.cartItems) {
                    console.log('📦 Using cart data from response');
                    // Use cart data directly from the response
                    displayCartItems(data.cartItems, data.cartTotal);
                    showCartOverview();
                    hidePriceModal();
                } else {
                    console.log('⚠️ No cart data in response, falling back to fetch');
                    hidePriceModal();
                    showCartOverview();
                    updateCartOverview();
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                alert('Error adding to cart: ' + error.message);
            });
        } else {
            console.error('No service selected!');
        }
    }

    function showCartOverview() {
        const cartElement = document.getElementById('cart-overview');
        if (cartElement) {
            cartElement.classList.remove('hidden');
            cartElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function hideCartOverview() {
        document.getElementById('cart-overview').classList.add('hidden');
    }

    // Cart overview functions (global scope)
    function updateCartOverview() {
        console.log('🔄 updateCartOverview called');
        @if(session('cart') && count(session('cart')) > 0)
            console.log('📋 Cart session exists, fetching details...');
            fetch('{{ route('cart.details') }}')
                .then(response => {
                    console.log('📡 Cart details response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Cart details data:', data);
                    if (data.items && data.items.length > 0) {
                        console.log('✅ Cart has', data.items.length, 'items');
                        displayCartItems(data.items, data.total);
                        showCartOverview(); // Show cart if it has items
                    } else {
                        console.log('⚠️ Cart is empty');
                        displayEmptyCart();
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading cart:', error);
                    displayEmptyCart();
                });
        @else
            console.log('⚠️ No cart session found');
            displayEmptyCart();
        @endif
    }

    function displayCartItems(items, total) {
        console.log('Displaying cart items:', items);
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');

        if (!cartItemsContainer || !cartTotalElement) {
            console.error('Cart elements not found!');
            return;
        }

        let itemsHtml = '';
        items.forEach(item => {
            console.log('Processing item:', item);

            // Determine the correct display type based on cart_key
            let displayType = 'Einzelbehandlung';

            if (item.cart_key && item.cart_key.includes('_')) {
                const parts = item.cart_key.split('_');
                if (parts.length >= 3) {
                    const packageCount = parts[2];
                    if (packageCount && packageCount !== '1' && packageCount !== 'service') {
                        displayType = packageCount + 'er-Pack';
                        console.log('Detected package type:', displayType, 'from cart_key:', item.cart_key);
                    }
                }
            }

            // Fallback to type field if cart_key doesn't contain package info
            if (displayType === 'Einzelbehandlung' && item.type) {
                if (item.type.includes('er-Pack') || item.type.includes('x Behandlung')) {
                    displayType = item.type;
                }
            }

            const cartKey = item.cart_key || item.cartKey;

            itemsHtml += `
            <div class="flex justify-between items-center py-0.5 border-b last:border-b-0" style="border-color: #138cc8 !important;">
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 text-xs truncate">${item.name}</div>
                    <div class="text-xs text-gray-500">${displayType}</div>
                </div>
                <div class="text-right ml-1 flex-shrink-0">
                    <div class="font-semibold text-green-600 text-xs">${parseFloat(item.price).toFixed(2)} €</div>
                    <button onclick="removeFromCart('${cartKey}')" class="text-red-500 hover:text-red-700 text-xs">
                        Entfernen
                    </button>
                </div>
            </div>
        `;
        });

        cartItemsContainer.innerHTML = itemsHtml;
        cartTotalElement.textContent = total;
        console.log('Cart display updated with', items.length, 'items');
    }

    function displayEmptyCart() {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');

        cartItemsContainer.innerHTML = '<div class="text-gray-500 italic">Noch nichts ausgewählt</div>';
        cartTotalElement.textContent = '0,00 €';
    }

    // Remove from cart function (global scope)
    function removeFromCart(cartKey) {
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
    }
</script>

<!-- Shopping Cart Instruction Modal -->
<div id="cart-instruction-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-30 overflow-y-auto h-full w-full z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-sm w-full mx-auto">
            <!-- Close button -->
            <button onclick="hideCartInstructionModal()"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition-colors z-10">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Behandlung kaufen</h3>
                <div class="mt-2 mb-6">
                    <p class="text-sm text-gray-500">
                        Um eine Behandlung zu kaufen, klicken Sie auf den Preis.
                    </p>
                </div>
                <button onclick="hideCartInstructionModal()"
                        class="px-6 py-2 bg-amber-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-300 w-full"
                        style="background-color: #138cc8 !important;">
                    Okay
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Price Selection Modal -->
<div id="price-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-30 overflow-y-auto h-full w-full z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-sm w-full mx-auto">
            <!-- Close button -->
            <button onclick="hidePriceModal()"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition-colors z-10">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="p-6">
                <!-- Header -->
                <div class="text-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Preise</h3>
                </div>

                <!-- Service Details -->
                <div class="mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h4 id="modal-service-name" class="font-medium text-gray-900 mb-1"></h4>
                        <p id="modal-service-description" class="text-sm text-gray-600"></p>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Preis:</span>
                        <span id="modal-price" class="font-semibold text-gray-900"></span>
                    </div>

                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="text-gray-600">Anzahl:</span>
                        <span id="modal-package-count" class="font-semibold text-gray-900"></span>
                    </div>

                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="text-gray-600">Gesamt:</span>
                        <span id="modal-total-price" class="font-semibold text-gray-900"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button onclick="hidePriceModal()"
                            class="flex-1 px-4 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium rounded-md transition-colors">
                        ZURÜCK
                    </button>
                    <button onclick="addToCartFromModal()"
                            class="flex-1 px-4 py-3 text-white font-medium rounded-md transition-colors hover:opacity-90"
                            style="background-color: #138cc8 !important;">
                        IN DEN WARENKORB LEGEN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
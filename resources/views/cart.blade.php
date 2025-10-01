<x-layouts.guest>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Shopping Cart</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($cartItems->count() > 0)
            <div class="space-y-4 mb-6">
                @foreach($cartItems as $item)
                    <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow">
                        <div>
                            <h3 class="font-semibold">{{ $item->name }}</h3>
                            <p class="text-gray-600">{{ $item->short_code ?? $item->services->pluck('short_code')->implode(', ') }}</p>
                            @if($item->cart_type === 'package')
                                <p class="text-sm text-gray-500">Paket mit {{ $item->services->count() }} Services</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="font-bold text-green-600">{{ $item->price }} €</span>
                            <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="item_type" value="{{ $item->cart_type }}">
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded" title="Remove">
                                    @svg('heroicon-o-trash', 'w-4 h-4')
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-right mb-6">
                <h2 class="text-2xl font-bold">Total: {{ $cartItems->sum('price') }} €</h2>
            </div>

            <div class="flex justify-between">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white p-3 rounded" title="Clear Cart">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                    </button>
                </form>
                <a href="{{ route('booking') }}?cart_services={{ implode(',', $cartServices ?? []) }}&cart_packages={{ implode(',', $cartPackages ?? []) }}" class="bg-blue-500 hover:bg-blue-600 text-white p-3 rounded font-medium" title="Proceed to Booking">
                    @svg('heroicon-o-arrow-right', 'w-5 h-5')
                </a>
            </div>
        @else
            <p class="text-center text-gray-500">Your cart is empty. <a href="/home" class="text-blue-500 hover:text-blue-700 font-medium transition-colors duration-300">Continue shopping</a></p>
        @endif
    </div>
</x-layouts.guest>
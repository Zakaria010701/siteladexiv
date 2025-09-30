<div class="cms-block py-8">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold mb-8 text-center">{{ $content['category_id'] ? \App\Models\Category::find($content['category_id'])->name : 'Preise' }} Preise {{ $content['gender'] === 'female' ? '(Damen)' : ($content['gender'] === 'male' ? '(Herren)' : '') }}</h2>
        @if(isset($content['category_id']))
            @php
                $category = \App\Models\Category::find($content['category_id']);
                $services = $category ? $category->services()->get() : collect();
                $cart = session('cart', []);
            @endphp
            @if($services->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($services as $service)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">{{ $service->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ $service->description ?? 'Professional service tailored to your needs.' }}</p>
                                @php $oldPrice = round($service->price * 1.1, 0); @endphp
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-lg text-gray-500 line-through">{{ $oldPrice }} €</span>
                                    <span class="text-xl font-bold text-green-600">{{ $service->price }} €</span>
                                </div>
                                <p class="text-sm text-gray-500 mb-4">{{ $service->short_code }}</p>
                                <div class="space-y-4">
                                    {{-- Single Service --}}
                                    <div class="flex justify-between items-center border-b pb-2">
                                        <span class="text-xl font-bold text-green-600">Einzel: {{ $service->price }} €</span>
                                        <div class="cart-actions">
                                            @if(in_array('service_' . $service->id, $cart))
                                                <span class="text-green-600 font-bold text-sm">Bereits im Warenkorb</span>
                                                <form action="{{ route('cart.remove') }}" method="POST" class="inline ml-2">
                                                    @csrf
                                                    <input type="hidden" name="item_type" value="service">
                                                    <input type="hidden" name="item_id" value="{{ $service->id }}">
                                                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 text-xs px-2 py-1 rounded border border-red-300" data-type="service" data-id="{{ $service->id }}">
                                                        Entfernen
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="item_type" value="service">
                                                    <input type="hidden" name="item_id" value="{{ $service->id }}">
                                                    <button type="submit" class="bg-white hover:bg-gray-50 text-green-600 font-bold text-sm px-4 py-2 rounded border border-green-500" data-type="service" data-id="{{ $service->id }}">
                                                        Hinzufügen
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- 6x Package --}}
                                    @php $sixPack = $service->servicePackages->firstWhere('name', 'like', '%6x%'); @endphp
                                    @if($sixPack)
                                        <div class="pt-2">
                                            <h4 class="font-medium mb-2 text-gray-800">6x Paket:</h4>
                                            <div class="flex justify-between items-center py-1 text-sm">
                                                <span>{{ $sixPack->name }}: <span class="font-bold text-green-600">{{ $sixPack->price }} €</span> (Ersparnis: {{ $sixPack->discount ?? '0' }}%)</span>
                                                <div class="cart-actions">
                                                    @if(in_array('package_' . $sixPack->id, $cart))
                                                        <span class="text-green-600 font-bold">Im Warenkorb</span>
                                                        <form action="{{ route('cart.remove') }}" method="POST" class="inline ml-2">
                                                            @csrf
                                                            <input type="hidden" name="item_type" value="package">
                                                            <input type="hidden" name="item_id" value="{{ $sixPack->id }}">
                                                            <button type="submit" class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded border border-red-300" data-type="package" data-id="{{ $sixPack->id }}">
                                                                Entfernen
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="item_type" value="package">
                                                            <input type="hidden" name="item_id" value="{{ $sixPack->id }}">
                                                            <button type="submit" class="bg-blue-100 hover:bg-blue-200 text-blue-600 text-xs px-4 py-2 rounded border border-blue-300" data-type="package" data-id="{{ $sixPack->id }}">
                                                                Hinzufügen
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
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
    let cartCount = {{ json_encode(count(session('cart', []))) }};
    const buttons = document.querySelectorAll('button[data-type]');
    const countBadge = document.getElementById('cart-count');

    function updateCart(type, id, isAdd) {
        const url = isAdd ? '{{ route('cart.add') }}' : '{{ route('cart.remove') }}';
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                item_type: type,
                item_id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartKey = type + '_' + id;
                if (isAdd) {
                    cartCount++;
                    // Update button to show added
                    const btn = event.target;
                    btn.innerHTML = 'Im Warenkorb';
                    btn.classList.add('bg-green-100', 'text-green-600');
                    btn.classList.remove('bg-white', 'bg-blue-100', 'hover:bg-gray-50', 'hover:bg-blue-200', 'text-green-600', 'text-blue-600', 'border-green-500', 'border-blue-300');
                    btn.disabled = true;
                } else {
                    cartCount--;
                    // Update button to show add again
                    const btn = event.target;
                    btn.innerHTML = 'Hinzufügen';
                    btn.classList.remove('bg-green-100', 'text-green-600');
                    if (type === 'service') {
                        btn.classList.add('bg-white', 'text-green-600', 'border-green-500');
                    } else {
                        btn.classList.add('bg-blue-100', 'text-blue-600', 'border-blue-300');
                    }
                    btn.disabled = false;
                }
                if (countBadge) {
                    countBadge.textContent = cartCount;
                    countBadge.style.display = cartCount > 0 ? 'flex' : 'none';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.dataset.type;
            const id = this.dataset.id;
            const isAdd = !this.disabled;
            updateCart(type, id, isAdd);
        });
    });
});
</script>
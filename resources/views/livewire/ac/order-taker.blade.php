<div class="h-screen flex flex-col md:flex-row overflow-hidden bg-gray-100">
    <!-- Products Grid -->
    <div class="flex-1 flex flex-col h-full">
        <div class="p-4 bg-white shadow z-10">
            <input wire:model.live="search" type="text" class="form-control w-full" placeholder="Buscar productos...">
        </div>
        
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($products as $product)
                <div wire:click="addToCart({{ $product->id }})" class="bg-white rounded-lg shadow cursor-pointer hover:shadow-lg transition transform hover:-translate-y-1 p-3 flex flex-col items-center text-center">
                    <div class="w-24 h-24 bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-400">
                        @if($product->provisional_image)
                            <img src="{{ $product->provisional_image }}" class="w-full h-full object-cover rounded">
                        @else
                            <i data-lucide="image" class="w-10 h-10"></i>
                        @endif
                    </div>
                    <h3 class="font-medium text-sm text-gray-800 leading-tight">{{ $product->name }}</h3>
                    <div class="text-green-600 font-bold mt-1">${{ number_format($product->price, 2) }}</div>
                    <div class="text-xs text-gray-500">Stock: {{ $product->stock }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Cart Sidebar / Bottom Sheet -->
    <div class="w-full md:w-96 bg-white shadow-xl flex flex-col border-l border-gray-200 h-1/3 md:h-full">
        <div class="p-4 bg-theme-jaki-primary text-white font-bold flex justify-between items-center">
            <span>Pedido Actual</span>
            <span>Total: ${{ number_format($total, 2) }}</span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @if(count($cart) > 0)
                @foreach($cart as $id => $item)
                <div class="flex justify-between items-center border-b pb-2">
                    <div class="flex-1">
                        <div class="font-bold text-gray-700">{{ $item['name'] }}</div>
                        <div class="text-sm text-gray-500">${{ $item['price'] }} x {{ $item['quantity'] }}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button wire:click="decreaseQty({{ $id }})" class="btn btn-sm btn-secondary">-</button>
                        <span>{{ $item['quantity'] }}</span>
                        <button wire:click="increaseQty({{ $id }})" class="btn btn-sm btn-secondary">+</button>
                    </div>
                    <div class="font-bold ml-2 text-gray-800">${{ number_format($item['subtotal'], 2) }}</div>
                </div>
                @endforeach
            @else
                <div class="text-center text-gray-500 mt-10">El carrito está vacío</div>
            @endif
        </div>

        <div class="p-4 border-t bg-gray-50">
            @if (session()->has('message'))
                <div class="alert alert-success show mb-2 p-2 text-sm text-center bg-green-100 text-green-800 rounded">
                    {{ session('message') }}
                </div>
            @endif
            <button wire:click="sendToCashier" class="btn btn-primary w-full h-12 text-lg shadow-lg" {{ empty($cart) ? 'disabled' : '' }}>
                Enviar a Caja
            </button>
        </div>
    </div>
</div>

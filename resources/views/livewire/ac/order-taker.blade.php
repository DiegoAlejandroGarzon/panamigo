<div class="intro-y grid grid-cols-12 gap-6 mt-5 bg-slate-100 p-2 rounded-lg">
    <!-- Products Grid -->
    <div class="col-span-12 lg:col-span-8 flex flex-col">
        <div class="intro-y box p-4 mb-4">
            <x-base.form-input 
                wire:model.live="search" 
                type="text" 
                class="w-full" 
                placeholder="Buscar productos..."
            />
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 overflow-y-auto" style="max-height: 70vh;">
            @foreach($products as $product)
            <div wire:click="addToCart({{ $product->id }})" class="intro-y box cursor-pointer hover:shadow-lg transition transform hover:-translate-y-1 p-3 flex flex-col items-center text-center">
                <div class="w-24 h-24 bg-slate-200 rounded-md mb-2 flex items-center justify-center text-slate-400 overflow-hidden">
                    @if($product->provisional_image)
                        <img src="{{ $product->provisional_image }}" class="w-full h-full object-cover">
                    @else
                        <x-base.lucide icon="Image" class="w-10 h-10" />
                    @endif
                </div>
                <h3 class="font-medium text-sm text-slate-800 leading-tight h-10 overflow-hidden">{{ $product->name }}</h3>
                <div class="text-success font-bold mt-1">${{ number_format($product->price, 2) }}</div>
                <div class="text-xs text-slate-500">Stock: {{ $product->stock }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div class="col-span-12 lg:col-span-4 flex flex-col h-full">
        <div class="intro-y box flex flex-col h-full overflow-hidden shadow-xl border-l border-slate-200">
            <div class="p-4 bg-primary text-white font-bold flex justify-between items-center rounded-t-lg">
                <span class="flex items-center"><x-base.lucide icon="ShoppingCart" class="mr-2 h-5 w-5" /> Pedido Actual</span>
                <span class="text-lg">Total: ${{ number_format($total, 2) }}</span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3 min-h-[400px]">
                @forelse($cart as $id => $item)
                    <div class="intro-x flex justify-between items-center border-b border-slate-100 pb-3">
                        <div class="flex-1">
                            <div class="font-bold text-slate-700">{{ $item['name'] }}</div>
                            <div class="text-sm text-slate-500">${{ number_format($item['price'], 2) }} x {{ $item['quantity'] }}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <x-base.button wire:click="decreaseQty({{ $id }})" size="sm" variant="outline-secondary" class="w-8 h-8 p-0">-</x-base.button>
                            <span class="font-medium w-6 text-center">{{ $item['quantity'] }}</span>
                            <x-base.button wire:click="increaseQty({{ $id }})" size="sm" variant="outline-secondary" class="w-8 h-8 p-0">+</x-base.button>
                        </div>
                        <div class="font-bold ml-4 text-slate-800 w-20 text-right">${{ number_format($item['subtotal'], 2) }}</div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center text-slate-400 mt-10">
                        <x-base.lucide icon="ShoppingBag" class="w-16 h-16 mb-2 opacity-20" />
                        <p>El carrito está vacío</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t border-slate-200 bg-slate-50">
                @if (session()->has('message'))
                    <div class="alert alert-success show mb-3 text-center">
                        {{ session('message') }}
                    </div>
                @endif
                <x-base.button 
                    wire:click="sendToCashier" 
                    variant="primary" 
                    class="w-full h-12 text-lg shadow-md" 
                    :disabled="empty($cart)"
                >
                    ENVIAR A CAJA
                </x-base.button>
            </div>
        </div>
    </div>
</div>


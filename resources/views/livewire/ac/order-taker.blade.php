<div class="intro-y grid grid-cols-12 gap-6 mt-5 bg-slate-100 p-2 rounded-lg">
    <!-- Products Grid -->
    <div class="col-span-12 lg:col-span-8 flex flex-col">
        <div class="intro-y box p-4 mb-4">
            <x-base.form-input wire:model.live="search" type="text" class="w-full" placeholder="Buscar productos..." />
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 overflow-y-auto" style="max-height: 70vh;">
            @foreach ($products as $product)
                <div wire:click="addToCart({{ $product->id }})"
                    class="intro-y box cursor-pointer hover:shadow-lg transition transform hover:-translate-y-1 p-3 flex flex-col items-center text-center {{ strtoupper($product->name) == 'PAN' ? 'border-2 border-primary bg-primary/5' : '' }}">
                    <div
                        class="w-24 h-24 bg-slate-200 rounded-md mb-2 flex items-center justify-center text-slate-400 overflow-hidden relative">
                        @if ($product->provisional_image)
                            <img src="{{ $product->provisional_image }}" class="w-full h-full object-cover">
                        @else
                            <x-base.lucide icon="Image" class="w-10 h-10" />
                        @endif
                        @if (strtoupper($product->name) == 'PAN')
                            <div class="absolute inset-0 bg-primary/10 flex items-center justify-center">
                                <span
                                    class="bg-primary text-white text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">Variado</span>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-medium text-sm text-slate-800 leading-tight h-10 overflow-hidden">
                        {{ $product->name }}</h3>
                    <div class="text-success font-bold mt-1">
                        @if (strtoupper($product->name) == 'PAN')
                            PRECIO VARIABLE
                        @else
                            ${{ number_format($product->price, 2) }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div class="col-span-12 lg:col-span-4 flex flex-col h-full">
        <div class="intro-y box flex flex-col h-full overflow-hidden shadow-xl border-l border-slate-200">
            <div class="p-4 bg-primary text-white font-bold flex justify-between items-center rounded-t-lg">
                <span class="flex items-center"><x-base.lucide icon="ShoppingCart" class="mr-2 h-5 w-5" /> Pedido
                    Actual</span>
                <span class="text-lg">Total: ${{ number_format($total, 2) }}</span>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 min-h-[400px]">
                @forelse($cart as $id => $item)
                    <div class="intro-x flex justify-between items-center border-b border-slate-100 pb-3">
                        <div class="flex-1">
                            <div class="font-bold text-slate-700">{{ $item['name'] }}</div>
                            <div class="text-sm text-slate-500">${{ number_format($item['price'], 2) }} x
                                {{ $item['quantity'] }}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if (!($item['is_pan'] ?? false))
                                <x-base.button wire:click="decreaseQty({{ $id }})" size="sm"
                                    variant="outline-secondary" class="w-8 h-8 p-0">-</x-base.button>
                                <span class="font-medium w-6 text-center">{{ $item['quantity'] }}</span>
                                <x-base.button wire:click="increaseQty({{ $id }})" size="sm"
                                    variant="outline-secondary" class="w-8 h-8 p-0">+</x-base.button>
                            @else
                                <span class="text-xs bg-slate-200 px-2 py-1 rounded">VALOR FIJO</span>
                            @endif
                        </div>
                        <div class="font-bold ml-4 text-slate-800 w-20 text-right">
                            ${{ number_format($item['subtotal'], 2) }}</div>
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
                <x-base.button wire:click="sendToCashier" variant="primary" class="w-full h-12 text-lg shadow-md"
                    :disabled="empty($cart)">
                    ENVIAR A CAJA
                </x-base.button>
            </div>
        </div>
    </div>

    <!-- Pan Modal -->
    @if ($showPanModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="intro-y box w-full max-w-sm bg-white shadow-2xl rounded-xl">
                <div class="p-5 border-b border-slate-200 flex justify-between items-center text-primary font-bold">
                    <h2>VALOR TOTAL DEL PAN</h2>
                    <button wire:click="closePanModal" class="text-slate-400 hover:text-slate-600">
                        <x-base.lucide icon="X" class="w-6 h-6" />
                    </button>
                </div>
                <div class="p-6">
                    <label class="font-bold text-xs uppercase text-slate-500 mb-2 block">Digite el valor total del pan
                        despachado</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-lg font-bold text-slate-400">$</span>
                        <x-base.form-input type="number" wire:model="panAmount" class="text-2xl font-bold pl-8 py-3"
                            autofocus />
                    </div>
                    @error('panAmount')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="p-5 flex gap-2">
                    <x-base.button wire:click="closePanModal" variant="outline-secondary"
                        class="flex-1">CANCELAR</x-base.button>
                    <x-base.button wire:click="addPanToCart" variant="primary" class="flex-1">AGREGAR</x-base.button>
                </div>
            </div>
        </div>
    @endif
</div>

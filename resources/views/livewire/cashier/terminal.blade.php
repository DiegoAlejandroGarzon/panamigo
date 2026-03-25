<div x-data="{
    showPOS: false,
    allProducts: @js($allProducts),
    search: '',
    categoryId: '',
    brandId: '',

    get filteredProducts() {
        if (!this.allProducts) return [];
        return this.allProducts.filter(p => {
            const s = this.search.toLowerCase();
            const matchesSearch = p.name.toLowerCase().includes(s);
            const matchesCategory = this.categoryId === '' || p.category_id == this.categoryId;
            const matchesBrand = this.brandId === '' || p.brand_id == this.brandId;
            return matchesSearch && matchesCategory && matchesBrand;
        });
    },

    cart: [],
    total: 0,
    isCartOpen: false,

    showPanModal: false,
    panAmount: '',
    panProduct: null,
    showQtyModal: false,
    tempQty: 1,
    tempProduct: null,

    addItem(product) {
        if (product.name.toUpperCase() === 'PAN') {
            this.panProduct = product;
            this.panAmount = '';
            this.showPanModal = true;
            return;
        }
        this.tempProduct = product;
        this.tempQty = 1;
        this.showQtyModal = true;
        setTimeout(() => {
            const input = document.getElementById('qtyInputPOS');
            if (input) {
                input.focus();
                input.select();
            }
        }, 100);
    },

    confirmQty() {
        const qty = parseInt(this.tempQty);
        if (isNaN(qty) || qty <= 0) return;
        const product = this.tempProduct;
        let existing = this.cart.find(i => i.id === product.id && !i.is_pan);
        if (existing) {
            existing.quantity += qty;
            existing.subtotal = existing.quantity * parseFloat(product.price);
        } else {
            this.cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                quantity: qty,
                subtotal: qty * parseFloat(product.price),
                is_pan: false
            });
        }
        this.calculateTotal();
        this.showQtyModal = false;
    },

    addPan() {
        if (!this.panAmount || this.panAmount <= 0) return;
        this.cart.push({
            id: this.panProduct.id,
            name: this.panProduct.name,
            price: parseFloat(this.panAmount),
            quantity: 1,
            subtotal: parseFloat(this.panAmount),
            is_pan: true
        });
        this.calculateTotal();
        this.showPanModal = false;
    },

    increase(id, is_pan) {
        if (is_pan) return;
        let item = this.cart.find(i => i.id === id && !i.is_pan);
        if (item) {
            item.quantity++;
            item.subtotal = item.quantity * item.price;
            this.calculateTotal();
        }
    },

    decrease(id, is_pan) {
        let index = this.cart.findIndex(i => i.id === id && i.is_pan === is_pan);
        if (index !== -1) {
            let item = this.cart[index];
            if (item.quantity > 1 && !item.is_pan) {
                item.quantity--;
                item.subtotal = item.quantity * item.price;
            } else {
                this.cart.splice(index, 1);
            }
            this.calculateTotal();
        }
    },

    calculateTotal() {
        this.total = this.cart.reduce((sum, i) => sum + i.subtotal, 0);
    },

    submitOrder() {
        if (this.cart.length === 0) return;
        $wire.createOrderFromTerminal(this.cart, this.total).then(() => {
            this.showPOS = false;
        });
    },

    init() {
        window.addEventListener('order-sent', () => {
            this.cart = [];
            this.total = 0;
            this.isCartOpen = false;
        });
    }
}">
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Caja - Terminal de Venta</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2">
            <x-base.button @click="showPOS = true" variant="success"
                class="shadow-md flex items-center gap-2 text-white font-bold">
                <x-base.lucide icon="PlusSquare" class="w-5 h-5" /> NUEVO PEDIDO
            </x-base.button>
            <x-base.button wire:click="openZModal" variant="primary" class="shadow-md flex items-center gap-2">
                <x-base.lucide icon="FileText" class="w-4 h-4" /> REPORTE Z
            </x-base.button>
            <x-base.button wire:click="openDrawerOnly" variant="outline-secondary"
                class="shadow-md flex items-center gap-2">
                <x-base.lucide icon="Archive" class="w-4 h-4" /> ABRIR CAJÓN
            </x-base.button>
            <div class="flex items-center bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm">
                <span class="mr-3 text-xs font-bold text-slate-600 uppercase">Imprimir Ticket:</span>
                <div class="form-check form-switch ps-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"
                        wire:model.live="shouldPrint">
                </div>
            </div>
        </div>
    </div>

    <!-- Z Report Modal -->
    @if ($showZModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="intro-y box w-full max-w-md bg-white shadow-2xl rounded-xl">
                <div class="p-5 border-b border-slate-200 flex justify-between items-center">
                    <h2 class="font-bold text-lg">GENERAR REPORTE Z (DIARIO)</h2>
                    <button wire:click="closeZModal" class="text-slate-400 hover:text-slate-600"><x-base.lucide
                            icon="X" class="w-6 h-6" /></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label font-bold text-xs uppercase text-slate-500">Fecha del Reporte</label>
                        <input type="date" wire:model.live="zDate" class="form-control">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-base.button wire:click="loadRealZData" variant="outline-primary" class="w-full text-xs py-3">
                            <x-base.lucide icon="RefreshCw" class="w-4 h-4 mr-2" /> VALORES REALES (BD)
                        </x-base.button>
                        <div class="flex items-center text-xs text-slate-400 italic">
                            Carga el total de ventas y personas del día seleccionado.
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label font-bold text-xs uppercase text-slate-500">Total Venta ($)</label>
                            <input type="number" wire:model="zTotal" class="form-control text-xl font-bold">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs uppercase text-slate-500">Personas
                                (Count)</label>
                            <input type="number" wire:model="zCount" class="form-control text-xl font-bold">
                        </div>
                    </div>
                </div>
                <div class="p-5 border-t border-slate-200 flex justify-end gap-2 bg-slate-50 rounded-b-xl">
                    <x-base.button wire:click="closeZModal" variant="outline-secondary">CANCELAR</x-base.button>
                    <x-base.button wire:click="printZReport" variant="success" class="text-white px-8">
                        <x-base.lucide icon="Printer" class="w-4 h-4 mr-2" /> IMPRIMIR Z
                    </x-base.button>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Fast Sale Section -->
        <div class="col-span-12">
            <div class="intro-y box p-5 bg-primary text-white">
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold">VENTA RÁPIDA (VALOR DIRECTO)</h3>
                        <p class="text-white/70 text-sm italic">Ingresa el total y presiona cobrar para registrar una
                            venta inmediata.</p>
                    </div>
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-800 font-bold">$</span>
                        </div>
                        <input type="number" step="0.01" wire:model="fastAmount"
                            class="form-control form-control-lg pl-8 font-bold text-slate-800" placeholder="0">
                    </div>
                    <x-base.button wire:click="processFastSale" variant="success" size="lg"
                        class="w-full md:w-auto px-8 py-3 text-white font-bold shadow-xl flex items-center gap-2">
                        <x-base.lucide icon="CheckCircle" class="w-6 h-6" /> COBRAR AHORA
                    </x-base.button>
                </div>
                @error('fastAmount')
                    <span
                        class="text-white bg-red-500 px-2 py-1 rounded text-xs mt-2 inline-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- List of Pending Orders -->
        <div class="col-span-12 lg:col-span-4 overflow-y-auto" style="max-height: 80vh;">
            <div class="intro-y box p-5" wire:poll.10s>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-medium text-base flex items-center">
                        <x-base.lucide icon="ClipboardList" class="mr-2 w-5 h-5 text-primary" /> Pedidos de Atención
                    </h2>
                    <x-base.button wire:click="$refresh" variant="outline-secondary" size="sm"
                        class="px-2 py-1 shadow-sm">
                        <x-base.lucide icon="RefreshCw" class="w-4 h-4" />
                    </x-base.button>
                </div>
                <div class="space-y-3">
                    @forelse($pendingOrders as $order)
                        <div wire:click="selectOrder({{ $order->id }})"
                            class="intro-x cursor-pointer p-4 rounded-lg border {{ $selectedOrder && $selectedOrder->id == $order->id ? 'bg-primary text-white border-primary shadow-lg' : 'bg-white border-slate-200 hover:bg-slate-50' }} transition-all duration-200">
                            <div class="flex justify-between items-center">
                                <span class="font-bold">#{{ $order->id }}</span>
                                <span
                                    class="text-xs {{ $selectedOrder && $selectedOrder->id == $order->id ? 'text-white/70' : 'text-slate-500' }}">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span
                                    class="{{ $selectedOrder && $selectedOrder->id == $order->id ? 'text-white/80' : 'text-slate-600' }}">{{ $order->items->count() }}
                                    items</span>
                                <span
                                    class="font-bold {{ $selectedOrder && $selectedOrder->id == $order->id ? 'text-white' : 'text-success' }}">${{ number_format($order->total, 2) }}</span>
                            </div>
                            <div
                                class="text-xs {{ $selectedOrder && $selectedOrder->id == $order->id ? 'text-white/60' : 'text-slate-400' }} mt-1">
                                Atendido por: {{ $order->user->name ?? 'N/A' }}
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center p-10 text-slate-400">
                            <x-base.lucide icon="Inbox" class="w-12 h-12 mb-2 opacity-20" />
                            <p>No hay pedidos pendientes</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="col-span-12 lg:col-span-8">
            <div class="intro-y box h-full flex flex-col min-h-[500px]">
                @if ($selectedOrder)
                    <div
                        class="p-5 border-b border-slate-200 flex justify-between items-center bg-slate-50 rounded-t-lg">
                        <h2 class="font-medium text-xl">Detalle del Pedido <span
                                class="text-primary">#{{ $selectedOrder->id }}</span></h2>
                        <span
                            class="px-3 py-1 rounded-full bg-warning/20 text-warning text-xs font-bold uppercase tracking-wider">Pendiente
                            de Pago</span>
                    </div>

                    <div class="flex-1 overflow-y-auto p-5">
                        <x-base.table class="border-b border-slate-200">
                            <x-base.table.thead>
                                <x-base.table.tr class="bg-slate-100">
                                    <x-base.table.th class="whitespace-nowrap">Producto</x-base.table.th>
                                    <x-base.table.th class="whitespace-nowrap text-center">Cant.</x-base.table.th>
                                    <x-base.table.th class="whitespace-nowrap text-right">Precio</x-base.table.th>
                                    <x-base.table.th class="whitespace-nowrap text-right">Subtotal</x-base.table.th>
                                </x-base.table.tr>
                            </x-base.table.thead>
                            <x-base.table.tbody>
                                @foreach ($selectedOrder->items as $item)
                                    <x-base.table.tr>
                                        <x-base.table.td class="py-4 border-b border-slate-100">
                                            <div class="font-medium text-slate-700">{{ $item->product->name }}</div>
                                        </x-base.table.td>
                                        <x-base.table.td
                                            class="py-4 text-center border-b border-slate-100 text-slate-600">{{ $item->quantity }}</x-base.table.td>
                                        <x-base.table.td
                                            class="py-4 text-right border-b border-slate-100 text-slate-600">${{ number_format($item->product->price, 2) }}</x-base.table.td>
                                        <x-base.table.td
                                            class="py-4 text-right font-bold border-b border-slate-100 text-slate-700">${{ number_format($item->subtotal, 2) }}</x-base.table.td>
                                    </x-base.table.tr>
                                @endforeach
                            </x-base.table.tbody>
                        </x-base.table>

                        <div class="mt-10 flex flex-col items-end px-5">
                            <div class="text-slate-500 font-medium">TOTAL A PAGAR</div>
                            <div class="text-3xl font-bold text-success mt-1">
                                ${{ number_format($selectedOrder->total, 2) }}</div>
                        </div>
                    </div>

                    <div class="p-5 border-t border-slate-200 bg-slate-50 rounded-b-lg flex justify-end items-center">
                        @if (session()->has('message'))
                            <div class="mr-auto text-success font-medium italic">
                                {{ session('message') }}
                            </div>
                        @endif
                        <x-base.button wire:click="markAsPaid" variant="success" size="lg"
                            class="px-10 shadow-lg flex items-center gap-2 text-white">
                            <x-base.lucide icon="{{ $shouldPrint ? 'Printer' : 'Wallet' }}" class="w-5 h-5" />
                            {{ $shouldPrint ? 'COBRAR E IMPRIMIR' : 'COBRAR (SOLO CAJÓN)' }}
                        </x-base.button>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-400">
                        <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <x-base.lucide icon="ShoppingCart" class="w-12 h-12 opacity-20" />
                        </div>
                        <p class="text-lg font-medium">Selecciona un pedido para procesar</p>
                        <p class="text-sm opacity-60 mt-1">O usa la Venta Rápida arriba para un valor directo</p>
                    </div>
                @endif
            </div>
        </div>
        <!-- FULL SCREEN POS OVERLAY -->
        <div x-show="showPOS" class="fixed inset-0 z-[100] bg-slate-100 flex flex-col"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" x-cloak>

            <!-- POS Header -->
            <div
                class="bg-white p-3 shadow-md flex justify-between items-center border-b border-slate-200 sticky top-0 z-[110]">
                <div class="flex items-center gap-2">
                    <div class="bg-success/10 p-2 rounded-lg text-success">
                        <x-base.lucide icon="PlusSquare" class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="font-black text-sm uppercase tracking-tight text-slate-800">NUEVA ORDEN DESDE CAJA
                        </h2>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Atención Directa</p>
                    </div>
                </div>
                <button @click="showPOS = false"
                    class="bg-slate-100 hover:bg-slate-200 p-2 rounded-full transition text-slate-600">
                    <x-base.lucide icon="X" class="w-7 h-7" />
                </button>
            </div>

            <div class="flex-1 flex flex-col overflow-hidden relative">
                <!-- POS Filters -->
                <div class="w-full bg-white p-2 mb-1 flex flex-col gap-1.5 shadow-sm sticky top-0 z-[10]">
                    <div class="w-full">
                        <x-base.form-input x-model="search" type="text" class="w-full text-xs h-8 px-2"
                            placeholder="🔍 Buscar productos..." />
                    </div>
                    <div class="grid grid-cols-2 gap-1.5 w-full">
                        <x-base.form-select x-model="categoryId" class="text-[10px] h-8 px-1 py-0">
                            <option value="">📁 Todas las Categorías</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </x-base.form-select>
                        <x-base.form-select x-model="brandId" class="text-[10px] h-8 px-1 py-0">
                            <option value="">🏷️ Todas las Marcas</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </x-base.form-select>
                    </div>
                </div>

                <!-- POS Body -->
                <div class="flex-1 flex overflow-hidden">
                    <!-- Products Grid -->
                    <div class="flex-1 flex flex-col p-2 overflow-y-auto pb-32">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                            <template x-for="product in filteredProducts" :key="product.id">
                                <div @click="addItem(product)"
                                    class="box cursor-pointer hover:shadow-lg transition transform active:scale-95 p-2 flex flex-col items-center text-center rounded-xl"
                                    :class="product.name.toUpperCase() == 'PAN' ? 'border-2 border-primary bg-primary/5' :
                                        'bg-white'">
                                    <div
                                        class="w-full aspect-square bg-slate-100 rounded-lg mb-1 flex items-center justify-center text-slate-300 overflow-hidden relative">
                                        <template x-if="product.provisional_image">
                                            <img :src="product.provisional_image" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!product.provisional_image">
                                            <x-base.lucide icon="Image" class="w-8 h-8 opacity-20" />
                                        </template>
                                    </div>
                                    <h3 class="font-bold text-[11px] text-slate-900 leading-tight h-6 overflow-hidden uppercase"
                                        x-text="product.name"></h3>
                                    <div class="text-success font-black text-[10px] mt-1 bg-success/10 px-2 py-0.5 rounded-full border border-success/20"
                                        x-text="product.name.toUpperCase() == 'PAN' ? 'VARIAR PRECIO' : '$' + new Intl.NumberFormat().format(product.price)">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Floating Cart Toggle (Mobile) -->
                    <div class="fixed bottom-6 right-6 lg:hidden z-[110]">
                        <button @click="isCartOpen = true"
                            class="bg-primary text-white p-4 rounded-full shadow-2xl flex items-center gap-2 relative border-4 border-white transform active:scale-90 transition">
                            <x-base.lucide icon="ShoppingCart" class="w-7 h-7" />
                            <span x-show="cart.length > 0" x-text="cart.length"
                                class="absolute -top-2 -right-2 bg-danger text-white text-[10px] w-6 h-6 rounded-full flex items-center justify-center font-black border-2 border-white shadow-md"></span>
                        </button>
                    </div>

                    <!-- Slide-out Cart Sidebar -->
                    <div class="w-full lg:w-1/3 lg:flex flex-col border-l border-slate-200"
                        :class="isCartOpen ? 'fixed inset-0 z-[120] bg-white lg:relative lg:bg-white' : 'hidden lg:flex'">
                        <div class="flex flex-col h-full bg-white">
                            <div class="p-4 bg-primary text-white font-bold flex justify-between items-center">
                                <span class="flex items-center uppercase tracking-tighter"><x-base.lucide
                                        icon="ShoppingCart" class="mr-2 h-5 w-5" /> Orden</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-xl font-black"
                                        x-text="'$' + new Intl.NumberFormat().format(total)">$0</span>
                                    <button @click="isCartOpen = false"
                                        class="lg:hidden text-white/50 hover:text-white">
                                        <x-base.lucide icon="X" class="w-7 h-7" />
                                    </button>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto p-3 space-y-2 min-h-[300px] bg-slate-50">
                                <template x-for="(item, index) in cart" :key="index">
                                    <div
                                        class="flex justify-between items-center border border-slate-100 p-2 rounded-lg bg-white shadow-sm">
                                        <div class="flex-1 pr-2">
                                            <div class="font-black text-slate-800 text-[12px] leading-tight uppercase"
                                                x-text="item.name"></div>
                                            <div class="text-[10px] text-slate-500 font-bold mt-1">
                                                <span class="text-primary"
                                                    x-text="'$' + new Intl.NumberFormat().format(item.price)"></span> x
                                                <span x-text="item.quantity"></span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <template x-if="!item.is_pan">
                                                <div class="flex items-center gap-1">
                                                    <button @click="decrease(item.id, false)"
                                                        class="w-7 h-7 bg-slate-100 rounded flex items-center justify-center font-black text-danger">-</button>
                                                    <span class="font-black text-sm w-5 text-center text-slate-700"
                                                        x-text="item.quantity"></span>
                                                    <button @click="increase(item.id, false)"
                                                        class="w-7 h-7 bg-slate-100 rounded flex items-center justify-center font-black text-success">+</button>
                                                </div>
                                            </template>
                                            <template x-if="item.is_pan">
                                                <div class="flex items-center">
                                                    <span
                                                        class="text-[9px] bg-primary/10 text-primary border border-primary/20 px-2 py-1 rounded-full font-bold">PAN</span>
                                                    <button @click="decrease(item.id, true)" class="ml-2 text-danger">
                                                        <x-base.lucide icon="Trash2" class="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="font-black ml-2 text-slate-900 text-xs w-20 text-right"
                                            x-text="'$' + new Intl.NumberFormat().format(item.subtotal)">
                                        </div>
                                    </div>
                                </template>

                                <div x-show="cart.length === 0"
                                    class="flex flex-col items-center justify-center text-slate-300 h-full opacity-60">
                                    <x-base.lucide icon="ShoppingBag" class="w-16 h-16 mb-2" />
                                    <p class="font-bold uppercase text-[10px] tracking-widest">Carrito Vacío</p>
                                </div>
                            </div>

                            <div class="p-4 bg-white border-t border-slate-200">
                                <button @click="submitOrder()" :disabled="cart.length === 0"
                                    :class="cart.length > 0 ? 'bg-success text-white shadow-xl transform active:scale-95' :
                                        'bg-slate-300 text-slate-500 cursor-not-allowed'"
                                    class="w-full h-16 text-xl font-black rounded-xl flex items-center justify-center gap-3 transition-all uppercase tracking-tighter">
                                    <x-base.lucide icon="CheckCircle" class="w-7 h-7" />
                                    REGISTRAR Y COBRAR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- POS Modals (Qty/Price) -->
            <div x-show="showQtyModal"
                class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md"
                x-cloak>
                <div
                    class="box w-full max-w-sm bg-white shadow-2xl rounded-2xl overflow-hidden border border-white/20">
                    <div
                        class="p-5 border-b border-slate-100 flex justify-between items-center text-primary bg-slate-50">
                        <h2 class="font-black uppercase tracking-tight" x-text="tempProduct ? tempProduct.name : ''">
                        </h2>
                        <button @click="showQtyModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <x-base.lucide icon="X" class="w-6 h-6" />
                        </button>
                    </div>
                    <form @submit.prevent="confirmQty()" class="p-6">
                        <label
                            class="font-black text-[10px] uppercase text-slate-400 mb-2 block tracking-widest text-center italic">Seleccione
                            o digite la cantidad</label>
                        <div class="flex items-center justify-center gap-4 mb-6">
                            <button type="button" @click="if(tempQty > 1) tempQty--"
                                class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-3xl font-black text-danger shadow hover:bg-slate-200 transition">-</button>
                            <input id="qtyInputPOS" type="number" x-model="tempQty"
                                class="w-24 text-center text-5xl font-black border-none focus:ring-0 text-slate-800"
                                min="1" />
                            <button type="button" @click="tempQty++"
                                class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-3xl font-black text-success shadow hover:bg-slate-200 transition">+</button>
                        </div>
                        <div class="grid grid-cols-4 gap-2 mb-6">
                            <template x-for="q in [1, 2, 3, 4, 5, 10, 12, 15]">
                                <button type="button" @click="tempQty = q"
                                    class="py-3 bg-slate-50 border border-slate-200 rounded-lg font-black text-slate-600 hover:bg-primary hover:text-white transition"
                                    :class="tempQty == q ? 'bg-primary text-white border-primary' : ''"
                                    x-text="q"></button>
                            </template>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="showQtyModal = false"
                                class="flex-1 py-4 font-bold bg-slate-100 rounded-xl text-slate-500">CANCELAR</button>
                            <button type="submit"
                                class="flex-1 py-4 font-black bg-primary text-white rounded-xl shadow-lg">CONFIRMAR</button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="showPanModal"
                class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md"
                x-cloak>
                <div
                    class="box w-full max-w-sm bg-white shadow-2xl rounded-2xl overflow-hidden border border-white/20">
                    <div
                        class="p-5 border-b border-slate-100 flex justify-between items-center text-primary bg-slate-50">
                        <h2 class="font-black uppercase tracking-tight">CANTIDAD EN PESOS ($)</h2>
                        <button @click="showPanModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <x-base.lucide icon="X" class="w-6 h-6" />
                        </button>
                    </div>
                    <form @submit.prevent="addPan()" class="p-6">
                        <label
                            class="font-black text-[10px] uppercase text-slate-400 mb-2 block tracking-widest">Digite
                            el
                            total vendido</label>
                        <div class="relative">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-black text-slate-300">$</span>
                            <input type="number" x-model="panAmount"
                                class="w-full text-4xl font-black pl-10 py-5 rounded-xl border-2 border-slate-200 focus:border-primary transition-colors text-slate-800"
                                autofocus placeholder="0" />
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button type="button" @click="showPanModal = false"
                                class="flex-1 py-4 font-bold bg-slate-100 rounded-xl">CANCELAR</button>
                            <button type="submit"
                                class="flex-1 py-4 font-black bg-primary text-white rounded-xl shadow-lg">AGREGAR</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('print-ticket', (data) => {
                    const order = data.order;
                    console.log("Printing order:", order);
                    printTicket(order);
                });

                Livewire.on('print-z', (event) => {
                    const zData = event.data;
                    console.log("Printing Z Report:", zData);
                    printZReport(zData);
                });

                Livewire.on('open-drawer', () => {
                    console.log("Opening drawer only...");
                    openDrawer();
                });
            });

            const PRINTER_NAME = "XP-58"; // Cambiar por el nombre real de la tiquetera

            function openDrawer() {
                if (typeof qz === 'undefined') return;

                qz.websocket.connect().then(function() {
                    return qz.printers.find(PRINTER_NAME);
                }).then(function(printer) {
                    var config = qz.configs.create(printer);
                    // Comando ESC/POS para abrir cajón (Connector 1)
                    var data = ['\x1B\x70\x00\x19\xFA'];
                    return qz.print(config, data);
                }).catch(e => console.error(e)).finally(() => qz.websocket.disconnect());
            }

            function printZReport(z) {
                if (typeof qz === 'undefined') return;

                qz.websocket.connect().then(function() {
                    return qz.printers.find(PRINTER_NAME);
                }).then(function(printer) {
                    var config = qz.configs.create(printer);
                    var formattedDate = z.date;
                    var total = parseInt(z.total).toLocaleString('es-CO');
                    var count = z.count.toString().padStart(4, ' ');

                    var data = [
                        '\x1B' + '\x40', // Initialize
                        '\x1B' + '\x61' + '\x31', // Center
                        '\x1B' + '\x21' + '\x08', // Bold
                        'JAKI - PAN Y SUS DELICIAS\x0A',
                        '\x1B' + '\x21' + '\x00', // Normal
                        'JACQUELINE NOVOA URREGO\x0A',
                        'NIT. 39.628.435-9\x0A',
                        'REGIMEN NO RESPONSABLE DE IVA\x0A\x0A',
                        'KRA 16 # 5-04\x0A',
                        'ALTO DEL ROSARIO\x0A',
                        'REG CASIO SE-800-0303888\x0A\x0A',
                        'Z   ' + formattedDate + '   3888 387625\x0A',
                        '--------------------------------\x0A',
                        'Z DAIARIO\x0A',
                        '--------------------------------\x0A',
                        '\x1B' + '\x61' + '\x30', // Left
                        'Z       DEPTOS           3392\x0A',
                        '                       0001015\x0A\x0A',
                        'DEPTO1             ' + count + '\x0A',
                        '                   ' + total + '\x0A\x0A',
                        'TL                 ' + count + '\x0A',
                        '                   ' + total + '\x0A\x0A',
                        'Z       TOT. FIJOS       3392\x0A',
                        '                       0001011\x0A\x0A',
                        'BRUTO              ' + count + '\x0A',
                        '                   ' + total + '\x0A',
                        'NETO               ' + count + '\x0A',
                        '                   ' + total + '\x0A',
                        'EFEC               ' + count + '\x0A',
                        '                   ' + total + '\x0A\x0A',
                        'BASE 1                0\x0A',
                        'BASE 2                0\x0A',
                        '                      0\x0A',
                        '                      0\x0A',
                        '          387542----->387625\x0A\x0A',
                        'Z       FUNC LIBRES      3392\x0A',
                        '                       0001012\x0A\x0A',
                        'CAJA               ' + count + '\x0A',
                        '                   ' + total + '\x0A\x0A',
                        'Z       CAJ/EMPLEADO     3392\x0A',
                        '                       0001017\x0A\x0A',
                        '\x0A\x0A\x0A\x0A\x0A\x1B\x69' // Paper cut
                    ];

                    return qz.print(config, data);
                }).catch(e => console.error(e)).finally(() => qz.websocket.disconnect());
            }

            function printTicket(order) {
                if (typeof qz === 'undefined') {
                    console.error('QZ Tray library not loaded!');
                    return;
                }

                qz.websocket.connect().then(function() {
                    return qz.printers.find(PRINTER_NAME);
                }).then(function(printer) {
                    var config = qz.configs.create(printer);

                    var data = [
                        '\x1B' + '\x40', // Initialize
                        '\x1B' + '\x70' + '\x00' + '\x19' + '\xFA', // Open Drawer
                        '\x1B' + '\x61' + '\x31', // Center alignment
                        '\x1B' + '\x21' + '\x08', // Bold
                        'JAKI - PAN Y SUS DELICIAS\x0A',
                        '\x1B' + '\x21' + '\x00', // Normal font
                        'JACQUELINE NOVOA URREGO\x0A',
                        'NIT. 39.628.435-9\x0A',
                        'REGIMEN NO RESPONSABLE DE IVA\x0A',
                        '\x0A',
                        'KRA 16 # 5-04\x0A',
                        'ALTO DEL ROSARIO\x0A',
                        'REG CASIO SE-800-0303888\x0A',
                        '\x0A',
                        '\x1B' + '\x61' + '\x30', // Left alignment
                        'REG- ' + new Date().toISOString().slice(0, 10) + ' ' + order.id.toString().padStart(4,
                            '0') + ' ' + (Math.floor(Math.random() * 900000) + 100000) + '\x0A',
                        '\x0A'
                    ];

                    if (order.items && order.items.length > 0) {
                        order.items.forEach(item => {
                            let qty = item.quantity.toString().padStart(2, ' ');
                            let name = item.product.name.substring(0, 18).toUpperCase().padEnd(18, ' ');
                            let price = parseInt(item.subtotal).toLocaleString('es-CO').padStart(8, ' ');
                            data.push(qty + ' ' + name + ' ' + price + '\x0A');
                        });
                    } else {
                        // For fast sales
                        let price = parseInt(order.total).toLocaleString('es-CO').padStart(8, ' ');
                        data.push(' 1 VENTA DIRECTA     ' + price + '\x0A');
                    }

                    data.push('\x0A');
                    data.push('   TL                ' + parseInt(order.total).toLocaleString('es-CO').padStart(8, ' ') +
                        '\x0A');
                    data.push('\x0A');
                    data.push('   CAJA\x0A');
                    data.push('\x0A\x0A\x0A\x0A\x0A\x1B\x69'); // Paper cut

                    return qz.print(config, data);
                }).catch(function(e) {
                    console.error(e);
                }).finally(function() {
                    qz.websocket.disconnect();
                });
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha256/0.9.0/sha256.min.js"></script>
    @endpush

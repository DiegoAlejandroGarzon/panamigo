<div x-data="{
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

    // ... remaining state ...
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
            const input = document.getElementById('qtyInput');
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
        $wire.sendToCashier(this.cart, this.total);
    },

    init() {
        window.addEventListener('order-sent', () => {
            this.cart = [];
            this.total = 0;
            this.isCartOpen = false;
        });
    }
}" class="flex flex-col bg-slate-100 p-0 md:p-2 relative">

    <!-- Filters Header (Sticky) -->
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

    <!-- Main Content Area -->
    <div class="flex flex-col lg:flex-row gap-2 px-1">
        <!-- Products Grid -->
        <div class="w-full lg:w-2/3 flex flex-col">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 overflow-y-auto pr-1 pb-20"
                style="max-height: calc(100vh - 110px); scrollbar-width: thin;">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="addItem(product)"
                        class="box cursor-pointer hover:shadow-lg transition transform active:scale-95 p-2 flex flex-col items-center text-center rounded-xl"
                        :class="product.name.toUpperCase() == 'PAN' ? 'border-2 border-primary bg-primary/5' : 'bg-white'">
                        <div
                            class="w-full aspect-square bg-slate-100 rounded-lg mb-1 flex items-center justify-center text-slate-300 overflow-hidden relative">
                            <template x-if="product.provisional_image">
                                <img :src="product.provisional_image" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!product.provisional_image">
                                <x-base.lucide icon="Image" class="w-8 h-8 opacity-20" />
                            </template>
                            <template x-if="product.name.toUpperCase() == 'PAN'">
                                <div class="absolute top-1 right-1">
                                    <span
                                        class="bg-primary text-white text-[8px] px-1.5 py-0.5 rounded-full font-bold uppercase">Variado</span>
                                </div>
                            </template>
                        </div>
                        <h3 class="font-extrabold text-[13px] text-slate-900 leading-tight h-8 overflow-hidden uppercase"
                            x-text="product.name"></h3>
                        <div class="text-success font-black text-xs mt-1 bg-success/10 px-2 py-0.5 rounded-full border border-success/20"
                            x-text="product.name.toUpperCase() == 'PAN' ? 'VARIAR PRECIO' : '$' + new Intl.NumberFormat().format(product.price)">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Mobile Cart Toggle -->
        <div class="fixed bottom-4 right-4 lg:hidden z-50">
            <button @click="isCartOpen = !isCartOpen"
                class="bg-primary text-white p-4 rounded-full shadow-2xl flex items-center gap-2 font-bold animate-bounce">
                <x-base.lucide icon="ShoppingCart" class="w-6 h-6" />
                <span class="text-xs bg-white text-primary px-2 py-1 rounded-full text-center min-w-[20px]"
                    x-text="cart.length"></span>
            </button>
        </div>

        <!-- Cart Sidebar -->
        <div class="w-full lg:w-1/3 lg:flex flex-col pt-1 lg:pt-0"
            :class="isCartOpen ? 'fixed inset-0 z-[100] bg-white lg:relative lg:bg-transparent lg:z-10' : 'hidden lg:flex'">
            <div class=" box flex flex-col h-full overflow-hidden shadow-2xl lg:shadow-xl border-l border-slate-200">
                <div class="p-4 bg-primary text-white font-bold flex justify-between items-center lg:rounded-t-lg">
                    <span class="flex items-center"><x-base.lucide icon="ShoppingCart" class="mr-2 h-5 w-5" /> Pedido
                        Actual</span>
                    <div class="flex items-center gap-3">
                        <span class="text-xl" x-text="'$' + new Intl.NumberFormat().format(total)">$0</span>
                        <button @click="isCartOpen = false" class="lg:hidden">
                            <x-base.lucide icon="X" class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-2 min-h-[300px] bg-white text-slate-800">
                    <template x-for="(item, index) in cart" :key="index">
                        <div
                            class=" flex justify-between items-center border border-slate-100 p-2 rounded-lg bg-slate-50 shadow-sm">
                            <div class="flex-1 pr-2">
                                <div class="font-black text-slate-800 text-[13px] leading-tight uppercase"
                                    x-text="item.name"></div>
                                <div class="text-[11px] text-slate-500 font-bold mt-1">
                                    <span class="text-primary"
                                        x-text="'$' + new Intl.NumberFormat().format(item.price)"></span> x <span
                                        x-text="item.quantity"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <template x-if="!item.is_pan">
                                    <div class="flex items-center gap-1">
                                        <button @click="decrease(item.id, false)"
                                            class="w-7 h-7 bg-white border border-slate-200 rounded flex items-center justify-center font-bold text-red-500">-</button>
                                        <span class="font-black text-sm w-5 text-center text-slate-700"
                                            x-text="item.quantity"></span>
                                        <button @click="increase(item.id, false)"
                                            class="w-7 h-7 bg-white border border-slate-200 rounded flex items-center justify-center font-bold text-green-500">+</button>
                                    </div>
                                </template>
                                <template x-if="item.is_pan">
                                    <div class="flex items-center">
                                        <span
                                            class="text-[9px] bg-primary/10 text-primary border border-primary/20 px-2 py-1 rounded-full font-bold">ABIERTO</span>
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
                        class="flex flex-col items-center justify-center text-slate-300 mt-20 pt-10 border-t border-dashed border-slate-200">
                        <x-base.lucide icon="ShoppingBag" class="w-20 h-20 mb-4 opacity-10" />
                        <p class="font-bold text-lg">El carrito está vacío</p>
                        <p class="text-xs uppercase mt-1">Agrega productos para comenzar</p>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-200 bg-slate-100">
                    @if (session()->has('message'))
                        <div
                            class="alert alert-success mt-1 mb-3 text-center py-2 text-xs font-bold text-white bg-green-500 rounded-lg">
                            {{ session('message') }}
                        </div>
                    @endif

                    <button @click="submitOrder()" :disabled="cart.length === 0"
                        :class="cart.length > 0 ? 'bg-primary border-primary text-white shadow-xl' :
                            'bg-slate-300 border-slate-300 text-slate-500 cursor-not-allowed'"
                        class="w-full h-14 text-xl font-black rounded-xl flex items-center justify-center gap-3 transition-all">
                        <x-base.lucide icon="Send" class="w-6 h-6" />
                        ENVIAR A CAJA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Select Quantity Modal -->
    <div x-show="showQtyModal"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md" x-cloak>
        <div class=" box w-full max-w-sm bg-white shadow-2xl rounded-2xl overflow-hidden border border-white/20">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center text-primary bg-slate-50">
                <h2 class="font-black uppercase tracking-tight" x-text="tempProduct ? tempProduct.name : ''"></h2>
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
                    <input id="qtyInput" type="number" x-model="tempQty"
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

    <!-- Pan Modal (Manual Price) -->
    <div x-show="showPanModal"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md" x-cloak>
        <div class=" box w-full max-w-sm bg-white shadow-2xl rounded-2xl overflow-hidden border border-white/20">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center text-primary bg-slate-50">
                <h2 class="font-black uppercase tracking-tight">CANTIDAD EN PESOS ($)</h2>
                <button @click="showPanModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <x-base.lucide icon="X" class="w-6 h-6" />
                </button>
            </div>
            <form @submit.prevent="addPan()" class="p-6">
                <label class="font-black text-[10px] uppercase text-slate-400 mb-2 block tracking-widest">Digite el
                    total vendido</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-black text-slate-300">$</span>
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

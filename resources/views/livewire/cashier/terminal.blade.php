<div class="intro-y grid grid-cols-12 gap-6 mt-5">
    <!-- List of Pending Orders -->
    <div class="col-span-12 lg:col-span-4 overflow-y-auto" style="max-height: 80vh;">
        <div class="intro-y box p-5">
            <h2 class="font-medium text-base mb-4 flex items-center">
                <x-base.lucide icon="ClipboardList" class="mr-2 w-5 h-5 text-primary" /> Pedidos Pendientes
            </h2>
            <div class="space-y-3">
                @forelse($pendingOrders as $order)
                    <div wire:click="selectOrder({{ $order->id }})" 
                         class="intro-x cursor-pointer p-4 rounded-lg border {{ $selectedOrder && $selectedOrder->id == $order->id ? 'bg-primary text-white border-primary shadow-lg' : 'bg-white border-slate-200 hover:bg-slate-50' }} transition-all duration-200">
                        <div class="flex justify-between items-center">
                            <span class="font-bold">#{{ $order->id }}</span>
                            <span class="text-xs {{ $selectedOrder && $selectedOrder->id == $order->id ? 'text-white/70' : 'text-slate-500' }}">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="{{ $selectedOrder && $selectedOrder->id == $order->id ? 'text-white/80' : 'text-slate-600' }}">{{ $order->items->count() }} items</span>
                            <span class="font-bold {{ $selectedOrder && $selectedOrder->id == $order->id ? 'text-white' : 'text-success' }}">${{ number_format($order->total, 2) }}</span>
                        </div>
                        <div class="text-xs {{ $selectedOrder && $selectedOrder->id == $order->id ? 'text-white/60' : 'text-slate-400' }} mt-1">
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
            @if($selectedOrder)
                <div class="p-5 border-b border-slate-200 flex justify-between items-center bg-slate-50 rounded-t-lg">
                    <h2 class="font-medium text-xl">Detalle del Pedido <span class="text-primary">#{{ $selectedOrder->id }}</span></h2>
                    <span class="px-3 py-1 rounded-full bg-warning/20 text-warning text-xs font-bold uppercase tracking-wider">Pendiente de Pago</span>
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
                            @foreach($selectedOrder->items as $item)
                            <x-base.table.tr>
                                <x-base.table.td class="py-4 border-b border-slate-100">
                                    <div class="font-medium text-slate-700">{{ $item->product->name }}</div>
                                </x-base.table.td>
                                <x-base.table.td class="py-4 text-center border-b border-slate-100 text-slate-600">{{ $item->quantity }}</x-base.table.td>
                                <x-base.table.td class="py-4 text-right border-b border-slate-100 text-slate-600">${{ number_format($item->product->price, 2) }}</x-base.table.td>
                                <x-base.table.td class="py-4 text-right font-bold border-b border-slate-100 text-slate-700">${{ number_format($item->subtotal, 2) }}</x-base.table.td>
                            </x-base.table.tr>
                            @endforeach
                        </x-base.table.tbody>
                    </x-base.table>
                    
                    <div class="mt-10 flex flex-col items-end px-5">
                        <div class="text-slate-500 font-medium">TOTAL A PAGAR</div>
                        <div class="text-3xl font-bold text-success mt-1">${{ number_format($selectedOrder->total, 2) }}</div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-200 bg-slate-50 rounded-b-lg flex justify-end items-center">
                    @if (session()->has('message'))
                        <div class="mr-auto text-success font-medium italic">
                            {{ session('message') }}
                        </div>
                    @endif
                    <x-base.button wire:click="markAsPaid" variant="success" size="lg" class="px-10 shadow-lg flex items-center gap-2 text-white">
                        <x-base.lucide icon="Printer" class="w-5 h-5" /> COBRAR E IMPRIMIR
                    </x-base.button>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-slate-400">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <x-base.lucide icon="ShoppingCart" class="w-12 h-12 opacity-20" />
                    </div>
                    <p class="text-lg font-medium">Selecciona un pedido para procesar</p>
                    <p class="text-sm opacity-60 mt-1">Los pedidos pendientes aparecerán en la lista de la izquierda</p>
                </div>
            @endif
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
    });

    function printTicket(order) {
        if (typeof qz === 'undefined') {
            console.error('QZ Tray library not loaded!');
            return;
        }

        qz.websocket.connect().then(function() {
            return qz.printers.find("XP-58"); 
        }).then(function(printer) {
            var config = qz.configs.create(printer);
            
            var data = [
                '\x1B' + '\x40',          
                '\x1B' + '\x61' + '\x31', 
                'JAKI-PAN POS\x0A',
                'Ticket #' + order.id + '\x0A',
                '\x0A',
                '\x1B' + '\x61' + '\x30', 
                'Fecha: ' + new Date().toLocaleString() + '\x0A',
                '--------------------------------\x0A'
            ];

            order.items.forEach(item => {
                data.push(item.product.name + ' x' + item.quantity + '\x0A');
                data.push('                    $' + item.subtotal + '\x0A');
            });

            data.push('--------------------------------\x0A');
            data.push('TOTAL: $' + order.total + '\x0A');
            data.push('\x0A\x0A\x0A\x0A\x0A\x1B\x69'); 

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


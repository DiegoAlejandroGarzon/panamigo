<div class="grid grid-cols-12 gap-6 h-screen p-5 bg-gray-100">
    <!-- List of Pending Orders -->
    <div class="col-span-12 md:col-span-4 bg-white rounded-lg shadowoverflow-y-auto">
        <div class="p-5 border-b border-gray-200">
            <h2 class="font-medium text-base mr-auto">Pedidos Pendientes</h2>
        </div>
        <div class="p-2">
            @forelse($pendingOrders as $order)
                <div wire:click="selectOrder({{ $order->id }})" 
                     class="cursor-pointer p-4 mb-2 rounded border {{ $selectedOrder && $selectedOrder->id == $order->id ? 'bg-blue-50 border-blue-500' : 'bg-white border-gray-200 hover:bg-gray-50' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-bold">#{{ $order->id }}</span>
                        <span class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-600">{{ $order->items->count() }} items</span>
                        <span class="font-bold text-green-600">${{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        Atendido por: {{ $order->user->name ?? 'N/A' }}
                    </div>
                </div>
            @empty
                <div class="text-center p-10 text-gray-400">No hay pedidos pendientes.</div>
            @endforelse
        </div>
    </div>

    <!-- Order Details -->
    <div class="col-span-12 md:col-span-8 bg-white rounded-lg shadow flex flex-col">
        @if($selectedOrder)
            <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                <h2 class="font-medium text-xl">Detalle del Pedido #{{ $selectedOrder->id }}</h2>
                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm font-bold">Pendiente de Pago</span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-5">
                <table class="table w-full">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="pb-2">Producto</th>
                            <th class="pb-2 text-center">Cant.</th>
                            <th class="pb-2 text-right">Precio</th>
                            <th class="pb-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedOrder->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">{{ $item->product->name }}</td>
                            <td class="py-2 text-center">{{ $item->quantity }}</td>
                            <td class="py-2 text-right">${{ number_format($item->product->price, 2) }}</td>
                            <td class="py-2 text-right font-medium">${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="pt-4 text-right font-bold text-lg">TOTAL:</td>
                            <td class="pt-4 text-right font-bold text-lg text-green-600">${{ number_format($selectedOrder->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end items-center">
                <button wire:click="markAsPaid" class="btn btn-success p-3 text-lg w-48 shadow-lg flex justify-center items-center gap-2">
                    <i data-lucide="printer" class="w-6 h-6"></i> Cobrar e Imprimir
                </button>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <i data-lucide="shopping-cart" class="w-16 h-16 mb-4 opacity-50"></i>
                <p>Selecciona un pedido para ver los detalles.</p>
            </div>
        @endif
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

    // QZ Tray Logic placeholder (Assuming printer.js is loaded or we put logic here if simpler)
    function printTicket(order) {
        // Build ESC/POS commands
        // Connect to QZ Tray
        // Print
        if (typeof qz === 'undefined') {
            alert('QZ Tray library not loaded!');
            return;
        }

        // Example QZ Tray connection and printing (simplified)
        qz.websocket.connect().then(function() {
            return qz.printers.find("XP-58"); // Find our printer
        }).then(function(printer) {
            var config = qz.configs.create(printer);
            
            // Generate ESC/POS data
            var data = [
                '\x1B' + '\x40',          // Init
                '\x1B' + '\x61' + '\x31', // Center align
                'JAKI-PAN POS\x0A',
                'Ticket #' + order.id + '\x0A',
                '\x0A',
                '\x1B' + '\x61' + '\x30', // Left align
                'Fecha: ' + new Date().toLocaleString() + '\x0A',
                '--------------------------------\x0A'
            ];

            order.items.forEach(item => {
                data.push(item.product.name + ' x' + item.quantity + '  $' + item.subtotal + '\x0A');
            });

            data.push('--------------------------------\x0A');
            data.push('TOTAL: $' + order.total + '\x0A');
            data.push('\x0A\x0A\x0A\x0A\x0A\x1B\x69'); // Cut paper (if supported) or extra feed

            return qz.print(config, data);
        }).catch(function(e) {
            console.error(e);
            alert('Error printing: ' + e);
        }).finally(function() {
            qz.websocket.disconnect();
        });
    }
</script>
<!-- Load QZ Tray JS (Using CDN for simplicity or local resource) -->
<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha256/0.9.0/sha256.min.js"></script>
@endpush

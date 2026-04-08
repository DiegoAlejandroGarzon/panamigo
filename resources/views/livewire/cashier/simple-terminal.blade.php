<div x-data="{ showZModal: false }">
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto flex items-center gap-4">
            Caja Simple - Ventas Rápidas
            <div class="hidden sm:flex items-center gap-2 bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-sm"
                x-data="{ time: new Date().toLocaleTimeString('es-CO', { timeStyle: 'medium' }) }" x-init="setInterval(() => time = new Date().toLocaleTimeString('es-CO', { timeStyle: 'medium' }), 1000)">
                <x-base.lucide icon="Clock" class="w-4 h-4 text-slate-500" />
                <span class="font-bold text-sm tracking-wider text-slate-700" x-text="time"></span>
            </div>
        </h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2">
            <x-base.button
                onclick="const nav = document.querySelector('.side-nav'); if(nav) nav.style.display = nav.style.display === 'none' ? '' : 'none';"
                variant="outline-primary" class="shadow-md flex items-center gap-2" title="Pantalla Completa">
                <x-base.lucide icon="Maximize" class="w-4 h-4" />
            </x-base.button>
            <x-base.button @click="showZModal = true" variant="primary" class="shadow-md flex items-center gap-2">
                <x-base.lucide icon="FileText" class="w-4 h-4" /> REPORTE Z
            </x-base.button>
            <x-base.button wire:click="openDrawerOnly" variant="outline-secondary"
                class="shadow-md flex items-center gap-2">
                <x-base.lucide icon="Archive" class="w-4 h-4" /> ABRIR CAJÓN
            </x-base.button>
            <div class="flex items-center bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm">
                <span class="mr-3 text-xs font-bold text-slate-600 uppercase">Imprimir Ticket:</span>
                <div class="form-check form-switch ps-0">
                    <input class="form-check-input" type="checkbox" role="switch" wire:model.live="shouldPrint">
                </div>
            </div>
        </div>
    </div>

    <!-- Z Report Modal -->
    <div x-show="showZModal" style="display: none"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-transition>
        <div class="intro-y box w-full max-w-md bg-white shadow-2xl rounded-xl overflow-hidden"
            @click.away="showZModal = false">
            <div class="p-5 border-b border-slate-200 flex justify-between items-center">
                <h2 class="font-bold text-lg">GENERAR REPORTE Z (DIARIO)</h2>
                <button @click="showZModal = false" class="text-slate-400 hover:text-slate-600"><x-base.lucide
                        icon="X" class="w-6 h-6" /></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="form-label font-bold text-xs uppercase text-slate-500">Fecha del Reporte</label>
                    <input type="date" wire:model="zDate" class="form-control">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-base.button wire:click="loadRealZData" variant="outline-primary" class="w-full text-xs py-3">
                        <x-base.lucide icon="RefreshCw" class="w-4 h-4 mr-2" /> VALORES REALES (BD)
                    </x-base.button>
                    <x-base.button wire:click="generateRandomZData" variant="outline-pending"
                        class="w-full text-xs py-3 font-bold">
                        <x-base.lucide icon="Shuffle" class="w-4 h-4 mr-2" /> VALORES ALEATORIOS
                    </x-base.button>
                </div>

                <div class="text-xs text-slate-400 italic text-center border-b pb-2">
                    Ingresa los valores manualmente o usa los botones de carga rápida.
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="form-label font-bold text-xs uppercase text-slate-500">Total Venta ($)</label>
                        <input type="number" wire:model="zTotal" class="form-control text-xl font-bold w-full">
                    </div>
                    <div class="flex-1">
                        <label class="form-label font-bold text-xs uppercase text-slate-500">Personas (Count)</label>
                        <input type="number" wire:model="zCount" class="form-control text-xl font-bold w-full">
                    </div>
                </div>
            </div>
            <div class="p-5 border-t border-slate-200 flex justify-end gap-2 bg-slate-50 rounded-b-xl">
                <x-base.button @click="showZModal = false" variant="outline-secondary">CANCELAR</x-base.button>
                <x-base.button wire:click="printZReport" @click="showZModal = false" variant="success"
                    class="text-white px-8">
                    <x-base.lucide icon="Printer" class="w-4 h-4 mr-2" /> IMPRIMIR Z
                </x-base.button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Main POS Area -->
        <div class="col-span-12 lg:col-span-8">
            <div class="intro-y box p-8 bg-primary text-white shadow-xl rounded-xl">
                <div class="flex flex-col gap-6">
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">VENTA RÁPIDA (VALOR DIRECTO)</h3>
                        <p class="text-white/70 text-sm mt-1">Ingresa el total y presiona cobrar para registrar una
                            venta inmediata en caja.</p>
                    </div>

                    <div class="flex flex-col md:flex-row items-center gap-4 mt-2">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <span class="text-slate-800/40 text-4xl font-black">$</span>
                            </div>
                            <input type="number" step="0.01" wire:model="fastAmount"
                                class="form-control pl-10 py-6 text-6xl font-black text-slate-800 shadow-inner rounded-2xl tracking-tighter w-full"
                                placeholder="0" autofocus>
                        </div>
                        <x-base.button wire:click="processFastSale" variant="success" size="lg"
                            class="w-full md:w-auto px-10 py-6 text-white font-black text-xl shadow-xl flex items-center justify-center gap-3 rounded-2xl transform hover:scale-105 transition-all">
                            <x-base.lucide icon="CheckCircle" class="w-8 h-8" /> COBRAR
                        </x-base.button>
                    </div>
                    @error('fastAmount')
                        <span
                            class="text-white bg-red-500 px-3 py-2 rounded-lg text-sm font-bold inline-block">{{ $message }}</span>
                    @enderror

                    <!-- Quick buttons -->
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-xs uppercase tracking-widest text-white/50 mb-3 font-bold">Atajos de Efectivo
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <button @click="$wire.fastAmount = parseFloat($wire.fastAmount || 0) + 2000"
                                class="bg-white/10 hover:bg-white/20 border border-white/20 transition-all rounded-xl py-3 text-lg font-bold flex items-center justify-center gap-1"><span
                                    class="text-white/50 font-normal">$</span>2.000</button>
                            <button @click="$wire.fastAmount = parseFloat($wire.fastAmount || 0) + 5000"
                                class="bg-white/10 hover:bg-white/20 border border-white/20 transition-all rounded-xl py-3 text-lg font-bold flex items-center justify-center gap-1"><span
                                    class="text-white/50 font-normal">$</span>5.000</button>
                            <button @click="$wire.fastAmount = parseFloat($wire.fastAmount || 0) + 10000"
                                class="bg-white/10 hover:bg-white/20 border border-white/20 transition-all rounded-xl py-3 text-lg font-bold flex items-center justify-center gap-1"><span
                                    class="text-white/50 font-normal">$</span>10.000</button>
                            <button @click="$wire.fastAmount = parseFloat($wire.fastAmount || 0) + 20000"
                                class="bg-white/10 hover:bg-white/20 border border-white/20 transition-all rounded-xl py-3 text-lg font-bold flex items-center justify-center gap-1"><span
                                    class="text-white/50 font-normal">$</span>20.000</button>
                            <button @click="$wire.fastAmount = parseFloat($wire.fastAmount || 0) + 50000"
                                class="bg-white/10 hover:bg-white/20 border border-white/20 transition-all rounded-xl py-3 text-lg font-bold flex items-center justify-center gap-1"><span
                                    class="text-white/50 font-normal">$</span>50.000</button>
                            <button @click="$wire.fastAmount = parseFloat($wire.fastAmount || 0) + 100000"
                                class="bg-white/10 hover:bg-white/20 border border-white/20 transition-all rounded-xl py-3 text-lg font-bold flex items-center justify-center gap-1"><span
                                    class="text-white/50 font-normal">$</span>100.000</button>
                            <button @click="$wire.fastAmount = ''"
                                class="bg-danger/80 hover:bg-danger text-white border border-danger transition-all rounded-xl py-3 text-lg font-bold col-span-2 md:col-span-2">LIMPIAR</button>
                        </div>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <x-base.alert variant="soft-success" class="flex items-center mt-5 shadow-sm">
                    <x-base.lucide icon="CheckCircle" class="w-6 h-6 mr-2" />
                    <span class="font-bold">{{ session('message') }}</span>
                </x-base.alert>
            @endif
        </div>

        <!-- History Area -->
        <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
            <div class="intro-y box p-5 shadow-sm border-t-4 border-success flex items-center justify-between">
                <div>
                    <h3 class="text-slate-500 text-xs font-bold uppercase tracking-widest">Ventas de Hoy</h3>
                    <div class="text-2xl font-black mt-1">
                        ${{ number_format(\App\Models\Order::whereDate('created_at', date('Y-m-d'))->where('status', 'paid')->sum('total'), 2) }}
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-success/10 text-success flex items-center justify-center">
                    <x-base.lucide icon="TrendingUp" class="w-6 h-6" />
                </div>
            </div>

            <div class="intro-y box p-5 flex-1 shadow-sm">
                <h3 class="text-slate-600 font-bold mb-4 flex items-center pb-3 border-b border-slate-100">
                    <x-base.lucide icon="History" class="w-5 h-5 mr-2" /> Historial Ultra-Reciente
                </h3>
                <div class="space-y-3">
                    @forelse($recentSales as $sale)
                        <div
                            class="flex items-center justify-between p-3 rounded-lg border border-slate-100 bg-slate-50">
                            <div>
                                <div class="font-bold text-slate-800 text-sm">Venta #{{ $sale->id }}</div>
                                <div class="text-xs text-slate-500">{{ $sale->created_at->format('h:i A') }}</div>
                            </div>
                            <div class="font-black text-success text-lg">
                                ${{ number_format($sale->total, 2) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-5 text-slate-400">
                            <x-base.lucide icon="Inbox" class="w-10 h-10 mx-auto opacity-20 mb-2" />
                            <p class="text-sm">Sin ventas recientes</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            console.log("printTicket");
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

            // Detección de dispositivo móvil (Tablet/Celular)
            function isMobileOrTablet() {
                return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i
                    .test(navigator.userAgent || navigator.vendor || window.opera);
            }

            // Función genérica para enviar RAW a RawBT
            function printWithRawBT(dataArray) {
                try {
                    // RawBT acepta comandos de escape en texto plano concatenado o base64
                    let printData = "";
                    for (let i = 0; i < dataArray.length; i++) {
                        printData += dataArray[i];
                    }

                    // Codificar la URL (btoa falla si hay caracteres fuera de latin1, por lo que usamos encodeURI / JS escape)
                    let base64Data = btoa(unescape(encodeURIComponent(printData)));

                    // El formato correcto de intent para RawBT para comandos ESC/POS requiere el prefijo base64,
                    let intentUrl = "intent:base64," + base64Data + "#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;";

                    console.log("Redirecting to: " + intentUrl.substring(0, 50) + "..."); // Solo vemos un pedacito en PC

                    window.location.href = intentUrl;
                } catch (error) {
                    alert("Error armando la data para la impresora: " + error.message);
                }
            }

            function openDrawer() {
                // Si el usuario simplemente quiere registrar sin abrir nada (silencioso), salimos
                if (!@js($shouldPrint)) {
                    console.log("Silent mode: skipping drawer/printer.");
                    return;
                }

                var data = ['\x1B\x70\x00\x19\xFA'];

                if (isMobileOrTablet()) {
                    console.log("Using RawBT for Drawer");
                    printWithRawBT(data);
                    return;
                }

                if (typeof qz === 'undefined') return;

                qz.websocket.connect().then(function() {
                    return qz.printers.find(PRINTER_NAME);
                }).then(function(printer) {
                    var config = qz.configs.create(printer);
                    return qz.print(config, data);
                }).catch(e => console.error(e)).finally(() => qz.websocket.disconnect());
            }

            function printZReport(z) {
                var count = (z.count || 0).toString().padStart(4, ' ');
                var total = parseInt(z.total || 0).toLocaleString('es-CO').padStart(10, ' ');
                var formattedDate = z.date || new Date().toISOString().slice(0, 10);

                var data = [
                    'KRA 16 # 5-04\x0A',
                    'ALTO DEL ROSARIO\x0A',
                    'REG CASIO SE-800-0303888\x0A\x0A',
                    'Z   ' + formattedDate + '   3888 387625\x0A',
                    '--------------------------------\x0A',
                    'Z DAIARIO\x0A',
                    '--------------------------------\x0A',
                    '\x1B' + '\x61' + '\x30',
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
                    '\x0A\x0A\x0A\x0A\x0A\x1B\x69'
                ];

                if (isMobileOrTablet()) {
                    console.log("Using RawBT for Z Report");
                    printWithRawBT(data);
                    return true;
                }

                if (typeof qz === 'undefined') return;

                qz.websocket.connect().then(function() {
                    return qz.printers.find(PRINTER_NAME);
                }).then(function(printer) {
                    var config = qz.configs.create(printer);
                    return qz.print(config, data);
                }).catch(e => console.error(e)).finally(() => qz.websocket.disconnect());
            }


            function printTicket(order) {
                // Just redundancy check
                if (!@js($shouldPrint)) return;

                var data = [
                    '\x1B' + '\x40',
                    '\x1B' + '\x70' + '\x00' + '\x19' + '\xFA',
                    '\x1B' + '\x61' + '\x31',
                    '\x1B' + '\x21' + '\x08',
                    'JAKI - PAN Y SUS DELICIAS\x0A',
                    '\x1B' + '\x21' + '\x00',
                    'JACQUELINE NOVOA URREGO\x0A',
                    'NIT. 39.628.435-9\x0A',
                    'REGIMEN NO RESPONSABLE DE IVA\x0A',
                    '\x0A',
                    'KRA 16 # 5-04\x0A',
                    'ALTO DEL ROSARIO\x0A',
                    'REG CASIO SE-800-0303888\x0A',
                    '\x0A',
                    '\x1B' + '\x61' + '\x30',
                    'REG- ' + new Date().toISOString().slice(0, 10) + ' ' + order.id.toString()
                    .padStart(4,
                        '0') + ' ' + (Math.floor(Math.random() * 900000) + 100000) + '\x0A',
                    '\x0A'
                ];

                let price = parseInt(order.total).toLocaleString('es-CO').padStart(8, ' ');
                data.push(' 1 VENTA DIRECTA     ' + price + '\x0A');

                data.push('\x0A');
                data.push('   TL                ' + price + '\x0A');
                data.push('\x0A');
                data.push('   CAJA\x0A');
                data.push('\x0A\x0A\x0A\x0A\x0A\x1B\x69');

                if (isMobileOrTablet()) {
                    console.log("Using RawBT for Ticket");
                    printWithRawBT(data);
                    return;
                }

                if (typeof qz === 'undefined') {
                    console.error('QZ Tray library not loaded!');
                    return;
                }

                qz.websocket.connect().then(function() {
                    return qz.printers.find(PRINTER_NAME);
                }).then(function(printer) {
                    var config = qz.configs.create(printer);
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
</div>

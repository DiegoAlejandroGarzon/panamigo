<div class="px-5 pb-8 relative">
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-2xl font-black mr-auto tracking-tight uppercase text-slate-800">
            <span class="text-primary">📊</span> Dashboard Simple - Resumen de Operación
        </h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-3">
            <x-base.button wire:click="$refresh" variant="outline-slate"
                class="bg-white shadow-sm flex items-center gap-2">
                <x-base.lucide icon="RefreshCw" class="w-4 h-4" /> REFRESCAR
            </x-base.button>
            <x-base.button wire:click="generateZReport" variant="primary"
                class="shadow-lg flex items-center gap-2 px-4 py-2 font-bold transition-all hover:translate-y-[-2px] active:translate-y-[1px]"
                onclick="confirm('¿Deseas generar el Reporte Z (Factura de Cierre) ahora?') || event.stopImmediatePropagation()">
                <x-base.lucide icon="FileCheck" class="w-5 h-5" /> GENERAR FACTURA Z
            </x-base.button>
        </div>
    </div>

    <!-- Notifications -->
    @if (session()->has('message'))
        <div class="intro-y mt-5">
            <x-base.alert variant="soft-success" class="flex items-center mb-6 shadow-sm border-l-4 border-success">
                <x-base.lucide icon="CheckCircle" class="w-6 h-6 mr-2" />
                <span class="font-bold">{{ session('message') }}</span>
                <x-base.alert.dismiss-button class="text-white" />
            </x-base.alert>
        </div>
    @endif

    <!-- Pending Closure Section -->
    <div class="intro-y mt-5 mb-8">
        <div
            class="bg-gradient-to-br from-primary to-primary-focus p-6 rounded-2xl border border-primary/20 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-white/20 text-white rounded-2xl flex items-center justify-center backdrop-blur-md">
                    <x-base.lucide icon="Archive" class="w-8 h-8" />
                </div>
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-widest">CIERRE DE CAJA PENDIENTE</h3>
                    <p class="text-white/80 font-medium">Hay <span
                            class="text-white font-black underline underline-offset-4 decoration-2 decoration-white/30">{{ $pendingZCount }}</span>
                        tickets listos para ser totalizados.</p>
                </div>
            </div>
            <div class="flex items-center gap-8">
                <div class="text-right">
                    <div class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">Acumulado a Cerrar
                    </div>
                    <div class="text-3xl font-black text-white tracking-tighter">
                        ${{ number_format($pendingZTotal, 0, ',', '.') }}</div>
                </div>
                <div class="text-right border-l border-white/20 pl-8">
                    <div class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">Próximo
                        Identificador</div>
                    <div class="text-3xl font-black text-white/40">Z-{{ $nextZNumber }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-slate-100 text-primary mb-6">
                    <x-base.lucide icon="DollarSign" class="w-6 h-6" />
                </div>
                <div class="text-3xl font-black text-slate-700 tracking-tighter">
                    ${{ number_format($totalSales, 0, ',', '.') }}</div>
                <div class="text-[10px] font-black text-slate-400 mt-1 uppercase tracking-widest">Total Ventas Hoy</div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-slate-100 text-pending mb-6">
                    <x-base.lucide icon="Package" class="w-6 h-6" />
                </div>
                <div class="text-3xl font-black text-slate-700 tracking-tighter">{{ $totalCount }}</div>
                <div class="text-[10px] font-black text-slate-400 mt-1 uppercase tracking-widest">Volumen tickets</div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-slate-100 text-warning mb-6">
                    <x-base.lucide icon="TrendingUp" class="w-6 h-6" />
                </div>
                <div class="text-3xl font-black text-slate-700 tracking-tighter">
                    ${{ number_format($averageTicket, 0, ',', '.') }}</div>
                <div class="text-[10px] font-black text-slate-400 mt-1 uppercase tracking-widest">Ticket Promedio</div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-slate-100 text-success mb-6">
                    <x-base.lucide icon="Zap" class="w-6 h-6" />
                </div>
                <div class="text-xl font-black text-slate-700 tracking-tighter uppercase truncate">{{ $peakHour }}
                </div>
                <div class="text-[10px] font-black text-slate-400 mt-1 uppercase tracking-widest">Hora de Mayor Venta
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Charts & History -->
    <div class="grid grid-cols-12 gap-6 mt-10">
        <div class="col-span-12 lg:col-span-8 intro-y box p-6 rounded-3xl shadow-lg bg-white">
            <div class="flex items-center gap-3 border-b pb-5 mb-5 border-slate-100">
                <div class="w-2 h-8 bg-primary rounded-full"></div>
                <h3 class="text-slate-800 text-xl font-black uppercase tracking-tight">Evolución Diaria</h3>
            </div>
            <div class="h-[400px] mt-6 w-full" wire:ignore>
                <canvas id="simple-dashboard-chart"></canvas>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4 intro-y box p-6 rounded-3xl shadow-lg bg-white">
            <div class="flex items-center gap-3 border-b pb-5 mb-5 border-slate-100">
                <div class="w-2 h-8 bg-pending rounded-full"></div>
                <h3 class="text-slate-800 text-xl font-black uppercase tracking-tight">RECIENTES Z</h3>
            </div>

            <div class="space-y-3">
                @forelse($zReports as $report)
                    <div wire:click="viewZReport({{ $report->id }})"
                        class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-200/50 transition-all cursor-pointer group flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-black text-primary/60 mb-1">FACTURA Z {{ $report->z_number }}
                            </div>
                            <div class="text-lg font-black text-slate-800 tracking-tighter">
                                ${{ number_format($report->total_sales, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase">
                                {{ $report->report_date->format('d M, Y') }}</div>
                        </div>
                        <div
                            class="bg-white p-2 rounded-lg shadow-sm group-hover:bg-primary group-hover:text-white transition-colors">
                            <x-base.lucide icon="Eye" class="w-4 h-4" />
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 text-slate-300">
                        <x-base.lucide icon="FileText" class="w-12 h-12 mx-auto mb-2 opacity-20" />
                        <p class="font-bold text-xs uppercase tracking-widest">Sin registros</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Z REPORTE TICKET MODAL -->
    @if ($showZModal && $selectedZReport)
        <div
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-300">
            <div
                class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl overflow-hidden relative animate-in zoom-in duration-300">
                <!-- Close Button -->
                <button wire:click="closeZModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                    <x-base.lucide icon="X" class="w-6 h-6" />
                </button>

                <!-- Physical Ticket Style Content -->
                <div class="p-8 max-h-[85vh] overflow-y-auto font-mono text-slate-800">
                    <div class="text-center space-y-1 mb-8">
                        <h2 class="text-xl font-black uppercase tracking-tight">JAKI- PAN Y SUS DELICIAS</h2>
                        <p class="text-xs font-bold uppercase">JACQUELINE NOVOA URREGO</p>
                        <p class="text-xs">NIT. 39.628.435-9</p>
                        <p class="text-xs font-bold">REGIMEN NO RESPONSABLE DE IVA</p>
                        <p class="text-xs">KRA 16 # 5-04</p>
                        <p class="text-xs">ALTO DEL ROSARIO</p>
                        <p class="text-[10px] mt-2 opacity-60">REG CASIO SE-800-0303888</p>
                    </div>

                    <div
                        class="border-t border-dashed border-slate-300 py-4 flex justify-between uppercase text-xs font-black">
                        <span>Z</span>
                        <span>{{ $selectedZReport->report_date->format('Y-m-d') }}</span>
                        <span>3888 {{ str_pad($selectedZReport->end_order_id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="mb-4">
                        <p class="font-black text-center text-sm py-2 border-y border-dashed border-slate-300 my-2">Z Z
                            DIARIO</p>

                        <div class="flex justify-between font-black text-xs my-1">
                            <span>Z DEPTOS</span>
                            <span>{{ $selectedZReport->z_number }}</span>
                        </div>

                        @if ($selectedZReport->category_summary)
                            @foreach ($selectedZReport->category_summary as $cat)
                                <div class="ml-4 space-y-1 my-2">
                                    <div class="flex justify-between items-end text-xs">
                                        <span
                                            class="uppercase truncate max-w-[150px]">{{ $cat['category_name'] }}</span>
                                        <div class="text-right">
                                            <div class="opacity-70 text-[10px]">{{ $cat['qty'] }} items</div>
                                            <div class="font-black">${{ number_format($cat['total'], 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center p-4 italic opacity-50 text-xs text-slate-400">Sin detalles de
                                depto.</div>
                        @endif

                        <div
                            class="flex justify-between border-t border-dashed border-slate-300 pt-2 font-black text-xs mt-4">
                            <span>TL</span>
                            <span>{{ $selectedZReport->order_count }} items</span>
                            <span>${{ number_format($selectedZReport->total_sales, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-slate-300 py-4 font-black text-xs space-y-2">
                        <div class="flex justify-between">
                            <span>Z TOT. FIJOS</span>
                            <span>{{ $selectedZReport->z_number }}</span>
                        </div>
                        <div class="flex justify-between pt-2">
                            <span>BRUTO</span>
                            <span>${{ number_format($selectedZReport->total_sales, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>NETO</span>
                            <span>${{ number_format($selectedZReport->total_sales, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base">
                            <span>EFEC</span>
                            <span
                                class="underline underline-offset-4 decoration-double">${{ number_format($selectedZReport->total_sales, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-y border-dashed border-slate-300 py-4 my-2 text-xs space-y-1">
                        <div class="flex justify-between opacity-50">
                            <span>BASE1</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between opacity-50">
                            <span>BASE2</span>
                            <span>0</span>
                        </div>
                    </div>

                    <div class="text-center text-xs font-black py-4 uppercase">
                        ID TICKETS: {{ $selectedZReport->start_order_id }} ---> {{ $selectedZReport->end_order_id }}
                    </div>

                    <div class="border-t border-dashed border-slate-300 pt-4 space-y-2 text-xs font-black">
                        <div class="flex justify-between">
                            <span>Z FUNC LIBRES</span>
                            <span>{{ $selectedZReport->z_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>CAJA</span>
                            <span>${{ number_format($selectedZReport->total_sales, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>CORR (Voids)</span>
                            <span>{{ $selectedZReport->corrections_count }} c /
                                ${{ number_format($selectedZReport->total_corrections, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-8 text-center border-t-2 border-slate-100 pt-6">
                        <x-base.button wire:click="closeZModal" variant="outline-slate"
                            class="w-full uppercase font-black tracking-widest text-xs">CERRAR VISTA</x-base.button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                var ctx = document.getElementById('simple-dashboard-chart').getContext('2d');
                var simpleChart;

                const renderChart = () => {
                    if (simpleChart) simpleChart.destroy();
                    var chartLabels = @json($chartLabels);
                    var chartData = @json($chartData);
                    var chartCounts = @json($chartCounts);

                    simpleChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Ventas ($)',
                                data: chartData,
                                backgroundColor: '#164e63',
                                borderRadius: 8,
                                barPercentage: 0.6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 15,
                                    cornerRadius: 12,
                                    callbacks: {
                                        label: (c) => '$' + c.parsed.y.toLocaleString() + ' (' +
                                            chartCounts[c.dataIndex] + ' tickets)'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    },
                                    ticks: {
                                        font: {
                                            family: 'mono'
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }
                            }
                        }
                    });
                };

                renderChart();
                Livewire.on('refreshDashboard', () => {
                    setTimeout(renderChart, 100);
                });
            });
        </script>
    @endpush
</div>

<div>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-xl font-black mr-auto tracking-tight uppercase text-slate-700">
            📊 Dashboard Simple - Resumen de Caja Rápida
        </h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2">
            <x-base.button onclick="location.reload()" variant="outline-primary" class="shadow-md flex items-center gap-2">
                <x-base.lucide icon="RefreshCw" class="w-4 h-4" /> ACTUALIZAR DATOS
            </x-base.button>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div
                class="report-box zoom-in bg-white dark:bg-darkmode-600 rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-primary/20 text-primary">
                        <x-base.lucide icon="DollarSign" class="w-6 h-6" />
                    </div>
                </div>
                <div class="text-3xl font-black leading-8 mt-6">
                    ${{ number_format($totalSales, 0, ',', '.') }}
                </div>
                <div class="text-base font-bold text-slate-500 mt-1 uppercase tracking-widest text-xs">
                    Ventas Totales Hoy
                </div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div
                class="report-box zoom-in bg-white dark:bg-darkmode-600 rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-pending/20 text-pending">
                        <x-base.lucide icon="ShoppingCart" class="w-6 h-6" />
                    </div>
                </div>
                <div class="text-3xl font-black leading-8 mt-6">
                    {{ $totalCount }}
                </div>
                <div class="text-base font-bold text-slate-500 mt-1 uppercase tracking-widest text-xs">
                    Operaciones (Tickets)
                </div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div
                class="report-box zoom-in bg-white dark:bg-darkmode-600 rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-warning/20 text-warning">
                        <x-base.lucide icon="CreditCard" class="w-6 h-6" />
                    </div>
                </div>
                <div class="text-3xl font-black leading-8 mt-6">
                    ${{ number_format($averageTicket, 0, ',', '.') }}
                </div>
                <div class="text-base font-bold text-slate-500 mt-1 uppercase tracking-widest text-xs">
                    Ticket Promedio
                </div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div
                class="report-box zoom-in bg-white dark:bg-darkmode-600 rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-success/20 text-success">
                        <x-base.lucide icon="Clock" class="w-6 h-6" />
                    </div>
                </div>
                <div class="text-xl font-bold leading-8 mt-6 overflow-hidden text-ellipsis whitespace-nowrap">
                    {{ $peakHour }}
                </div>
                <div class="text-base font-bold text-slate-500 mt-1 uppercase tracking-widest text-xs">
                    Hora Pico
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12 intro-y box p-8 mt-12 sm:mt-5 rounded-2xl shadow-xl border border-slate-100 bg-white">
            <div class="flex flex-col md:flex-row md:items-center border-b pb-5 mb-5 border-slate-200/60">
                <div class="flex">
                    <div>
                        <h3 class="text-slate-800 dark:text-slate-300 text-lg xl:text-xl font-bold">
                            Dinámica de Ventas
                        </h3>
                        <p class="text-slate-500 text-sm mt-1">Evolución de las ventas a lo largo del día (intervalos de
                            30 minutos).</p>
                    </div>
                </div>
            </div>

            <div class="h-[400px] mt-6 w-full relative" wire:ignore>
                <canvas id="simple-dashboard-chart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                var ctx = document.getElementById('simple-dashboard-chart').getContext('2d');

                var chartLabels = @json($chartLabels);
                var chartData = @json($chartData);
                var chartCounts = @json($chartCounts);

                var simpleChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Total Vendido ($)',
                            data: chartData,
                            backgroundColor: 'rgba(22, 78, 99, 0.8)', // Un azul oscuro verdoso profesional
                            borderColor: 'transparent',
                            borderWidth: 0,
                            borderRadius: 6,
                            barPercentage: 0.7,
                            categoryPercentage: 0.9,
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
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 14
                                },
                                padding: 12,
                                boxPadding: 6,
                                callbacks: {
                                    label: function(context) {
                                        let sales = window.Intl.NumberFormat('es-CO', {
                                            style: 'currency',
                                            currency: 'COP'
                                        }).format(context.parsed.y);
                                        let count = chartCounts[context.dataIndex];
                                        return sales + ' (' + count + ' ventas)';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                border: {
                                    display: false
                                },
                                grid: {
                                    color: 'rgba(203, 213, 225, 0.4)',
                                    drawBorder: false,
                                },
                                ticks: {
                                    font: {
                                        family: 'Inter',
                                        size: 12
                                    },
                                    color: '#64748b',
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                border: {
                                    display: false
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    font: {
                                        family: 'Inter',
                                        size: 11
                                    },
                                    color: '#64748b',
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</div>

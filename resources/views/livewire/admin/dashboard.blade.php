<div class="p-5">

    <!-- Header -->
    <div class="intro-y flex items-center mt-8 mb-6">
        <h2 class="text-lg font-medium mr-auto flex items-center gap-2">
            <x-base.lucide icon="LayoutDashboard" class="w-5 h-5 text-primary" />
            Panel de Control
        </h2>
        <span class="text-slate-400 text-sm">{{ now()->translatedFormat('l, d \d\e F Y') }}</span>
    </div>

    <!-- ── Metric Cards ── -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- Ventas Hoy --}}
        <div class="intro-y">
            <div class="box p-5 h-full">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center">
                        <x-base.lucide icon="TrendingUp" class="w-5 h-5 text-success" />
                    </div>
                    @if(!is_null($salesGrowth))
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $salesGrowth >= 0 ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                            {{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 1) }}%
                        </span>
                    @endif
                </div>
                <div class="text-2xl font-bold mt-4 leading-tight">${{ number_format($totalSalesToday, 0, ',', '.') }}</div>
                <div class="text-slate-500 text-sm mt-1">Ventas Hoy</div>
                <div class="text-slate-400 text-xs mt-1">Ayer: ${{ number_format($salesYesterday, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Número de ventas --}}
        <div class="intro-y">
            <div class="box p-5 h-full">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <x-base.lucide icon="ShoppingBag" class="w-5 h-5 text-primary" />
                    </div>
                    @if(!is_null($ordersGrowth))
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $ordersGrowth >= 0 ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                            {{ $ordersGrowth >= 0 ? '+' : '' }}{{ number_format($ordersGrowth, 1) }}%
                        </span>
                    @endif
                </div>
                <div class="text-2xl font-bold mt-4">{{ $totalOrdersToday }}</div>
                <div class="text-slate-500 text-sm mt-1">Ventas de Hoy</div>
                <div class="text-slate-400 text-xs mt-1">Hora pico: {{ $peakHour }}</div>
            </div>
        </div>

        {{-- Ticket promedio --}}
        <div class="intro-y">
            <div class="box p-5 h-full">
                <div class="w-10 h-10 rounded-full bg-warning/10 flex items-center justify-center">
                    <x-base.lucide icon="Receipt" class="w-5 h-5 text-warning" />
                </div>
                <div class="text-2xl font-bold mt-4">${{ number_format($averageTicket, 0, ',', '.') }}</div>
                <div class="text-slate-500 text-sm mt-1">Ticket Promedio</div>
                <div class="text-slate-400 text-xs mt-1">Por venta hoy</div>
            </div>
        </div>

        {{-- Pendiente cierre Z --}}
        <div class="intro-y">
            <div class="box p-5 h-full {{ $pendingZCount > 0 ? 'border-l-4 border-orange-400' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-full {{ $pendingZCount > 0 ? 'bg-orange-50' : 'bg-slate-100' }} flex items-center justify-center">
                        <x-base.lucide icon="FileText" class="w-5 h-5 {{ $pendingZCount > 0 ? 'text-orange-500' : 'text-slate-400' }}" />
                    </div>
                    @if($pendingZCount > 0)
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-orange-100 text-orange-600">Pendiente</span>
                    @else
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-success/10 text-success">Al día</span>
                    @endif
                </div>
                <div class="text-2xl font-bold mt-4">{{ $pendingZCount }}</div>
                <div class="text-slate-500 text-sm mt-1">Sin cierre Z</div>
                <div class="text-slate-400 text-xs mt-1">${{ number_format($pendingZTotal, 0, ',', '.') }} acumulado</div>
            </div>
        </div>

    </div>

    <!-- ── Balance Row ── -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- Gastos Hoy --}}
        <div class="intro-y">
            <div class="box p-5 h-full">
                <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center">
                    <x-base.lucide icon="ShoppingCart" class="w-5 h-5 text-amber-500" />
                </div>
                <div class="text-2xl font-bold mt-4">${{ number_format($todayExpensesTotal, 0, ',', '.') }}</div>
                <div class="text-slate-500 text-sm mt-1">Gastos Hoy</div>
            </div>
        </div>

        {{-- Balance Hoy --}}
        @php $todayBalance = $totalSalesToday - $todayExpensesTotal; @endphp
        <div class="intro-y">
            <div class="box p-5 h-full {{ $todayBalance >= 0 ? 'border-l-4 border-success' : 'border-l-4 border-danger' }}">
                <div class="w-10 h-10 rounded-full {{ $todayBalance >= 0 ? 'bg-success/10' : 'bg-danger/10' }} flex items-center justify-center">
                    <x-base.lucide icon="{{ $todayBalance >= 0 ? 'TrendingUp' : 'TrendingDown' }}" class="w-5 h-5 {{ $todayBalance >= 0 ? 'text-success' : 'text-danger' }}" />
                </div>
                <div class="text-2xl font-bold mt-4 {{ $todayBalance >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format(abs($todayBalance), 0, ',', '.') }}</div>
                <div class="text-slate-500 text-sm mt-1">Balance del Día</div>
                <div class="text-slate-400 text-xs mt-1">Ventas − Gastos</div>
            </div>
        </div>

        {{-- Gastos del Mes --}}
        <div class="intro-y">
            <div class="box p-5 h-full">
                <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center">
                    <x-base.lucide icon="CalendarDays" class="w-5 h-5 text-amber-500" />
                </div>
                <div class="text-2xl font-bold mt-4">${{ number_format($monthExpensesTotal, 0, ',', '.') }}</div>
                <div class="text-slate-500 text-sm mt-1">Gastos del Mes</div>
            </div>
        </div>

        {{-- Balance del Mes --}}
        @php $monthBalance = $monthSalesTotal - $monthExpensesTotal; @endphp
        <div class="intro-y">
            <div class="box p-5 h-full {{ $monthBalance >= 0 ? 'border-l-4 border-primary' : 'border-l-4 border-danger' }}">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <x-base.lucide icon="BarChart2" class="w-5 h-5 text-primary" />
                </div>
                <div class="text-2xl font-bold mt-4 {{ $monthBalance >= 0 ? 'text-primary' : 'text-danger' }}">${{ number_format(abs($monthBalance), 0, ',', '.') }}</div>
                <div class="text-slate-500 text-sm mt-1">Balance del Mes</div>
                <div class="text-slate-400 text-xs mt-1">Ventas ${{ number_format($monthSalesTotal, 0, ',', '.') }}</div>
            </div>
        </div>

    </div>

    <!-- ── Charts Row ── -->
    <div class="grid grid-cols-12 gap-4 mb-6">

        {{-- Gráfico ventas por hora --}}
        <div class="col-span-12 xl:col-span-8 intro-y">
            <div class="box p-5 h-full">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-slate-700">Ventas por Hora</h3>
                        <p class="text-slate-400 text-xs mt-0.5">Distribución de ventas durante el día de hoy</p>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-slate-500">
                        <x-base.lucide icon="Zap" class="w-3.5 h-3.5 text-warning" />
                        Pico: {{ $peakHour }}
                    </div>
                </div>
                <div class="relative" style="height: 240px;">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfico últimos 7 días --}}
        <div class="col-span-12 xl:col-span-4 intro-y">
            <div class="box p-5 h-full">
                <div class="mb-4">
                    <h3 class="font-semibold text-slate-700">Últimos 7 Días</h3>
                    <p class="text-slate-400 text-xs mt-0.5">Tendencia de ventas semanal</p>
                </div>
                <div class="relative" style="height: 240px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- ── Bottom Row ── -->
    <div class="grid grid-cols-12 gap-4">

        {{-- Ventas recientes --}}
        <div class="col-span-12 xl:col-span-8 intro-y">
            <div class="box">
                <div class="flex items-center px-5 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-700 mr-auto">Ventas Recientes</h3>
                    <span class="text-slate-400 text-xs">Últimas 8 transacciones</span>
                </div>
                <div class="overflow-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr class="text-slate-500 text-xs uppercase">
                                <th class="pl-5">#</th>
                                <th>Cajero</th>
                                <th class="text-center">Hora</th>
                                <th class="text-right pr-5">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr class="border-t border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="pl-5 py-2.5 text-slate-400 text-xs font-mono">#{{ $order->id }}</td>
                                    <td class="py-2.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($order->user->name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-slate-700">{{ $order->user->name ?? 'Sistema' }}</div>
                                                <div class="text-xs text-slate-400">{{ $order->customer_served_by }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-2.5 text-slate-500 text-xs">{{ $order->created_at->format('h:i A') }}</td>
                                    <td class="text-right pr-5 py-2.5 font-bold text-slate-700">${{ number_format($order->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-slate-400 text-sm">No hay ventas registradas hoy</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel lateral: Top productos + Último Z --}}
        <div class="col-span-12 xl:col-span-4 intro-y flex flex-col gap-4">

            {{-- Top productos --}}
            <div class="box p-5 flex-1">
                <h3 class="font-semibold text-slate-700 mb-4">Top Productos</h3>
                @forelse ($topProducts as $top)
                    <div class="flex items-center gap-3 {{ !$loop->first ? 'mt-3 pt-3 border-t border-slate-50' : '' }}">
                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs flex-shrink-0">
                            {{ $loop->iteration }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-700 truncate">{{ $top->product->name ?? 'Producto eliminado' }}</div>
                            <div class="text-xs text-slate-400">{{ $top->total_qty }} unidades</div>
                        </div>
                        <div class="text-xs font-semibold text-slate-600">${{ number_format($top->total_revenue, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Sin datos de productos</p>
                @endforelse
            </div>

            {{-- Estado Z --}}
            <div class="box p-5">
                <h3 class="font-semibold text-slate-700 mb-3">Último Reporte Z</h3>
                @if($lastZReport)
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 rounded-full bg-success/10 flex items-center justify-center">
                            <x-base.lucide icon="CheckCircle" class="w-4 h-4 text-success" />
                        </div>
                        <div>
                            <div class="font-semibold text-slate-700">Z #{{ $lastZReport->z_number }}</div>
                            <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($lastZReport->report_date)->translatedFormat('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-3">
                        <div class="bg-slate-50 rounded-lg p-2.5 text-center">
                            <div class="text-sm font-bold text-slate-700">${{ number_format($lastZReport->total_sales, 0, ',', '.') }}</div>
                            <div class="text-xs text-slate-400">Total</div>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2.5 text-center">
                            <div class="text-sm font-bold text-slate-700">{{ $lastZReport->order_count }}</div>
                            <div class="text-xs text-slate-400">Ventas</div>
                        </div>
                    </div>
                @else
                    <p class="text-slate-400 text-sm text-center py-4">Sin reportes Z aún</p>
                @endif
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    const hourlyLabels  = @json($hourlyLabels);
    const hourlyData    = @json($hourlyData);
    const hourlyCounts  = @json($hourlyCounts);
    const weeklyLabels  = @json($weeklyLabels);
    const weeklyData    = @json($weeklyData);

    function getColor() {
        return getComputedStyle(document.documentElement)
            .getPropertyValue('--color-primary').trim() || '21 128 61';
    }

    function initHourly() {
        const ctx = document.getElementById('hourlyChart');
        if (!ctx) return;
        const primary = getColor();
        const maxVal = Math.max(...hourlyData);
        const colors = hourlyData.map(v =>
            v === maxVal && v > 0 ? 'rgba(245, 158, 11, 0.85)' : `rgba(${primary}, 0.75)`
        );
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{ label: 'Ventas', data: hourlyData, backgroundColor: colors, borderRadius: 6, borderSkipped: false }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (c) => {
                        const i = c.dataIndex;
                        return [` $${c.parsed.y.toLocaleString('es-CO')}`, ` ${hourlyCounts[i]} venta${hourlyCounts[i] !== 1 ? 's' : ''}`];
                    }}}
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 }, callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v) } }
                }
            }
        });
    }

    function initWeekly() {
        const ctx = document.getElementById('weeklyChart');
        if (!ctx) return;
        const primary = getColor();
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: [{
                    label: 'Ventas', data: weeklyData,
                    borderColor: `rgb(${primary})`, backgroundColor: `rgba(${primary}, 0.08)`,
                    borderWidth: 2.5, pointBackgroundColor: `rgb(${primary})`, pointRadius: 4,
                    fill: true, tension: 0.4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (c) => ` $${c.parsed.y.toLocaleString('es-CO')}` }}
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 }, callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v) } }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => { initHourly(); initWeekly(); });
    document.addEventListener('livewire:navigated', () => { initHourly(); initWeekly(); });
})();
</script>
@endpush

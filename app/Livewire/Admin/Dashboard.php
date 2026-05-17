<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Order;
use App\Models\ZReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function render()
    {
        $today     = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $todayEnd  = now()->endOfDay();

        // --- Métricas hoy ---
        $totalSalesToday = Order::where('status', 'paid')
            ->whereBetween('created_at', [$today, $todayEnd])
            ->sum('total');

        $totalOrdersToday = Order::where('status', 'paid')
            ->whereBetween('created_at', [$today, $todayEnd])
            ->count();

        $averageTicket = $totalOrdersToday > 0 ? $totalSalesToday / $totalOrdersToday : 0;

        // --- Comparativa ayer ---
        $salesTodayFormatted     = $totalSalesToday;
        $salesYesterday = Order::where('status', 'paid')
            ->whereBetween('created_at', [$yesterday, $yesterday->copy()->endOfDay()])
            ->sum('total');

        $ordersYesterday = Order::where('status', 'paid')
            ->whereBetween('created_at', [$yesterday, $yesterday->copy()->endOfDay()])
            ->count();

        $salesGrowth  = $salesYesterday > 0 ? (($totalSalesToday - $salesYesterday) / $salesYesterday) * 100 : null;
        $ordersGrowth = $ordersYesterday > 0 ? (($totalOrdersToday - $ordersYesterday) / $ordersYesterday) * 100 : null;

        // --- Ventas pendientes de cierre Z ---
        $pendingZ = Order::where('customer_served_by', 'Caja Simple')
            ->where('status', 'paid')
            ->whereNull('z_report_id')
            ->selectRaw('COUNT(*) as count, SUM(total) as total')
            ->first();

        // --- Gráfico: ventas por hora (hoy) ---
        $ordersToday = Order::where('status', 'paid')
            ->whereBetween('created_at', [$today, $todayEnd])
            ->get(['total', 'created_at']);

        $hourlyLabels = [];
        $hourlyData   = [];
        $hourlyCounts = [];
        $peakHour     = 'N/A';
        $peakAmount   = 0;

        for ($h = 6; $h <= 21; $h++) {
            $start = $today->copy()->addHours($h);
            $end   = $start->copy()->addHour();
            $startTs = $start->timestamp;
            $endTs   = $end->timestamp;
            $slice = $ordersToday->filter(function($o) use ($startTs, $endTs) {
                $t = Carbon::parse($o->created_at)->timestamp;
                return $t >= $startTs && $t < $endTs;
            });
            $sum   = $slice->sum('total');
            $label = $start->format('h A');

            $hourlyLabels[] = $label;
            $hourlyData[]   = $sum;
            $hourlyCounts[] = $slice->count();

            if ($sum > $peakAmount) {
                $peakAmount = $sum;
                $peakHour   = $start->format('h:i A') . ' – ' . $end->format('h:i A');
            }
        }

        // --- Gráfico: ventas últimos 7 días ---
        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $day         = now()->subDays($i)->startOfDay();
            $dayEnd      = $day->copy()->endOfDay();
            $weeklyLabels[] = $day->translatedFormat('D d');
            $weeklyData[]   = (float) Order::where('status', 'paid')
                ->whereBetween('created_at', [$day, $dayEnd])
                ->sum('total');
        }

        // --- Pedidos recientes ---
        $recentOrders = Order::with('user')
            ->where('status', 'paid')
            ->latest()
            ->take(8)
            ->get();

        // --- Top productos (histórico) ---
        $topProducts = \App\Models\OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // --- Último reporte Z ---
        $lastZReport = ZReport::latest()->first();

        // --- Gastos (expenses) ---
        $todayExpensesTotal = Expense::whereDate('created_at', now()->toDateString())->sum('amount');
        $monthExpensesTotal = Expense::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $monthSalesTotal = Order::where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        return view('livewire.admin.dashboard', [
            'totalSalesToday'  => $totalSalesToday,
            'totalOrdersToday' => $totalOrdersToday,
            'averageTicket'    => $averageTicket,
            'salesGrowth'      => $salesGrowth,
            'ordersGrowth'     => $ordersGrowth,
            'salesYesterday'   => $salesYesterday,
            'pendingZCount'    => $pendingZ->count ?? 0,
            'pendingZTotal'    => $pendingZ->total ?? 0,
            'hourlyLabels'     => $hourlyLabels,
            'hourlyData'       => $hourlyData,
            'hourlyCounts'     => $hourlyCounts,
            'peakHour'         => $peakAmount > 0 ? $peakHour : 'Sin ventas aún',
            'weeklyLabels'     => $weeklyLabels,
            'weeklyData'       => $weeklyData,
            'recentOrders'          => $recentOrders,
            'topProducts'           => $topProducts,
            'lastZReport'           => $lastZReport,
            'todayExpensesTotal'    => $todayExpensesTotal,
            'monthExpensesTotal'    => $monthExpensesTotal,
            'monthSalesTotal'       => $monthSalesTotal,
        ]);
    }
}

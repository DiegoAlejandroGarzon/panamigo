<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;

class Dashboard extends Component
{
    public function render()
    {
        $today = now()->startOfDay();

        // Estadísticas básicas
        $totalSalesToday = Order::where('status', 'paid')
            ->where('created_at', '>=', $today)
            ->sum('total');

        $totalOrdersToday = Order::where('created_at', '>=', $today)
            ->count();

        $pendingOrdersCount = Order::where('status', 'pending')
            ->count();

        // Pedidos por empleado
        $employees = \App\Models\User::role(['Atención al Cliente', 'Cajera'])
            ->withCount(['orders' => function($query) use ($today) {
                $query->where('created_at', '>=', $today);
            }])
            ->get();

        // Pedidos recientes
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Productos más vendidos (Top 5)
        $topProducts = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalSalesToday' => $totalSalesToday,
            'totalOrdersToday' => $totalOrdersToday,
            'pendingOrdersCount' => $pendingOrdersCount,
            'employees' => $employees,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }
}

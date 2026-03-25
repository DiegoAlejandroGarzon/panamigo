<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use App\Models\ZReport;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class SimpleDashboard extends Component
{
    public $showZModal = false;
    public $nextZNumber;
    public $lastZReport;
    public $selectedZReport;

    public function mount()
    {
        $this->lastZReport = ZReport::latest()->first();
        $this->nextZNumber = $this->lastZReport ? $this->lastZReport->z_number + 1 : 1;
    }

    public function generateZReport()
    {
        $unclosedOrders = Order::where('customer_served_by', 'Caja Simple')
            ->where('status', 'paid')
            ->whereNull('z_report_id')
            ->get();

        if ($unclosedOrders->isEmpty()) {
            session()->flash('error', 'No hay ventas pendientes por cerrar actualmente.');
            return;
        }

        DB::transaction(function() use ($unclosedOrders) {
            $totalSales = $unclosedOrders->sum('total');
            $orderCount = $unclosedOrders->count();
            $startOrder = $unclosedOrders->sortBy('id')->first();
            $endOrder = $unclosedOrders->sortBy('id')->last();

            // Calculate category summary
            $categorySummary = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->whereIn('order_items.order_id', $unclosedOrders->pluck('id'))
                ->select('categories.name as category_name', DB::raw('count(order_items.id) as qty'), DB::raw('sum(order_items.subtotal) as total'))
                ->groupBy('categories.id', 'categories.name')
                ->get()
                ->toArray();

            $categoryTotal = collect($categorySummary)->sum('total');
            $fastSalesTotal = $totalSales - $categoryTotal;

            if ($fastSalesTotal > 0) {
                $categorySummary[] = [
                    'category_name' => 'VENTAS RÁPIDAS',
                    'qty' => $unclosedOrders->count() - collect($categorySummary)->sum('qty'),
                    'total' => $fastSalesTotal
                ];
            }

            // Check for voided orders (could be from session or status)
            $voidedOrders = Order::where('customer_served_by', 'Caja Simple')
                ->where('status', 'canceled')
                ->whereNull('z_report_id')
                ->get();
            
            $correctionsCount = $voidedOrders->count();
            $totalCorrections = $voidedOrders->sum('total');

            $zReport = ZReport::create([
                'z_number' => $this->nextZNumber,
                'report_date' => date('Y-m-d'),
                'start_order_id' => $startOrder->id,
                'end_order_id' => $endOrder->id,
                'total_sales' => $totalSales,
                'order_count' => $orderCount,
                'total_corrections' => $totalCorrections,
                'corrections_count' => $correctionsCount,
                'user_id' => auth()->id(),
                'category_summary' => $categorySummary
            ]);

            Order::whereIn('id', $unclosedOrders->pluck('id'))->update(['z_report_id' => $zReport->id]);
            if ($voidedOrders->isNotEmpty()) {
                Order::whereIn('id', $voidedOrders->pluck('id'))->update(['z_report_id' => $zReport->id]);
            }
        });

        $this->mount();
        session()->flash('message', 'Reporte Z #' . ($this->nextZNumber - 1) . ' generado con éxito.');
        $this->dispatch('refreshDashboard');
    }

    public function viewZReport($id)
    {
        $this->selectedZReport = ZReport::with(['user', 'startOrder', 'endOrder'])->find($id);
        $this->showZModal = true;
    }

    public function closeZModal()
    {
        $this->showZModal = false;
        $this->selectedZReport = null;
    }

    public function render()
    {
        $today = date('Y-m-d');
        
        $ordersToday = Order::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->where('customer_served_by', 'Caja Simple')
            ->get();

        $totalSales = $ordersToday->sum('total');
        $totalCount = $ordersToday->count();
        $averageTicket = $totalCount > 0 ? $totalSales / $totalCount : 0;

        // Chart calculations
        $chartLabels = []; $chartData = []; $chartCounts = [];
        $startTime = Carbon::today()->addHours(5);
        $lastOrderTime = $ordersToday->max('created_at');
        $endTime = max(Carbon::today()->addHours(11)->addMinutes(30), $lastOrderTime ? Carbon::parse($lastOrderTime)->addHour() : Carbon::today()->addHours(11)->addMinutes(30));
        
        $current = clone $startTime;
        $maxIntervalSales = 0; $peakHour = 'N/A';

        while ($current <= $endTime) {
            $next = (clone $current)->addMinutes(30);
            $filtered = $ordersToday->filter(fn($o) => Carbon::parse($o->created_at)->between($current, $next));
            $sum = $filtered->sum('total');
            $count = $filtered->count();
            $label = $current->format('h:i A');
            $chartLabels[] = $label; $chartData[] = $sum; $chartCounts[] = $count;

            if ($sum > $maxIntervalSales) { $maxIntervalSales = $sum; $peakHour = $label . ' - ' . $next->format('h:i A'); }
            $current->addMinutes(30);
        }

        $pendingZOrders = Order::where('customer_served_by', 'Caja Simple')->where('status', 'paid')->whereNull('z_report_id')->get();
        $zReports = ZReport::with('user')->latest()->take(10)->get();

        return view('livewire.dashboard.simple-dashboard', [
            'totalSales' => $totalSales, 'totalCount' => $totalCount, 'averageTicket' => $averageTicket,
            'chartLabels' => $chartLabels, 'chartData' => $chartData, 'chartCounts' => $chartCounts,
            'peakHour' => $peakHour, 'maxIntervalSales' => $maxIntervalSales,
            'pendingZCount' => $pendingZOrders->count(), 'pendingZTotal' => $pendingZOrders->sum('total'),
            'zReports' => $zReports
        ]);
    }
}

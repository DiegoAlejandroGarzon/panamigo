<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Carbon\Carbon;
use Livewire\Component;

class SimpleDashboard extends Component
{
    public function render()
    {
        $today = date('Y-m-d');
        
        $orders = Order::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->where('customer_served_by', 'Caja Simple')
            ->get();

        $totalSales = $orders->sum('total');
        $totalCount = $orders->count();
        $averageTicket = $totalCount > 0 ? $totalSales / $totalCount : 0;

        $chartLabels = [];
        $chartData = [];
        $chartCounts = [];
        
        // Define range: from 05:00 AM to 11:30 AM (as requested, intervals of 30 min)
        // If current time is past 11:30 AM, we can expand it, but let's stick to their request 5AM-11AM.
        // Actually, we can expand it up to 2:00 PM if needed, but I'll make the range flexible up to the last order or 11:30 AM, whichever is later.
        $startTime = Carbon::today()->addHours(5);
        $lastOrderTime = $orders->max('created_at');
        // If there's orders after 11am, we expand the end time.
        $endTime = Carbon::today()->addHours(11)->addMinutes(30);
        
        if ($lastOrderTime && Carbon::parse($lastOrderTime)->isAfter($endTime)) {
            // Round up to nearest half hour
            $minutes = Carbon::parse($lastOrderTime)->minute;
            if ($minutes <= 30) {
                $endTime = Carbon::parse($lastOrderTime)->minute(30)->second(0);
            } else {
                $endTime = Carbon::parse($lastOrderTime)->addHour()->minute(0)->second(0);
            }
        }

        $current = clone $startTime;
        $maxIntervalSales = 0;
        $peakHour = 'N/A';

        while ($current <= $endTime) {
            $next = (clone $current)->addMinutes(30);
            
            $filtered = $orders->filter(function($o) use ($current, $next) {
                $orderTime = Carbon::parse($o->created_at);
                return $orderTime >= $current && $orderTime < $next;
            });
            
            $sum = $filtered->sum('total');
            $count = $filtered->count();

            $label = $current->format('h:i A'); // 05:00 AM
            $chartLabels[] = $label;
            $chartData[] = $sum;
            $chartCounts[] = $count;

            if ($sum > $maxIntervalSales) {
                $maxIntervalSales = $sum;
                $peakHour = $label . ' - ' . $next->format('h:i A');
            }

            $current->addMinutes(30);
        }

        return view('livewire.dashboard.simple-dashboard', [
            'totalSales' => $totalSales,
            'totalCount' => $totalCount,
            'averageTicket' => $averageTicket,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'chartCounts' => $chartCounts,
            'peakHour' => $peakHour,
            'maxIntervalSales' => $maxIntervalSales
        ]);
    }
}

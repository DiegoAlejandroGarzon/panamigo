<?php

namespace App\Livewire\Reports;

use App\Models\Order;
use App\Models\ZPrint;
use Carbon\Carbon;
use Livewire\Component;

class ZPrintReport extends Component
{
    public $selectedYear;
    public $selectedMonth;

    public function mount()
    {
        $this->selectedYear  = now()->year;
        $this->selectedMonth = now()->month;
    }

    public function render()
    {
        $zPrints = ZPrint::with('user')
            ->whereYear('print_date', $this->selectedYear)
            ->whereMonth('print_date', $this->selectedMonth)
            ->orderBy('print_date')
            ->get();

        $totalReported = $zPrints->sum('reported_total');
        $totalCount    = $zPrints->sum('reported_count');

        // Valores reales de BD para el mismo período
        $start = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $realTotal = Order::where('status', 'paid')
            ->where('customer_served_by', 'Caja Simple')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        $realCount = Order::where('status', 'paid')
            ->where('customer_served_by', 'Caja Simple')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $difference = $totalReported - $realTotal;

        return view('livewire.reports.z-print-report', [
            'zPrints'       => $zPrints,
            'totalReported' => $totalReported,
            'totalCount'    => $totalCount,
            'realTotal'     => $realTotal,
            'realCount'     => $realCount,
            'difference'    => $difference,
        ]);
    }
}

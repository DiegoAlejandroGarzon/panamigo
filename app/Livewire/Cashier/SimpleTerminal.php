<?php

namespace App\Livewire\Cashier;

use Livewire\Component;
use App\Models\Order;

class SimpleTerminal extends Component
{
    public $fastAmount = '';
    public $shouldPrint = true;
    public $showZModal = false;
    public $zDate = '';
    public $zTotal = 0;
    public $zCount = 0;

    public function mount()
    {
        $this->zDate = date('Y-m-d');
    }

    public function render()
    {
        $recentSales = Order::where('customer_served_by', 'Caja Simple')
            ->where('status', 'paid')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.cashier.simple-terminal', [
            'recentSales' => $recentSales
        ]);
    }

    public function togglePrint()
    {
        $this->shouldPrint = !$this->shouldPrint;
    }

    public function processFastSale()
    {
        $this->validate([
            'fastAmount' => 'required|numeric|min:0.01'
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $this->fastAmount,
            'status' => 'paid',
            'customer_served_by' => 'Caja Simple'
        ]);

        if ($this->shouldPrint) {
            $this->dispatch('print-ticket', order: $order->load('items.product'));
        } else {
            $this->dispatch('open-drawer');
        }

        $this->fastAmount = '';
        session()->flash('message', 'Venta rápida por $' . $order->total . ' procesada exitosamente.');
    }

    public function openZModal()
    {
        $this->showZModal = true;
    }

    public function closeZModal()
    {
        $this->showZModal = false;
    }

    public function loadRealZData()
    {
        $date = $this->zDate ?: date('Y-m-d');
        $orders = Order::whereDate('created_at', $date)
            ->where('status', 'paid')
            ->get();
        
        $this->zTotal = $orders->sum('total');
        $this->zCount = $orders->count();
    }

    public function printZReport()
    {
        $this->dispatch('print-z', data: [
            'date' => $this->zDate,
            'total' => $this->zTotal,
            'count' => $this->zCount,
        ]);
        
        $this->showZModal = false;
    }

    public function openDrawerOnly()
    {
        $this->dispatch('open-drawer');
    }
}

<?php

namespace App\Livewire\Cashier;

use Livewire\Component;
use App\Models\Order;

class Terminal extends Component
{
    public $selectedOrder = null;
    public $fastAmount = '';
    public $shouldPrint = true;

    public function render()
    {
        $pendingOrders = Order::where('status', 'pending')
            ->with(['items.product', 'user']) // Eager load
            ->latest()
            ->get();

        return view('livewire.cashier.terminal', [
            'pendingOrders' => $pendingOrders
        ]);
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrder = Order::with(['items.product', 'user'])->find($orderId);
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
            'customer_served_by' => 'Caja'
        ]);

        if ($this->shouldPrint) {
            $this->dispatch('print-ticket', order: $order->load('items.product'));
        } else {
            // Even if not printing ticket, maybe open drawer?
            $this->dispatch('open-drawer');
        }

        $this->fastAmount = '';
        session()->flash('message', 'Venta rápida por $' . $order->total . ' procesada.');
    }

    public function markAsPaid()
    {
        if (!$this->selectedOrder) return;

        $this->selectedOrder->status = 'paid';
        $this->selectedOrder->save();

        // Dispatch browser event for printing
        if ($this->shouldPrint) {
            $this->dispatch('print-ticket', order: $this->selectedOrder->load('items.product'));
        } else {
            $this->dispatch('open-drawer');
        }

        session()->flash('message', 'Pedido #' . $this->selectedOrder->id . ' marcado como pagado.');
        
        $this->selectedOrder = null;
    }

    public $showZModal = false;
    public $zDate = '';
    public $zTotal = 0;
    public $zCount = 0;

    public function mount()
    {
        $this->zDate = date('Y-m-d');
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

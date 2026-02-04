<?php

namespace App\Livewire\Cashier;

use Livewire\Component;
use App\Models\Order;

class Terminal extends Component
{
    public $selectedOrder = null;

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

    public function markAsPaid()
    {
        if (!$this->selectedOrder) return;

        $this->selectedOrder->status = 'paid';
        $this->selectedOrder->save();

        // Dispatch browser event for printing
        $this->dispatch('print-ticket', order: $this->selectedOrder->load('items.product'));

        session()->flash('message', 'Pedido #' . $this->selectedOrder->id . ' marcado como pagado e imprimiendo.');
        
        $this->selectedOrder = null;
    }
}

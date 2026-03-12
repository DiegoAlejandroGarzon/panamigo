<?php

namespace App\Livewire\Ac;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class OrderTaker extends Component
{
    public function render()
    {
        return view('livewire.ac.order-taker', [
            'allProducts' => Product::with(['category', 'brand'])->get(),
            'categories' => \App\Models\Category::all(),
            'brands' => \App\Models\Brand::all(),
        ])->layout('layouts.pos');
    }

    /**
     * Procesa el pedido recibido desde el cliente (Alpine.js)
     */
    public function sendToCashier($cart, $total)
    {
        if (empty($cart)) {
            return;
        }

        // Crear la orden principal
        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $total,
            'status' => 'pending',
            'customer_served_by' => 'Atención al Cliente',
        ]);

        // Crear los items de la orden
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal']
            ]);
        }

        session()->flash('message', 'Pedido enviado a Caja #' . $order->id);
        
        // Emitir evento para que el frontend limpie el carrito local
        $this->dispatch('order-sent');
    }
}

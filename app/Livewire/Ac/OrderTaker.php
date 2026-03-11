<?php

namespace App\Livewire\Ac;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class OrderTaker extends Component
{
    public $search = '';
    public $categoryId = null;
    public $brandId = null;

    protected $updatesQueryString = ['search', 'categoryId', 'brandId'];

    public function render()
    {
        $query = Product::where('name', 'like', '%' . $this->search . '%');

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->brandId) {
            $query->where('brand_id', $this->brandId);
        }

        return view('livewire.ac.order-taker', [
            'products' => $query->get(),
            'categories' => \App\Models\Category::all(),
            'brands' => \App\Models\Brand::all(),
        ]);
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

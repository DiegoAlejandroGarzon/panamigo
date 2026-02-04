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
    public $cart = [];
    public $total = 0;

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->where('stock', '>', 0)
            ->get();

        return view('livewire.ac.order-taker', [
            'products' => $products
        ]);
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if(isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $product->price;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price
            ];
        }
        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        if(isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            $this->calculateTotal();
        }
    }

    public function increaseQty($productId)
    {
        if(isset($this->cart[$productId])) {
             $this->cart[$productId]['quantity']++;
             $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $this->cart[$productId]['price'];
             $this->calculateTotal();
        }
    }

    public function decreaseQty($productId)
    {
        if(isset($this->cart[$productId])) {
             if($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
                $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $this->cart[$productId]['price'];
             } else {
                unset($this->cart[$productId]);
             }
             $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $this->total = array_sum(array_column($this->cart, 'subtotal'));
    }

    public function sendToCashier()
    {
        if(empty($this->cart)) return;

        // Create Order
        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $this->total,
            'status' => 'pending',
            'customer_served_by' => 'Cliente en mostrador', // Or dynamic if needed
        ]);

        // Create Items
        foreach($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal']
            ]);
            
            // Decrease Stock
            $product = Product::find($item['id']);
            $product->decrement('stock', $item['quantity']);
        }

        $this->cart = [];
        $this->total = 0;
        session()->flash('message', 'Pedido enviado a Caja #' . $order->id);
    }
}

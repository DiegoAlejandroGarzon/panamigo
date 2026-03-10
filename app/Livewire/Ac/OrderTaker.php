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

    // Pan especial
    public $showPanModal = false;
    public $panAmount = '';
    public $panProductId = null;

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->get();

        return view('livewire.ac.order-taker', [
            'products' => $products
        ]);
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        // Si el producto se llama "PAN" o similar, abrir el modal en vez de añadir directo si es la primera vez?
        // O mejor una función aparte para el pan.
        
        if(strtoupper($product->name) == 'PAN') {
            $this->openPanModal($product->id);
            return;
        }

        if(isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $product->price;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
                'is_pan' => false
            ];
        }
        $this->calculateTotal();
    }

    public function openPanModal($productId)
    {
        $this->panProductId = $productId;
        $this->panAmount = isset($this->cart[$productId]) ? $this->cart[$productId]['subtotal'] : '';
        $this->showPanModal = true;
    }

    public function closePanModal()
    {
        $this->showPanModal = false;
        $this->panAmount = '';
    }

    public function addPanToCart()
    {
        $this->validate([
            'panAmount' => 'required|numeric|min:0'
        ]);

        $product = Product::find($this->panProductId);

        $this->cart[$this->panProductId] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $this->panAmount,
            'quantity' => 1,
            'subtotal' => $this->panAmount,
            'is_pan' => true
        ];

        $this->calculateTotal();
        $this->closePanModal();
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
        }

        $this->cart = [];
        $this->total = 0;
        session()->flash('message', 'Pedido enviado a Caja #' . $order->id);
    }
}

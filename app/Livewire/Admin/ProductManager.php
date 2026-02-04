<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;

class ProductManager extends Component
{
    public $products, $name, $price, $stock, $product_id, $provisional_image;
    public $isOpen = false;

    public function render()
    {
        $this->products = Product::all();
        return view('livewire.admin.product-manager');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->price = '';
        $this->stock = '';
        $this->product_id = '';
        $this->provisional_image = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        Product::updateOrCreate(['id' => $this->product_id], [
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'provisional_image' => $this->provisional_image,
        ]);

        session()->flash('message', 
            $this->product_id ? 'Producto Actualizado con éxito.' : 'Producto Creado con éxito.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->product_id = $id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->provisional_image = $product->provisional_image;

        $this->openModal();
    }

    public function delete($id)
    {
        Product::find($id)->delete();
        session()->flash('message', 'Producto Eliminado con éxito.');
    }
}

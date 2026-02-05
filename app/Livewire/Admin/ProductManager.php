<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;

class ProductManager extends Component
{
    public $products, $name, $price, $stock, $product_id, $provisional_image, $category_id, $brand_id, $idToDelete;
    public $isOpen = false;

    public function render()
    {
        $this->products = Product::with(['category', 'brand'])->latest()->get();
        $categories = \App\Models\Category::all();
        $brands = \App\Models\Brand::all();
        
        return view('livewire.admin.product-manager', [
            'allCategories' => $categories,
            'allBrands' => $brands,
        ]);
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
        $this->category_id = null;
        $this->brand_id = null;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        Product::updateOrCreate(['id' => $this->product_id], [
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'provisional_image' => $this->provisional_image,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
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
        $this->category_id = $product->category_id;
        $this->brand_id = $product->brand_id;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->idToDelete = $id;
        $this->dispatch('open-delete-confirmation');
    }

    public function delete()
    {
        if ($this->idToDelete) {
            Product::find($this->idToDelete)->delete();
            session()->flash('message', 'Producto Eliminado con éxito.');
            $this->idToDelete = null;
            $this->dispatch('close-delete-confirmation');
        }
    }
}

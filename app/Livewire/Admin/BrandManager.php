<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Brand;

class BrandManager extends Component
{
    public $name, $brand_id, $idToDelete;

    public function render()
    {
        return view('livewire.admin.brand-manager', [
            'brands' => Brand::latest()->get()
        ]);
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->brand_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        Brand::updateOrCreate(['id' => $this->brand_id], [
            'name' => $this->name,
        ]);

        session()->flash('message', 
            $this->brand_id ? 'Marca Actualizada con éxito.' : 'Marca Creada con éxito.');

        $this->dispatch('close-modal');
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        $this->brand_id = $id;
        $this->name = $brand->name;
        
        $this->dispatch('open-brand-modal');
    }

    public function confirmDelete($id)
    {
        $this->idToDelete = $id;
        $this->dispatch('open-delete-confirmation');
    }

    public function delete()
    {
        if ($this->idToDelete) {
            Brand::find($this->idToDelete)->delete();
            session()->flash('message', 'Marca Eliminada con éxito.');
            $this->idToDelete = null;
            $this->dispatch('close-delete-confirmation');
        }
    }
}

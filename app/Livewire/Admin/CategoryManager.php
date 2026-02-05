<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Category;

class CategoryManager extends Component
{
    public $name, $category_id, $idToDelete;
    public $isOpen = false;
    protected $queryString = ['create'];
    public $create;

    public function mount()
    {
        if ($this->create) {
            $this->startCreate();
            $this->dispatch('open-category-modal');
        }
    }

    public function render()
    {
        return view('livewire.admin.category-manager', [
            'categories' => Category::latest()->get()
        ]);
    }

    public function startCreate()
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
        $this->category_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        Category::updateOrCreate(['id' => $this->category_id], [
            'name' => $this->name,
        ]);

        session()->flash('message',
            $this->category_id ? 'Categoría Actualizada con éxito.' : 'Categoría Creada con éxito.');

        $this->dispatch('close-modal');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->category_id = $id;
        $this->name = $category->name;

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
            Category::find($this->idToDelete)->delete();
            session()->flash('message', 'Categoría Eliminada con éxito.');
            $this->idToDelete = null;
            $this->dispatch('close-delete-confirmation');
        }
    }
}

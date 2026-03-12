<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductTemplateExport;
use App\Imports\ProductImport;

class ProductManager extends Component
{
    use WithFileUploads;

    public $products, $name, $price, $stock, $product_id, $provisional_image, $category_id, $brand_id, $idToDelete;
    public $photo; // Para la carga de imagen nueva
    
    // Excel Import
    public $excelFile;
    public $importResults = null;
    public $showImportModal = false;

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

    public function resetInputFields()
    {
        $this->name = '';
        $this->price = '';
        $this->stock = '';
        $this->product_id = '';
        $this->provisional_image = '';
        $this->photo = null;
        $this->category_id = null;
        $this->brand_id = null;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'brand_id' => 'required',
            'photo' => 'nullable|image|max:5120', // Max 5MB
        ]);

        $imagePath = $this->provisional_image;

        if ($this->photo) {
            // Eliminar imagen anterior si existe y estamos editando
            if ($this->product_id) {
                $oldProduct = Product::find($this->product_id);
                if ($oldProduct && $oldProduct->provisional_image && \Storage::disk('public')->exists(str_replace('/storage/', '', $oldProduct->provisional_image))) {
                    \Storage::disk('public')->delete(str_replace('/storage/', '', $oldProduct->provisional_image));
                }
            }

            // Comprimir y guardar
            $filename = time() . '_' . $this->photo->getClientOriginalName();
            $tempPath = $this->photo->getRealPath();
            $compressedImage = $this->compressImage($tempPath);
            
            \Storage::disk('public')->put('products/' . $filename, $compressedImage);
            $imagePath = '/storage/products/' . $filename;
        }

        Product::updateOrCreate(['id' => $this->product_id], [
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock ?: 0,
            'provisional_image' => $imagePath,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
        ]);

        session()->flash('message', 
            $this->product_id ? 'Producto Actualizado con éxito.' : 'Producto Creado con éxito.');

        $this->dispatch('close-product-modal');
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
        $this->photo = null;

        $this->dispatch('open-product-modal');
    }

    public function confirmDelete($id)
    {
        $this->idToDelete = $id;
        $this->dispatch('open-delete-confirmation');
    }

    public function delete()
    {
        if ($this->idToDelete) {
            $product = Product::find($this->idToDelete);
            if ($product) {
                // Eliminar imagen física
                if ($product->provisional_image && \Storage::disk('public')->exists(str_replace('/storage/', '', $product->provisional_image))) {
                    \Storage::disk('public')->delete(str_replace('/storage/', '', $product->provisional_image));
                }
                $product->delete();
            }
            session()->flash('message', 'Producto Eliminado con éxito.');
            $this->idToDelete = null;
            $this->dispatch('close-delete-confirmation');
        }
    }

    /**
     * Comprime la imagen usando GD a un tamaño máximo de 400x400 y 80% de calidad
     */
    private function compressImage($path)
    {
        list($width, $height, $type) = getimagesize($path);
        
        // Determinar el recurso de imagen según el tipo
        switch ($type) {
            case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($path); break;
            case IMAGETYPE_PNG: $src = imagecreatefrompng($path); break;
            case IMAGETYPE_WEBP: $src = imagecreatefromwebp($path); break;
            default: return file_get_contents($path);
        }

        // Redimensionar si es necesario (max 400px)
        $max_dim = 400;
        $new_width = $width;
        $new_height = $height;

        if ($width > $max_dim || $height > $max_dim) {
            if ($width > $height) {
                $new_width = $max_dim;
                $new_height = floor($height * ($max_dim / $width));
            } else {
                $new_height = $max_dim;
                $new_width = floor($width * ($max_dim / $height));
            }
        }

        $dst = imagecreatetruecolor($new_width, $new_height);
        
        // Mantener transparencia para PNG/WEBP
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Capturar el output en un buffer
        ob_start();
        // Guardamos todo como JPEG para consistencia y peso, excepto si queremos mantener transparencia
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            imagewebp($dst, null, 80);
        } else {
            imagejpeg($dst, null, 80);
        }
        $data = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $data;
    }

    // Excel Logic
    public function downloadTemplate()
    {
        return Excel::download(new ProductTemplateExport, 'plantilla_productos_panamigo.xlsx');
    }

    public function openImportModal()
    {
        $this->importResults = null;
        $this->excelFile = null;
        $this->dispatch('open-import-modal');
    }

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $import = new ProductImport;
        Excel::import($import, $this->excelFile->getRealPath());

        $this->importResults = $import->results;
        
        if ($this->importResults['success'] > 0) {
            session()->flash('message', "Importación completada: {$this->importResults['success']} productos subidos.");
        }
        
        $this->excelFile = null;
    }
}

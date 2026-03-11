<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class ProductImport implements ToCollection, WithHeadingRow
{
    public $results = [
        'success' => 0,
        'failed' => []
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();
            
            // Allow both ID or direct data if needed, but per template it's headings
            // Headings from Excel: 'nombre', 'precio', 'stock', 'id_categoria', 'id_marca', 'url_imagen_opcional'
            
            $validator = Validator::make($data, [
                'nombre' => 'required',
                'precio' => 'required|numeric',
                'stock' => 'nullable|numeric',
                'id_categoria' => 'required|exists:categories,id',
                'id_marca' => 'required|exists:brands,id',
            ]);

            if ($validator->fails()) {
                $this->results['failed'][] = [
                    'row' => $index + 2,
                    'name' => $data['nombre'] ?? 'Desconocido',
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            try {
                Product::create([
                    'name' => $data['nombre'],
                    'price' => $data['precio'],
                    'stock' => $data['stock'] ?? 0,
                    'category_id' => $data['id_categoria'],
                    'brand_id' => $data['id_marca'],
                    'provisional_image' => $data['url_imagen_opcional'] ?? null,
                ]);
                $this->results['success']++;
            } catch (\Exception $e) {
                $this->results['failed'][] = [
                    'row' => $index + 2,
                    'name' => $data['nombre'] ?? 'Desconocido',
                    'errors' => [$e->getMessage()]
                ];
            }
        }
    }
}

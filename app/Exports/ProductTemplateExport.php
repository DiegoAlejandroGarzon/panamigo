<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateSheet implements WithTitle, WithHeadings
{
    public function title(): string
    {
        return 'Importar Productos';
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Precio',
            'Stock',
            'ID Categoría',
            'ID Marca',
            'URL Imagen (Opcional)'
        ];
    }
}

class CategoriesSheet implements WithTitle, WithHeadings, FromCollection
{
    public function title(): string
    {
        return 'Categorias';
    }

    public function headings(): array
    {
        return ['ID', 'Nombre'];
    }

    public function collection()
    {
        return \App\Models\Category::select('id', 'name')->get();
    }
}

class BrandsSheet implements WithTitle, WithHeadings, FromCollection
{
    public function title(): string
    {
        return 'Marcas';
    }

    public function headings(): array
    {
        return ['ID', 'Nombre'];
    }

    public function collection()
    {
        return \App\Models\Brand::select('id', 'name')->get();
    }
}

class ProductTemplateExport implements \Maatwebsite\Excel\Concerns\WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductTemplateSheet(),
            new CategoriesSheet(),
            new BrandsSheet(),
        ];
    }
}

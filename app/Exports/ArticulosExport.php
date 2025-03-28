<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArticulosExport implements FromCollection, WithHeadings
{
    protected $articulos;

    public function __construct($articulos)
    {
        $this->articulos = $articulos;
    }

    public function collection()
    {
        return $this->articulos;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre',
            'Stock',
            'Stock Mínimo',
            'Categoría'
        ];
    }
}

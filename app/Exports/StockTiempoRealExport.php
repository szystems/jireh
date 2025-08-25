<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockTiempoRealExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $reporteStock;

    public function __construct($reporteStock)
    {
        $this->reporteStock = $reporteStock;
    }

    public function array(): array
    {
        $data = [];
        
        foreach ($this->reporteStock['articulos'] as $item) {
            $data[] = [
                'codigo' => $item['articulo']->codigo,
                'nombre' => $item['articulo']->nombre,
                'tipo' => ucfirst($item['articulo']->tipo),
                'stock_actual' => $item['stock_actual'],
                'stock_teorico' => $item['stock_teorico'],
                'diferencia' => $item['diferencia'],
                'estado' => $item['consistente'] ? 'Consistente' : 'Inconsistente',
                'nivel_stock' => $this->getNivelStock($item['stock_actual']),
                'ultima_venta' => $item['ultima_venta'] ? $item['ultima_venta']->format('d/m/Y') : 'Sin ventas',
                'observaciones' => $this->getObservaciones($item)
            ];
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Artículo', 
            'Tipo',
            'Stock Actual',
            'Stock Teórico',
            'Diferencia',
            'Estado',
            'Nivel Stock',
            'Última Venta',
            'Observaciones'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'],
            ],
            'alignment' => [
                'horizontal' => 'center',
            ],
        ]);

        // Aplicar colores a las filas según el nivel de stock
        $rowNumber = 2;
        foreach ($this->reporteStock['articulos'] as $item) {
            $stockActual = $item['stock_actual'];
            
            if ($stockActual < 0) {
                // Stock negativo - rojo
                $sheet->getStyle("A{$rowNumber}:J{$rowNumber}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFEBEE'],
                    ],
                ]);
            } elseif ($stockActual <= 10) {
                // Stock bajo - amarillo
                $sheet->getStyle("A{$rowNumber}:J{$rowNumber}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF3E0'],
                    ],
                ]);
            }
            
            $rowNumber++;
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Código
            'B' => 30,  // Artículo
            'C' => 12,  // Tipo
            'D' => 12,  // Stock Actual
            'E' => 12,  // Stock Teórico
            'F' => 12,  // Diferencia
            'G' => 15,  // Estado
            'H' => 15,  // Nivel Stock
            'I' => 15,  // Última Venta
            'J' => 25,  // Observaciones
        ];
    }

    public function title(): string
    {
        return 'Stock Tiempo Real ' . date('d-m-Y');
    }

    private function getNivelStock($stock)
    {
        if ($stock < 0) {
            return 'CRÍTICO';
        } elseif ($stock <= 10) {
            return 'BAJO';
        } else {
            return 'NORMAL';
        }
    }

    private function getObservaciones($item)
    {
        $observaciones = [];
        
        if ($item['stock_actual'] < 0) {
            $observaciones[] = 'Stock negativo - Requiere reposición urgente';
        } elseif ($item['stock_actual'] <= 10) {
            $observaciones[] = 'Stock bajo - Considerar reposición';
        }
        
        if (!$item['consistente']) {
            $observaciones[] = 'Inconsistencia detectada - Verificar movimientos';
        }
        
        if (!$item['ultima_venta']) {
            $observaciones[] = 'Sin ventas registradas';
        }
        
        return empty($observaciones) ? 'Sin observaciones' : implode('. ', $observaciones);
    }
}

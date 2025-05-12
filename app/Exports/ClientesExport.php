<?php

namespace App\Exports;

use App\Models\Cliente;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ClientesExport implements     
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithColumnWidths, 
    ShouldAutoSize,
    WithTitle,
    WithEvents
{
    protected $filtros;

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Cliente::query();

        if (!empty($this->filtros['nomeCliente'])) {
            $query->where('nome', 'like', '%' . $this->filtros['nomeCliente'] . '%');
        }

        if (!empty($this->filtros['cnpjCliente'])) {
            $query->where('cnpj', 'like', '%' . $this->filtros['cnpjCliente'] . '%');
        }

        if (!empty($this->filtros['cliente_novo'])) {
            $query->where('cliente_novo', $this->filtros['cliente_novo']);
        }

        return $query->get();
    }

    
    public function map($cliente): array
    {
        return [
            $cliente->id,
            $cliente->nome ?? 'N/A',
            "'" . ($cliente->cnpj ?? 'N/A'),
            $cliente->email ?? 'N/A',

            isset($cliente->telefone) && $cliente->telefone instanceof \Illuminate\Support\Collection # garante que estamos lidando com uma coleção do Laravel.
            ? $cliente->telefone->map(fn($t) => $t->telefone ?? 'N/A')->implode(', ')
            : ($cliente->telefone->telefone ?? 'N/A'),

            $cliente->cliente_novo ? 'Cliente Novo' : 'Renovação',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome do Cliente',
            'CNPJ',
            'email',
            'Telefone',
            'Status Cliente',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '79c5b6']
            ]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 30,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 20,
        ];
    }

    public function title(): string
    {
        return 'Relatório de Cliente';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                // Cria bordas em toda a tabela
                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Ajusta alinhamento
                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // Transforma em tabela formatada
                $event->sheet->getDelegate()->setAutoFilter("A1:{$highestCol}1");
            },
        ];
    }

}

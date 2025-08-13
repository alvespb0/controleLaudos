<?php

namespace App\Exports;

use App\Models\Comissoes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ComissaoExport implements FromCollection, 
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
        $query = Comissoes::query();

        if(!empty($this->filtros['dataInicioComissoes'])){
            $query->whereDate('created_at', '>=', $this->filtros['dataInicioComissoes']);
        }

        if(!empty($this->filtros['dataFimComissoes'])){
            $query->whereDate('created_at', '>=', $this->filtros['dataFimComissoes']);            
        }

        if(!empty($this->filtros['statusComissao'])){
            $query->where('status', $this->filtros['statusComissao']);
        }

        if(!empty($this->filtros['vendedorComissoes'])){
            $query->where('vendedor_id', $this->filtros['vendedorComissoes']);
        }

        return $query->get();
    }

    public function map($comissao): array
    {
        return [
            $comissao->id,
            optional($comissao->vendedor)->usuario ?? 'N/A',
            optional($comissao->recomendador)->nome ?? 'N/A',
            optional($comissao->lead)->cliente->nome ?? 'N/A',
            $comissao->valor_comissao !== null ? 'R$' . number_format($comissao->valor_comissao, 2, ',', '') : 'N/A',
            $comissao->percentual_aplicado !== null ? number_format($comissao->percentual_aplicado, 2, ',', '') . '%' : 'N/A',
            $comissao->tipo_comissao ?? 'N/A',
            $comissao->status ?? 'N/A',
            $comissao->created_at ?? 'N/A',
        ];
    }
    public function headings(): array
    {
        return [
            'ID',
            'Vendedor',
            'Indicador',
            'Lead',
            'Valor Total da Comissão',
            'Percentual Aplicado no Lead',
            'Tipo da Comissão',
            'Status',
            'Data',
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
        return 'Relatório de Comissão Geral';
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

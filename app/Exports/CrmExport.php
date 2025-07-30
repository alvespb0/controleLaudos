<?php

namespace App\Exports;

use App\Models\Lead;
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

class CrmExport implements FromCollection, 
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
        $query = Lead::query();
        
        if (!empty($this->filtros['dataInicio'])) {
            $query->whereDate('created_at', '>=', $this->filtros['dataInicio']);
        }

        if (!empty($this->filtros['dataFim'])) {
            $query->whereDate('created_at', '<=', $this->filtros['dataFim']);
        }

        if (!empty($this->filtros['vendedor'])) {
            $query->where('vendedor_id', $this->filtros['vendedor']);
        }

        if (!empty($this->filtros['etapa'])) {
            $query->where('status_id', $this->filtros['etapa']);
        }

        return $query->get();
    }

    public function map($leads): array
    {
        return [
            $leads->id,
            optional($leads->cliente)->nome ?? 'N/A',
            optional($leads->vendedor)->usuario ?? 'N/A',
            optional($leads->status)->nome ?? 'N/A',
            $leads->num_funcionarios ?? 'N/A',
            $leads->observacoes ?? 'N/A',
            $leads->nome_contato ?? 'N/A',
            $leads->created_at ?? 'N/A',
            $leads->valor_min_sugerido ?? 'N/A',
            $leads->valor_max_sugerido ?? 'N/A',
            $leads->valor_definido ?? 'N/A',
            $leads->orcamento_gerado ? 'sim' : 'não',
            $leads->contrato_gerado ? 'sim' : 'não',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Vendedor',
            'Etapa',
            'Número de Funcionarios',
            'Observações',
            'Nome do Contato',
            'Data de Lançamento',
            'Valor Mínimo Sugerido',
            'Valor Máximo Sugerido',
            'Valor Definido',
            'Orçamento Gerado',
            'Contrato Gerado'
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
        return 'Relatório de CRM';
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

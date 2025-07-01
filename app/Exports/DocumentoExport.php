<?php

namespace App\Exports;

use App\Models\Documentos_Tecnicos;
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

class DocumentoExport implements FromCollection, 
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
        $query = Documentos_Tecnicos::query();

        if (!empty($this->filtros['dataElaboracaoInicio'])) {
            $query->whereDate('data_elaboracao', '>=', $this->filtros['dataElaboracaoInicio']);
        }

        if (!empty($this->filtros['dataElaboracaoFim'])) {
            $query->whereDate('data_elaboracao', '<=', $this->filtros['dataElaboracaoFim']);
        }

        if(!empty($this->filtros['dataConclusaoInicio'])){
            $query->whereDate('data_conclusao', '>=', $this->filtros['dataConclusaoInicio']);
        }

        if(!empty($this->filtros['dataConclusaoFim'])){
            $query->whereDate('data_conclusao', '<=', $this->filtros['dataConclusaoFim']);
        }

        if(!empty($this->filtros['tipoDocumento'])){
            $query->where('tipo_documento', $this->filtros['tipoDocumento']);
        }

        if (!empty($this->filtros['cliente'])) {
            $query->where('cliente_id', $this->filtros['cliente']);
        }

        if (!empty($this->filtros['status'])) {
            $query->where('status_id', $this->filtros['status']);
        }

        return $query->get();
    }

    public function map($docs): array
    {
        return [
            $docs->id,
            $docs->tipo_documento ?? 'N/A',
            optional($docs->cliente)->nome ?? 'N/A',
            optional($docs->tecnico)->usuario ?? 'N/A',
            optional($docs->status)->nome ?? 'N/A',
            $docs->descricao ?? 'N/A',
            $docs->data_elaboracao ?? 'N/A',
            $docs->data_conclusao ?? 'N/A',
            $docs->created_at ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tipo',
            'Cliente',
            'Técnico responsável',
            'Status',
            'Descrição',
            'Data Solicitação',
            'Data Conclusão',
            'Data de Cadastro',
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
        return 'Relatório de Documentos Técnicos';
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

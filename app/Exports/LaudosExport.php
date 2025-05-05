<?php
namespace App\Exports;

use App\Models\Laudo;
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

class LaudosExport implements 
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

    public function collection()
    {
        $query = Laudo::query();

        if (!empty($this->filtros['dataInicio'])) {
            $query->whereDate('data_conclusao', '>=', $this->filtros['dataInicio']);
        }

        if (!empty($this->filtros['dataFim'])) {
            $query->whereDate('data_conclusao', '<=', $this->filtros['dataFim']);
        }

        if(!empty($this->filtros['dataAceiteInicio'])){
            $query->whereDate('data_aceite', '>=', $this->filtros['dataAceiteInicio']);
        }

        if(!empty($this->filtros['dataAceiteFim'])){
            $query->whereDate('data_aceite', '<=', $this->filtros['dataAceiteFim']);
        }

        if (!empty($this->filtros['cliente'])) {
            $query->where('cliente_id', $this->filtros['cliente']);
        }

        if (!empty($this->filtros['status'])) {
            $query->where('status_id', $this->filtros['status']);
        }

        return $query->get();
    }

    public function map($laudo): array
    {
        return [
            $laudo->id,
            $laudo->nome ?? 'N/A',
            optional($laudo->cliente)->nome ?? 'N/A',
            optional($laudo->comercial)->usuario ?? 'N/A',
            optional($laudo->tecnico)->usuario ?? 'N/A',
            optional($laudo->status)->nome ?? 'N/A',
            $laudo->data_previsao ?? 'N/A',
            $laudo->data_conclusao ?? 'N/A',
            $laudo->data_fim_contrato ?? 'N/A',
            $laudo->data_aceite ?? 'N/A',
            $laudo->esocial ?? 'N/A',
            $laudo->numero_clientes ?? 'N/A',
            $laudo->created_at ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome do Laudo',
            'Cliente',
            'Comercial',
            'Técnico',
            'Status',
            'Data Previsão',
            'Data Conclusão',
            'Data Fim Contrato',
            'Data Aceite',
            'eSocial',
            'Nº de Clientes',
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
        return 'Relatório de Laudos';
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

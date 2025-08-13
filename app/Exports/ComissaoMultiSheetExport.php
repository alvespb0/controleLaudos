<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ComissaoMultiSheetExport implements WithMultipleSheets
{
    protected array $filtros;

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function sheets(): array
    {
        return [
            new ComissaoExport($this->filtros),
            new ParcelasComissaoExport($this->filtros),
        ];
    }
}

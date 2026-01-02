<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Laudo;
use App\Models\Integracoes;
use App\Models\Empresas_Soc;

class SocGed extends Component
{
    public $codGed;
    public $gedsEncontrados = [];
    public $laudo;

    public function mount($laudo){
        $this->laudo = $laudo;
    }
    
    public function render()
    {
        return view('livewire/laudos/soc-ged', ['gedsEncontrados' => $this->gedsEncontrados]);
    }

    public function buscarGeds(){
        $codEmpresa = Empresas_Soc::where('cnpj', $this->laudo->cliente->cnpj)
                            ->orWhere('nome', 'like', '%' . $this->laudo->cliente->nome . '%')
                            ->first()
                            ->codigo_soc ?? null;
        $this->gedsEncontrados = (new \App\Services\CodigoSocGedService)->getCodigoGed($this->codGed, $codEmpresa);
    }


}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Status;
use App\Models\Laudo;
use App\Models\Cliente;
use App\Models\Op_Tecnico;

class CardLaudosControl extends Component
{
    public $laudo;

    /**
     * Inicializa as variáveis do componente com os dados do laudo.
     *
     * @param Documentos_Tecnicos $laudo laudo técnico a ser editado.
     * 
     * @return void
     */
    public function mount($laudo)
    {
        $this->laudo = $laudo;
    }

    public function render()
    {
        $status = Status::all();
        $tecnicos = Op_Tecnico::all(); 
        return view('livewire/laudos/card-laudos-control',  ['status' => $status, 'tecnicos' => $tecnicos]);
    }
}

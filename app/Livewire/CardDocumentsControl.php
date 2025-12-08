<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Documentos_Tecnicos;
use App\Models\Status;
use App\Models\Op_Tecnico;

class CardDocumentsControl extends Component
{
    public $documento;
    public $statusAlterado;

    public function mount($documento)
    {
        $this->documento = $documento;
        $this->statusAlterado = $documento->status_id; // <<< IMPORTANTE

    }

    public function updatedStatusAlterado($statusAlterado)
    {
        $this->documento->update([
            'status_id' => $this->statusAlterado
        ]);

        $this->documento->refresh();

        $this->dispatch('toast-sucesso', message: 'Status alterado com sucesso');   
    }

    public function render()
    {
        $status = Status::all();
        $tecnicos = Op_Tecnico::all(); 

        return view('livewire.card-documents-control', ['status' => $status, 'tecnicos' => $tecnicos]);
    }
}

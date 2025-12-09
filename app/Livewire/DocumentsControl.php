<?php

namespace App\Livewire;

use Livewire\WithPagination;

use Livewire\Component;
use App\Models\Status;
use App\Models\Documentos_Tecnicos;
use App\Models\Cliente;
use App\Models\Op_Tecnico;

class DocumentsControl extends Component
{
    #use WithPagination;

    public $clienteFilter = '';
    public $dataFilterMes = '';
    public $statusFilter = '';
    public $dataFilterConclusao = '';
    public $ordenarPor = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        $status = Status::all();
        $tecnicos = Op_Tecnico::all(); 
        $query = Documentos_Tecnicos::query();

        if($this->clienteFilter){
            $clientes = Cliente::where('nome', 'like', "%{$this->clienteFilter}%")->pluck('id');
            if($clientes->isNotEmpty()){
                $query->whereIn('cliente_id', $clientes);
            }else{
                session()->flash('Error', 'Nenhum cliente localizado');
            }
        }

        if($this->dataFilterMes){
            [$ano, $mes] = explode('-', $this->dataFilterMes);

            $query->whereYear('data_elaboracao', $ano)
                             ->whereMonth('data_elaboracao', $mes);
        }

        if($this->statusFilter){
            if($this->statusFilter == "sem_status"){
                $query->where('status_id', null);
            }else{
                $query->where('status_id', $this->statusFilter);
            }
        }

        if($this->dataFilterConclusao){
            $query->where('data_conclusao', $this->dataFilterConclusao);
        }

        $query->orderBy('data_elaboracao', "{$this->ordenarPor}");
        
        $documentos = $query->paginate(6);
        return view('livewire/documentos/documents-control', ['status' => $status, 'documentos' => $documentos, 'tecnicos' => $tecnicos]);
    }

    public function toggleOrdenacao()
    {
        $this->ordenarPor = $this->ordenarPor === 'desc' ? 'asc' : 'desc';
    }
}

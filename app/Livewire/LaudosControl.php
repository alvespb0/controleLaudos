<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Status;
use App\Models\Laudo;
use App\Models\Cliente;
use App\Models\Op_Tecnico;

class LaudosControl extends Component
{
    public $clienteFilter;
    public $dataFilterMes;
    public $statusFilter;
    public $dataFilterConclusao;
    public $ordenarPor = 'desc';

    /**
     * Reseta a paginação quando algum campo de busca é atualizado.
     *
     * Este método é automaticamente identificado pelo Livewire
     * por conta do prefixo "updating".
     *
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tecnicos = Op_Tecnico::all(); 
        $status = Status::all();
        $query = Laudo::query();

        if($this->clienteFilter){
            $clientes = Cliente::where('nome', 'like', "%{$this->clienteFilter}%")->pluck('id');
            if($clientes->isNotEmpty()){
                $query->whereIn('cliente_id', $clientes);
            }
        }

        if($this->dataFilterMes){
            [$ano, $mes] = explode('-', $this->dataFilterMes);

            $query->whereYear('data_aceite', $ano)
                             ->whereMonth('data_aceite', $mes);
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

        $query->orderBy('data_aceite', "{$this->ordenarPor}");

        $laudos = $query->paginate(6);
        return view('livewire/laudos/laudos-control', ['status' => $status, 'laudos' => $laudos, 'tecnicos' => $tecnicos]);
    }

    /**
     * Alterna a ordenação entre ASC e DESC.
     *
     * Usado em botões de ordenação da interface.
     *
     * @return void
     */
    public function toggleOrdenacao()
    {
        $this->ordenarPor = $this->ordenarPor === 'desc' ? 'asc' : 'desc';
    }
}

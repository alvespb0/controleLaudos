<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\WithPagination;

class ClienteFilter extends Component
{
    use WithPagination;
    
    public $search = '';
    
    // Resetar paginação quando o search mudar
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $cliente = Cliente::where('nome', 'like', '%'. $this->search .'%')
                ->orWhere('cnpj', 'like', '%'. $this->search . '%')
                ->paginate(10);
        return view('livewire.cliente-filter',  ['clientes'=> $cliente]);
    }
}

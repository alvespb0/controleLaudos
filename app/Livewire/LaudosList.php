<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Laudo;
use Livewire\WithPagination;

class LaudosList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $busca = $this->search;
        $laudos = $laudos = Laudo::with('cliente')
            ->when($busca, function ($query, $busca) {
                $query->whereHas('cliente', function ($q) use ($busca) {
                    $q->where('nome', 'like', "%$busca%")
                    ->orWhere('cnpj', 'like', "%$busca%");
                });
            })
            ->paginate(10);

        return view('livewire/laudos/laudos-list', ['laudos' => $laudos]);
    }
}

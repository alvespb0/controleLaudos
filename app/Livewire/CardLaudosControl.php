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
    public $statusAlterado;
    public $dataConclusaoAlterado;
    public $tecnicoAlterado;
    public $observacaoAlterado;
    
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
        $this->statusAlterado = $laudo->status_id;
        $this->dataConclusaoAlterado = $laudo->data_conclusao;
        $this->tecnicoAlterado = $laudo->tecnico_id;
        $this->observacaoAlterado = $laudo->observacao;
    }

    /**
     * Atualiza o status do laudo quando a variável `statusAlterado` é modificada.
     *
     * Atualiza o campo `status_id` do laudo no banco de dados e envia uma notificação de sucesso.
     *
     * @param int $statusAlterado Novo status selecionado para o laudo.
     * 
     * @return void
     */
    public function updatedStatusAlterado()
    {
        $this->laudo->update([
            'status_id' => $this->statusAlterado
        ]);

        $this->laudo->refresh();

        $this->dispatch('toast-sucesso', message: 'Status atualizado com sucesso');   
    }

    /**
     * Atualiza a data de conclusão do laudo quando a variável `dataConclusaoAlterado` é modificada.
     *
     * Atualiza o campo `data_conclusao` do laudo no banco de dados e envia uma notificação de sucesso.
     *
     * @return void
     */
    public function updatedDataConclusaoAlterado()
    {
        $this->laudo->update([
            'data_conclusao' => $this->dataConclusaoAlterado
        ]);

        $this->laudo->refresh();

        $this->dispatch('toast-sucesso', message: 'Data de conclusão atualizado com sucesso');   
    }

    /**
     * Atualiza o técnico do laudo quando a variável `tecnicoAlterado` é modificada.
     *
     * Atualiza o campo `tecnico_id` do laudo no banco de dados e envia uma notificação de sucesso.
     *
     * @return void
     */
    public function updatedTecnicoAlterado(){
        $this->laudo->update([
            'tecnico_id' => $this->tecnicoAlterado
        ]);

        $this->laudo->refresh();

        $this->dispatch('toast-sucesso', message: 'Técnico atualizado com sucesso');   
    }

    public function updatedObservacaoAlterado(){
        $this->laudo->update([
            'observacao' => $this->observacaoAlterado
        ]);

        $this->laudo->refresh();

        $this->dispatch('toast-sucesso', message: 'Observação atualizada com sucesso');   
    }

    public function render()
    {
        $status = Status::all();
        $tecnicos = Op_Tecnico::all(); 
        return view('livewire/laudos/card-laudos-control',  ['status' => $status, 'tecnicos' => $tecnicos]);
    }
}

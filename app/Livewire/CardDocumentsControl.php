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
    public $tecnicoAlterado;
    public $dataConclusaoAlterado;

    /**
     * Inicializa as variáveis do componente com os dados do documento.
     *
     * @param Documentos_Tecnicos $documento Documento técnico a ser editado.
     * 
     * @return void
     */
    public function mount($documento)
    {
        $this->documento = $documento;
        $this->statusAlterado = $documento->status_id;
        $this->tecnicoAlterado = $documento->tecnico_id;
        $this->dataConclusaoAlterado = $documento->data_conclusao;
    }

    
    /**
     * Atualiza o status do documento quando a variável `statusAlterado` é modificada.
     *
     * Atualiza o campo `status_id` do documento no banco de dados e envia uma notificação de sucesso.
     *
     * @param int $statusAlterado Novo status selecionado para o documento.
     * 
     * @return void
     */
    public function updatedStatusAlterado($statusAlterado)
    {
        $this->documento->update([
            'status_id' => $this->statusAlterado
        ]);

        $this->documento->refresh();

        $this->dispatch('toast-sucesso', message: 'Status atualizado com sucesso');   
    }

    /**
     * Atualiza o técnico do documento quando a variável `tecnicoAlterado` é modificada.
     *
     * Atualiza o campo `tecnico_id` do documento no banco de dados e envia uma notificação de sucesso.
     *
     * @return void
     */
    public function updatedTecnicoAlterado(){
        $this->documento->update([
            'tecnico_id' => $this->tecnicoAlterado
        ]);

        $this->documento->refresh();

        $this->dispatch('toast-sucesso', message: 'Técnico atualizado com sucesso');   
    }

    /**
     * Atualiza a data de conclusão do documento quando a variável `dataConclusaoAlterado` é modificada.
     *
     * Atualiza o campo `data_conclusao` do documento no banco de dados e envia uma notificação de sucesso.
     *
     * @return void
     */
    public function updatedDataConclusaoAlterado(){
        $this->documento->update([
            'data_conclusao' => $this->dataConclusaoAlterado
        ]);

        $this->documento->refresh();

        $this->dispatch('toast-sucesso', message: 'Data de conclusão atualizado com sucesso');   
    }

    /**
     * Renderiza a view do componente e envia os dados necessários para o front-end.
     *
     * Carrega todos os status e técnicos disponíveis para exibir na interface.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $status = Status::all();
        $tecnicos = Op_Tecnico::all(); 

        return view('livewire/documentos/card-documents-control', ['status' => $status, 'tecnicos' => $tecnicos]);
    }
}

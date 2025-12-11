<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Status;
use App\Models\Laudo;
use App\Models\Cliente;
use App\Models\Op_Tecnico;
use App\Models\Responsaveis;

class CardLaudosControl extends Component
{
    public $laudo;
    public $statusAlterado;
    public $dataConclusaoAlterado;
    public $responsavelLevantamentoAlterado;
    public $responsavelEngenheiroAlterado;
    public $responsavelDigitacaoAlterado;
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
        $this->responsavelLevantamentoAlterado = optional($laudo->tecnicoLevantamento())->tecnico_id;
        $this->responsavelEngenheiroAlterado = optional($laudo->engenheiroResponsavel())->tecnico_id;
        $this->responsavelDigitacaoAlterado = optional($laudo->responsavelDigitacao())->tecnico_id;
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
     * Atualiza o técnico do laudo quando a variável `responsavelLevantamentoAlterado` é modificada.
     * Tabela responsáveis, considerando as relações N:N
     *
     *
     * @return void
     */
    public function updatedResponsavelLevantamentoAlterado(){

        if (empty($this->responsavelLevantamentoAlterado)) {

            $responsavel = $this->laudo->tecnicoLevantamento();

            if ($responsavel) {
                $responsavel->delete();
            }

            $this->laudo->refresh();

            return $this->dispatch(
                'toast-sucesso',
                message: 'Responsável pelo levantamento removido com sucesso'
            );
        }


        Responsaveis::updateOrCreate(
            [
                'laudo_id' => $this->laudo->id,
                'tipo' => 'levantamento'
            ],
            [
                'tecnico_id' => $this->responsavelLevantamentoAlterado
            ]
        );
        $this->laudo->refresh();

        $this->dispatch('toast-sucesso', message: 'Técnico responsável pelo levantamento atualizado com sucesso');   
    }

    
    /**
     * Atualiza o técnico do laudo quando a variável `responsavelDigitacaoAlterado` é modificada.
     * Tabela responsáveis, considerando as relações N:N
     *
     *
     * @return void
     */
    public function updatedResponsavelDigitacaoAlterado(){
        
        if (empty($this->responsavelDigitacaoAlterado)) {

            $responsavel = $this->laudo->responsavelDigitacao();

            if ($responsavel) {
                $responsavel->delete();
            }

            $this->laudo->refresh();

            return $this->dispatch(
                'toast-sucesso',
                message: 'Responsável pela digitação removido com sucesso'
            );
        }

        Responsaveis::updateOrCreate(
            [
                'laudo_id' => $this->laudo->id,
                'tipo' => 'digitacao'
            ],
            [
                'tecnico_id' => $this->responsavelDigitacaoAlterado
            ]
        );
        $this->laudo->refresh();

        $this->dispatch('toast-sucesso', message: 'Técnico responsável pela digitação atualizado com sucesso');   
    }

    
    /**
     * Atualiza o técnico do laudo quando a variável `ResponsavelEngenheiroAlterad` é modificada.
     * Tabela responsáveis, considerando as relações N:N
     *
     *
     * @return void
     */
    public function updatedResponsavelEngenheiroAlterado(){
        if (empty($this->responsavelEngenheiroAlterado)) {

            $responsavel = $this->laudo->engenheiroResponsavel();

            if ($responsavel) {
                $responsavel->delete();
            }

            $this->laudo->refresh();

            return $this->dispatch(
                'toast-sucesso',
                message: 'Engenheiro responsável removido com sucesso'
            );
        }

        Responsaveis::updateOrCreate(
            [
                'laudo_id' => $this->laudo->id,
                'tipo' => 'engenheiro'
            ],
            [
                'tecnico_id' => $this->responsavelEngenheiroAlterado
            ]
        );
        $this->laudo->refresh();

        $this->dispatch('toast-sucesso', message: 'Engenheiro responsável atualizado com sucesso');   
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

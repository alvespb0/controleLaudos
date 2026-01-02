<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Response;

use Livewire\Component;
use App\Models\Laudo;
use App\Models\Integracao;
use App\Models\Empresas_Soc;

use App\Services\DownloadSocGedService;

class SocGed extends Component
{
    public $codGed;
    public $gedsEncontrados = array();
    public $laudo;
    public $codEmpresa;
    public ?string $erroGed = null;

    public function mount($laudo){
        $this->laudo = $laudo;
    }
    
    public function render()
    {
        return view('livewire/laudos/soc-ged', ['gedsEncontrados' => $this->gedsEncontrados]);
    }

    public function buscarGeds(){
        
        $this->erroGed = null;
        $this->gedsEncontrados = [];

        try{
            $empresa = Empresas_Soc::where('cliente_id', $this->laudo->cliente_id)
                                ->orWhere('cnpj', $this->laudo->cliente->cnpj)
                                ->orWhere('nome', 'like', '%' . $this->laudo->cliente->nome . '%')
                                ->first();
            
            if (!$empresa) {
                $this->erroGed = 'Empresa não encontrada no SOC para este cliente.';
                return;
            }

            if($empresa != null && !$empresa->cliente_id){
                $empresa->update([
                    'cliente_id' => $this->laudo->cliente_id
                ]);
            }

            $this->codEmpresa = $empresa->codigo_soc;
            $this->gedsEncontrados = (new \App\Services\CodigoSocGedService)->getCodigoGed($this->codGed, $this->codEmpresa);
            
            if (empty($this->gedsEncontrados)) {
                $this->erroGed = 'Nenhum GED encontrado para o tipo selecionado.';
            }

        }catch (\Throwable $e) {
            logger()->error('Erro ao buscar GEDs', [
                'erro' => $e->getMessage()
            ]);

            $this->erroGed = 'Erro inesperado ao buscar GEDs. Tente novamente.';
        }

    }

    public function baixarGed($codGed){
        $auth = Integracao::where('slug', 'ws_soc_download_ged')->first();
        $service = new \App\Services\DownloadSocGedService($auth->username, $auth->getDecryptedPassword(), $this->codEmpresa, $codGed);

        $file = $service->requestDownload();

        if($file['success']){
            return Response::download(storage_path($file['file']))->deleteFileAfterSend(true);;
        }
    }
}

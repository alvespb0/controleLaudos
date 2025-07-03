<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ZappyController extends Controller
{    
    /**
     * Cria um novo atendimento (ticket) na plataforma Zappy e transfere para o setor adequado com base no usuário logado.
     *
     * Este método realiza os seguintes passos:
     * 1. Limpa e formata o número de telefone do request para o padrão internacional.
     * 2. Envia uma requisição para a API do Zappy para criar um novo atendimento com a mensagem informada.
     * 3. Caso a criação seja bem-sucedida, transfere o atendimento para a fila correta (chamando `transferAtendimento`).
     * 4. Define mensagens de sessão para informar sucesso ou erro ao usuário.
     *
     * @param \Illuminate\Http\Request $request Request HTTP contendo:
     *  - `numero`: número de telefone do cliente (string)
     *  - `mensagem`: mensagem a ser enviada (string)
     *
     * @return \Illuminate\Http\RedirectResponse Redireciona para o dashboard com mensagem de sucesso ou erro.
     *
     * @throws \Exception Em caso de falha na criação do atendimento ou falha de comunicação com a API.
     */
    public function createAtendimento(Request $request){
        $token = env('ZAPPY_TOKEN');
        try{
            $numeroLimpo = preg_replace('/[()\s-]+/', '', $request->numero);
            $numero = '55' . $numeroLimpo;
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $token,
            ])->post("https://api-segmetre.zapplataforma.chat/api/send/$numero",[
                'body' => $request->mensagem,
                'connectionFrom' => 0,
                'ticketStrategy' => 'create',
            ]);
            
            if($response->status() == 200){
                $data = $response->json();
                $ticketId = $data['message']['ticketId'];
                $transfere = $this->transferAtendimento($ticketId);
                if($transfere != 200){
                    session()->flash('error', 'Erro ao abrir o atendimento no zappy'); # ele vai abrir sem setor, então o usuário não vai conseguir ver
                    return redirect()->route('dashboard.show');
                }else{
                    session()->flash('mensagem', 'Atendimento criado com sucesso');
                    return redirect()->route('dashboard.show');
                }
            }else{
                session()->flash('error', 'Erro ao abrir o atendimento no zappy');
                \Log::error('Erro ao criar o atendimento:', [
                    'error' => $response->body(),
                ]);
                return redirect()->route('dashboard.show');
            }
            
        }catch (\Exception $e) {
            session()->flash('error', 'Erro ao abrir o atendimento no zappy');
            \Log::error('Erro ao criar o atendimento:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard.show');
        }
    }

    /**
     * Transfere um atendimento (ticket) para uma fila específica, com base no tipo de usuário autenticado.
     *
     * Dependendo do tipo do usuário (`seguranca`, `comercial`, ou outro), o atendimento é redirecionado para a
     * respectiva fila da plataforma de atendimento Zappy (via API).
     *
     * @param int $ticketId ID do atendimento (ticket) que será transferido.
     * 
     * @return int Código HTTP da resposta da API (ex: 200 em caso de sucesso, 400 em caso de erro).
     *
     * @throws \Exception Em caso de erro na requisição ou falha na comunicação com a API.
     *
     * Lógica das filas:
     * - Tipo `seguranca` → fila 2
     * - Tipo `comercial` → fila 7
     * - Qualquer outro tipo (ex: TI) → fila 11
     */
    public function transferAtendimento($ticketId){
        $token = env('ZAPPY_TOKEN');
        try{
            if(Auth::user()->tipo == 'seguranca'){
                $queue = 2; # setor de segurança
            }else if(Auth::user()->tipo == 'comercial'){
                $queue = 7; # setor de comercial
            }else{
                $queue = 11; # setor de TI 
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $token,
            ])->post("https://api-segmetre.zapplataforma.chat/api/tickets/$ticketId/transfer",[
                'queueId' => $queue,
                'userId' => 0,
                'connectionId' => 0
            ]);            

            return $response->status();
        }catch (\Exception $e) {
            session()->flash('error', 'Erro ao transferir o atendimento');
            \Log::error('Erro ao transferir o atendimento:', [
                'error' => $e->getMessage(),
            ]);
            return 400;
        }
    }

    public function encaminhaOrcamentoCliente(Request $request){
        $token = env('ZAPPY_TOKEN');
        $arquivo = $request->file('fileOrcamento');
        $mimeType = $arquivo->getMimeType();
        $tipoDocumento = $this->getTipoDocumento($mimeType);

        try{
            $numeroLimpo = preg_replace('/[()\s-]+/', '', $request->telefone);
            $numero = '55' . $numeroLimpo;
            
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->attach(
                'media',
                file_get_contents($arquivo->getRealPath()),
                $arquivo->getClientOriginalName()
            )->post("https://api-segmetre.zapplataforma.chat/api/send/$tipoDocumento/$numero",[
                'caption' => $request->mensagem, # legenda para o arquivo
                'connectionFrom' => 0,
                'ticketStrategy' => 'create',
            ]);
            
            if($response->status() == 200){
                $data = $response->json();
                $ticketId = $data['message']['ticketId'];
                $transfere = $this->transferAtendimento($ticketId);
                if($transfere != 200){
                    session()->flash('error', 'Erro ao abrir o atendimento no zappy'); # ele vai abrir sem setor, então o usuário não vai conseguir ver
                    return redirect()->route('dashboard.show');
                }else{
                    session()->flash('mensagem', 'Atendimento criado com sucesso');
                    return redirect()->route('dashboard.show');
                }
            }else{
                session()->flash('error', 'Erro ao abrir o atendimento no zappy');
                \Log::error('Erro ao criar o atendimento:', [
                    'error' => $response->body(),
                ]);
                return redirect()->route('dashboard.show');
            }
            
        }catch (\Exception $e) {
            session()->flash('error', 'Erro ao abrir o atendimento no zappy');
            \Log::error('Erro ao criar o atendimento:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard.show');
        }
    }

    public function getTipoDocumento($mimeType){
        $tipoDocumento = 'document'; 

        switch (true) {
            case str_starts_with($mimeType, 'image/'):
                $tipoDocumento = 'image';
                break;

            case str_starts_with($mimeType, 'video/'):
                $tipoDocumento = 'video';
                break;

            case str_starts_with($mimeType, 'audio/'):
                $tipoDocumento = 'audio';
                break;

            case $mimeType === 'audio/ogg' || $mimeType === 'audio/opus':
                $tipoDocumento = 'voice';
                break;

            default:
                $tipoDocumento = 'document';
                break;
        }

        return $tipoDocumento;
    }
}

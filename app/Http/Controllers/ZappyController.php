<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ZappyController extends Controller
{    
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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ZappyController extends Controller
{
    private const BEARER_TOKEN  = 'Bearer a92b89c065bc2d2b133ed28bf45994bf4d953a474d45170cc890c296c68072a9ba7fa11c747fa517ab894d3aedae3419617c9bcecebde7e3b0361ed7cdacc07e6e3e001803784d60932292f0ef06650e8a0ed1caea4fdc3292585cf7f02ce634403b131a3d0084a71d0a6ea83a999ee31cb685961b82aed5ae48da330e';

    public function createAtendimento(Request $request){
        try{
            $numeroLimpo = preg_replace('/[()\s-]+/', '', $request->numero);
            $numero = '55' . $numeroLimpo;
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => self::BEARER_TOKEN,
            ])->post("https://api-segmetre.zapplataforma.chat/api/send/$numero",[
                'body' => $request->mensagem,
                'connectionFrom' => 0,
                'ticketStrategy' => 'create',
            ]);
            
            if($response->status() == 200){
                $data = $response->json();
                $ticketId = $data['message']['ticketId'];
            }else{
                session()->flash('error', 'Erro ao abrir o atendimento no zappy');
                \Log::error('Erro ao criar o atendimento:', [
                    'error' => $e->getMessage(),
                ]);
                return route('dashboard.show');
            }
            
        }catch (\Exception $e) {
            session()->flash('error', 'Erro ao abrir o atendimento no zappy');
            \Log::error('Erro ao criar o atendimento:', [
                'error' => $e->getMessage(),
            ]);
            return route('dashboard.show');
        }
    }

    public function transferAtendimento($ticketId){
        try{
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => self::BEARER_TOKEN,
            ])->post("https://api-segmetre.zapplataforma.chat/api/tickets/$ticketId/transfer",[
                'queue'
            ]);            
        }catch (\Exception $e) {
            session()->flash('error', 'Erro ao abrir o atendimento no zappy');
            \Log::error('Erro ao criar o atendimento:', [
                'error' => $e->getMessage(),
            ]);
            return route('dashboard.show');
        }
    }
}

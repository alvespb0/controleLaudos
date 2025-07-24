<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ClienteRequest;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\Cliente;
use App\Models\Telefone;
use App\Models\Endereco_Cliente;
use App\Models\Dados_Cobranca;

class ClienteController extends Controller
{
    /**
    * Retorna a pagina de cadastro do Cliente
    * @return View
    */
   public function cadastroCliente(){
       return view("Cliente/Cliente_new");
   }

   /**
    * Recebe uma request via POST valida os dados, se validado cadastra no banco
    * Se não retorna o erro
    * @param ClienteRequest $request
    * @return Redirect
    */
   public function createCliente(ClienteRequest $request){
       $request->validated();

       $cliente = Cliente::create([
            'nome'=> $request->nome,
            'cnpj' => $request->cnpj,
            'email' => $request->email,
            'cliente_novo' => $request->cliente_novo
       ]);

        foreach($request->telefone as $telefone){
            Telefone::create([
                'telefone' =>  $telefone,
                'cliente_id' => $cliente->id
            ]);
        }

        $endereco = "$request->rua $request->numero, $request->bairro, $request->cidade - $request->uf, $request->cep, Brasil"; # formata a string para consulta ao nominantim

        $coordenadas = $this->getCordsCli($endereco); # retorna lat e lon SE NÃO ACHAR RETORNA NULL

        $distancia = null;
        if($coordenadas){
            $distancia = $this->calcularDistanciaORS(env('LAT_ORIGEM'), env('LON_ORIGEM'), $coordenadas['lat'], $coordenadas['lon']); # retorna certinho a distancia em km
            
            if($distancia == null){
                $distancia = $this->calcularDistanciaHaversine(env('LAT_ORIGEM'), env('LON_ORIGEM'), $coordenadas['lat'], $coordenadas['lon']); # fallback para haversine
                Log::info("Fallback Haversine usado para calcular distância do cliente ID {$cliente->id} no endereço: {$endereco}");
            }
        }else{
            Log::warning('Endereço sem coordenadas: ' . $endereco . '| Coordenadas'. $coordenadas);
        }
        
        Endereco_Cliente::create([
            'cliente_id' => $cliente->id,
            'cep' => $request->cep,
            'bairro' => $request->bairro,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'latitude' => $coordenadas['lat'] ?? null,
            'longitude' => $coordenadas['lon'] ?? null,
            'distancia' => $distancia ?? null
        ]);

        Dados_Cobranca::create([
            'cliente_id' => $cliente->id,
            'cep' => $request->cep_cobranca,
            'bairro' => $request->bairro_cobranca,
            'rua' => $request->rua_cobranca,
            'numero' => $request->numero_cobranca,
            'complemento' => $request->complemento_cobranca,
            'cidade' => $request->cidade_cobranca,
            'uf' => $request->uf_cobranca,
            'email_cobranca' => $request->email_cobranca,
            'telefone_cobranca' => $request->telefone_cobranca,
        ]);

       session()->flash('mensagem', 'Cliente registrado com sucesso');

       return redirect()->route('readCliente');
   }

   /**
    * retorna os cleintes cadastrados no banco
    * @return Array
    */
   public function readCliente(){
        $clientes = Cliente::orderBy('nome', 'asc')->paginate(10);
        return view('Cliente/Cliente_show', ['clientes'=> $clientes]);
   }

   /**
    * recebe um ID valida se o ID é válido via find or fail
    * se for válido retorna o formulario de edição do cliente 
    * @param int $id
    * @return array
    */
   public function alteracaoCliente($id){
       $cliente = Cliente::findOrFail($id);
       return view('Cliente/Cliente_edit', ['cliente' => $cliente]);
   }

    /**
        * Recebe uma request faz a validação dos dados e faz o update dado o id
        * @param Request
        * @param int $id
        * @return Redirect
        */
    public function updateCliente(ClienteRequest $request, $id){
        $request->validated();
 
        $cliente = Cliente::findOrFail($id);
 
        $cliente->update([
            'nome'=> $request->nome,
            'cnpj' => $request->cnpj,
            'email' => $request->email,
            'cliente_novo' => $request->cliente_novo
        ]);
 
        $cliente->telefone()->delete();
 
        foreach($request->telefone as $telefone){
             Telefone::create([
                 'telefone' => $telefone,
                 'cliente_id' => $cliente->id
             ]);
         }

        $cliente->endereco()->delete();

        $endereco = "$request->rua $request->numero, $request->bairro, $request->cidade - $request->uf, $request->cep, Brasil"; # formata a string para consulta ao nominantim

        $coordenadas = $this->getCordsCli($endereco); # retorna lat e lon SE NÃO ACHAR RETORNA NULL

        $distancia = null;
        if($coordenadas){
            $distancia = $this->calcularDistanciaORS(env('LAT_ORIGEM'), env('LON_ORIGEM'), $coordenadas['lat'], $coordenadas['lon']); # retorna certinho a distancia em km
            
            if($distancia == null){
                $distancia = $this->calcularDistanciaHaversine(env('LAT_ORIGEM'), env('LON_ORIGEM'), $coordenadas['lat'], $coordenadas['lon']); # fallback para haversine
                Log::info("Fallback Haversine usado para calcular distância do cliente ID {$cliente->id} no endereço: {$endereco}");
            }
        }else{
            Log::warning('Endereço sem coordenadas: ' . $endereco . '| Coordenadas'. $coordenadas);
        }

        Endereco_Cliente::create([
            'cliente_id' => $cliente->id,
            'cep' => $request->cep,
            'bairro' => $request->bairro,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'latitude' => $coordenadas['lat'] ?? null,
            'longitude' => $coordenadas['lon'] ?? null,
            'distancia' => $distancia ?? null
        ]);

        $cliente->dadosCobranca()->delete();

        Dados_Cobranca::create([
            'cliente_id' => $cliente->id,
            'cep' => $request->cep_cobranca,
            'bairro' => $request->bairro_cobranca,
            'rua' => $request->rua_cobranca,
            'numero' => $request->numero_cobranca,
            'complemento' => $request->complemento_cobranca,
            'cidade' => $request->cidade_cobranca,
            'uf' => $request->uf_cobranca,
            'email_cobranca' => $request->email_cobranca,
            'telefone_cobranca' => $request->telefone_cobranca,
        ]);

        session()->flash('mensagem', 'Cliente Alterado com sucesso');
 
        return redirect()->route('readCliente');
    }
 
 
   /**
    * recebe o id e deleta o cliente vinculado nesse ID
    * @param int $id
    * @return view
    */
   public function deleteCliente($id){
       $cliente = Cliente::findOrFail($id);

       $cliente->delete();

       session()->flash('mensagem', 'Cliente Excluido com sucesso');

       return redirect()->route('readCliente');
   }

    /**
     * recebe uma request e busca no banco um cliente com esse nome ou CNPJ utilizando like
     * @param Request
     * @return Array
     */
    public function filterCliente(Request $request){
        $cliente = Cliente::where('nome', 'like', '%'. $request->cliente .'%')
                        ->orWhere('cnpj', 'like', '%'. $request->cliente . '%')
                        ->paginate(10);
        return view('Cliente/Cliente_show', ['clientes'=> $cliente]);
    }
   
    /**
     * Obtém as coordenadas geográficas (latitude e longitude) de um endereço utilizando o serviço
     * Nominatim (OpenStreetMap).
     *
     * @param string $endereco Endereço completo em formato texto (ex: "Rua Exemplo, 123, Bairro, Cidade - UF, CEP").
     *
     * @return array|null Retorna um array com as chaves 'lat' e 'lon' em caso de sucesso, ou null se a geolocalização
     *         não for encontrada ou ocorrer erro na requisição.
     *
     * @throws \Exception Caso ocorra erro inesperado durante a requisição.
     *
     * @example
     * $coordenadas = $this->getCordsCli("Av. Brasil, 100, Centro, São Paulo - SP, 01000-000");
     * // Resultado: ['lat' => '-23.550520', 'lon' => '-46.633308']
     *
     * @see https://nominatim.org/release-docs/latest/api/Search/ Documentação oficial da API Nominatim.
     */
    private function getCordsCli($endereco){
        try{
            $response = Http::withHeaders([
                'User-Agent' => 'controleLaudos/1.0 ti@segmetreambiental.com.br'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $endereco,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1,
                'countrycodes' => 'br'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data[0])) {
                    return [
                        'lat' => $data[0]['lat'],
                        'lon' => $data[0]['lon']
                    ];
                } else {
                    Log::warning('Nominatim não retornou dados.', ['endereco' => $endereco]);
                    return null;
                }
            } else {
                Log::error('Erro ao consultar Nominatim', [
                    'status' => $response->status(),
                    'endereco' => $endereco,
                    'body' => $response->body()
                ]);
                return null;
            }
        }catch (\Exception $e) {
            \Log::error('Erro ao pegar as coordenadas do endereço', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Calcula a distância em metros entre dois pontos geográficos (origem e destino)
     * utilizando a API da OpenRouteService (ORS), com o modo de direção "driving-car".
     *
     * @param float $latOrigem Latitude do ponto de origem.
     * @param float $lonOrigem Longitude do ponto de origem.
     * @param float $latDestino Latitude do ponto de destino.
     * @param float $lonDestino Longitude do ponto de destino.
     *
     * @return float|null Distância em metros entre os dois pontos. Retorna null se a requisição falhar.
     *
     * @throws \Exception Lança exceção se houver erro durante a chamada da API.
     */
    private function calcularDistanciaORS($latOrigem, $lonOrigem, $latDestino, $lonDestino){
        $apiKey = env('ORS_API_KEY');

        $url = 'https://api.openrouteservice.org/v2/directions/driving-car';

        try{
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'coordinates' => [
                    [$lonOrigem, $latOrigem],
                    [$lonDestino, $latDestino],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $distanciaMetros !== null ? $distanciaMetros / 1000 : null; // distância em km
            } else {
                Log::error('Erro na resposta da ORS calcularDistanciaORS', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            }
        }catch (\Exception $e) {
            \Log::error('Erro ao calcular distancia do cliente:', [
                'error' => $e->getMessage(),
                'origem' => [$latOrigem, $lonOrigem],
                'destino' => [$latDestino, $lonDestino]
            ]);
            return null;
        }
    }

    /**
     * Calcula a distância em linha reta (distância geodésica) entre dois pontos geográficos
     * utilizando a fórmula de Haversine. Essa distância é uma aproximação e não considera
     * estradas ou rotas reais — apenas a menor distância entre dois pontos na superfície da Terra.
     *
     * @param float $lat1 Latitude do ponto de origem.
     * @param float $lon1 Longitude do ponto de origem.
     * @param float $lat2 Latitude do ponto de destino.
     * @param float $lon2 Longitude do ponto de destino.
     *
     * @return float Distância aproximada em quilômetros (km) entre os dois pontos.
     */
    private function calcularDistanciaHaversine($lat1, $lon1, $lat2, $lon2) { /* usando a formula de haversine, porém só retorna em ponto reto */
        $earthRadius = 6371; // Raio médio da Terra em km

        // 1. Converter diferenças de latitude e longitude para radianos
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        // 2. Calcular o valor 'a' usando a fórmula do Haversine
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);

        // 3. Calcular o valor 'c' que é o ângulo central entre os dois pontos
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // 4. Multiplicar o ângulo pelo raio da Terra para obter a distância
        return $earthRadius * $c; // distância em km
    }

}

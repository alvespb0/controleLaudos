<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\CA_Tokens;
use App\Models\Lead;

class ContaAzulController extends Controller
{
    /**
     * Redireciona o usuário para a página de autorização OAuth2 da Conta Azul.
     *
     * Este método monta a URL de autorização da API da Conta Azul utilizando as variáveis de ambiente
     * definidas no `.env` (CLIENT_ID_CA e CONTA_AZUL_REDIRECT_URI). Ele inicia o fluxo OAuth2,
     * redirecionando o usuário para a página de login/autorização da Conta Azul, onde será gerado o
     * código de autorização necessário para obter o access token.
     *
     * Em caso de erro durante o processo, o método registra a exceção no log e redireciona o usuário
     * de volta ao dashboard com uma mensagem de erro.
     *
     * @return \Illuminate\Http\RedirectResponse Redireciona para a URL de autorização da Conta Azul
     *                                           ou para o dashboard em caso de falha.
     *
     * @throws \Exception Caso ocorra algum erro inesperado na construção ou redirecionamento da URL.
     *
     * @example
     * // Exemplo de uso:
     * // Inicia o fluxo OAuth da Conta Azul
     * return $this->getAuthorizationToken();
     *
     * // Isso redirecionará o usuário para:
     * // https://auth.contaazul.com/oauth2/authorize?response_type=code&client_id=XXXX&redirect_uri=XXXX
     */
    public function getAuthorizationToken(){
        $urlRedirect = env('CONTA_AZUL_REDIRECT_URI');
        $client_id = env('CLIENT_ID_CA');
        try{
            $url = "https://auth.contaazul.com/oauth2/authorize?" .
                    "response_type=code" .
                    "&client_id={$client_id}" .
                    "&redirect_uri={$urlRedirect}" .
                    "&scope=openid+profile+aws.cognito.signin.user.admin" .
                    "&state=xyz123";
            return redirect($url);
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao conseguir o token de autorização do CA');
            \Log::error('Erro ao conseguir o Auth Token do Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard.show');
        }
    }

    /**
     * Obtém o access token e o refresh token da Conta Azul via OAuth2.
     *
     * Este método realiza a requisição HTTP para o endpoint de token da Conta Azul,
     * utilizando o código de autorização previamente obtido no fluxo OAuth2.
     * Ele envia os parâmetros exigidos (client_id, client_secret, grant_type, code e redirect_uri)
     * e autentica a requisição via header "Authorization: Basic base64(client_id:client_secret)".
     *
     * Caso a resposta da API seja bem-sucedida, retorna o corpo JSON contendo os tokens.
     * Em caso de falha, registra os detalhes do erro no log e retorna o status HTTP.
     *
     * Se ocorrer uma exceção durante o processo, o usuário é redirecionado ao dashboard com uma mensagem de erro.
     *
     * @return array|int|\Illuminate\Http\RedirectResponse
     *         - array: Retorna o JSON decodificado com os tokens em caso de sucesso.
     *         - int: Retorna o código de status HTTP em caso de erro na resposta.
     *         - \Illuminate\Http\RedirectResponse: Redireciona para o dashboard em caso de exceção.
     *
     * @throws \Exception Caso ocorra um erro inesperado durante a requisição.
     *
     * @example
     * // Obtém os tokens de acesso da Conta Azul
     * $tokens = $this->getAccessToken();
     *
     * // Exemplo de resposta bem-sucedida:
     * // [
     * //     "access_token" => "eyJraWQiOiJ...",
     * //     "refresh_token" => "eyJjdHkiOiJ...",
     * //     "expires_in" => 3600,
     * //     "token_type" => "Bearer"
     * // ]
     */
    public function getAccessToken(){
        $data = [
            'client_id' => env('CLIENT_ID_CA'),
            'client_secret' => env('SECRET_ID_CA'),
            'grant_type' => 'authorization_code',
            'code' => env('AUTH_TOKEN_CA'),
            'redirect_uri' => env('CONTA_AZUL_REDIRECT_URI')
        ];
        try{
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($data['client_id'] . ':' . $data['client_secret']),
                ])
                ->post('https://auth.contaazul.com/oauth2/token', $data);

            if($response->ok()){
                return $response->json();
            } else {
                \Log::error('Erro ao obter access token', ['status' => $response->status(), 'body' => $response->body()]);
                return $response->status();
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao conseguir o token de acesso e refresh do CA');
            \Log::error('Erro ao conseguir o access Token do Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard.show');
        }
    }

    /**
     * Cria ou atualiza o token de acesso da Conta Azul no banco de dados.
     *
     * Este método garante que sempre exista apenas um registro de token válido na tabela `CA_Tokens`.
     * 
     * Caso não exista nenhum token salvo, ele obtém um novo par de tokens (access e refresh) chamando
     * o método {@see getAccessToken()}, e salva o resultado na tabela.
     *
     * Caso já exista um token salvo, o método realiza o fluxo de **refresh token** para obter um novo
     * access token e atualizar a data de expiração no banco de dados. O refresh token também é
     * atualizado se a API retornar um novo.
     *
     * Todos os erros de comunicação com a API da Conta Azul são registrados no log do sistema.
     *
     * @return void
     *
     * @example
     * // Atualiza o token automaticamente se estiver expirado
     * $this->saveOrRefreshToken();
     *
     * // Após a execução, a tabela `CA_Tokens` conterá sempre o token mais recente.
     */
    public function saveOrRefreshToken(){
        $ca_tokens = CA_Tokens::all();

        if($ca_tokens->isEmpty()){ //verifica se já há algum token cadastrado, só pode haver uma linha no banco
            $data = $this->getAccessToken();
            CA_Tokens::create([
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->addSeconds($data['expires_in'])
            ]);
        }else{
            $token = $ca_tokens->first(); //all retorna uma collection

            $data = [
                'client_id' => env('CLIENT_ID_CA'),
                'client_secret' => env('SECRET_ID_CA'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token
            ];
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($data['client_id'] . ':' . $data['client_secret']),
                ])
                ->post('https://auth.contaazul.com/oauth2/token', $data);

            if($response->ok()){
                $dataNova = $response->json();
                $token->update([
                    'access_token' => $dataNova['access_token'],
                    'refresh_token' => $dataNova['refresh_token'] ?? $token->refresh_token,
                    'expires_at' => now()->addSeconds($dataNova['expires_in'])
                ]);
            }else {
                \Log::error('Erro ao conectar com o conta azul', ['status' => $response->status(), 'body' => $response->body()]);
            }
        }
    }

    public function lancarVenda(Request $request){
        if(!$request->lancar_venda){
            return redirect()->route('show.CRM');
        }

        $token = CA_Tokens::first();
        $lead = Lead::findOrFail($request->lead_id);

        if(!$token || $token->expires_at < now()){
            $this->saveOrRefreshToken();
            $token->refresh();
        }

        #dd($token);
        $cliente_id = $this->getClienteUUID($lead, $token->access_token); 
        dd($cliente_id);
    }

    public function getClienteUUID($lead, $access_token){ // CPF ou CNPJ + token 
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $access_token
            ])->get('https://api-v2.contaazul.com/v1/pessoas', [
                'documentos' => $lead->cliente->cnpj
            ]);

            if($response->status() == 200){
                $data = $response->json();
                if($data['items'] == null){
                    $cliente = $this->createClienteCA($lead, $access_token);
                    return $cliente['id'];
                }else{
                    return $data['items'][0]['id'];
                }
            } else {
                \Log::error('Erro ao acessar a API para resgatar o cliente', ['status' => $response->status(), 'body' => $response->body()]);
                return $response->status();
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para resgatar o cliente do CA');
            \Log::error('Erro ao acessar a API para resgatar o cliente no Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }
    }

    public function createClienteCa($lead, $access_token){
        try{
            $documentKey = strlen($lead->cliente->cnpj) === 11 ? 'cpf' : 'cnpj';
            $tipoPessoa = strlen($lead->cliente->cnpj) === 11 ? 'Física' : 'Jurídica'; #tem que ter acento, o enum deles é estranho

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $access_token
            ])->post('https://api-v2.contaazul.com/v1/pessoas', [
                'ativo' => true,
                $documentKey => $lead->cliente->cnpj,
                'nome' => $lead->cliente->nome,
                'tipo_pessoa' => $tipoPessoa,
                'perfis' => [
                    [
                        'tipo_perfil' => 'Cliente'
                    ]
                ]            
            ]);

            $cliente = $response->json();

            return $cliente;
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para criar o cliente do CA');
            \Log::error('Erro ao acessar a API para criar o cliente no Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }

    }
}

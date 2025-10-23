<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

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

    /**
     * Lança uma venda no Conta Azul a partir de um lead interno.
     *
     * Este método integra o sistema com a API de Vendas da Conta Azul, criando automaticamente
     * uma venda aprovada com base nos dados do cliente, serviço e condições de pagamento.
     * 
     * O método verifica se o token de acesso ainda é válido, renova se necessário,
     * obtém os UUIDs de cliente, categoria, centro de custo e serviço, gera as parcelas,
     * e então envia uma requisição POST à API.
     *
     * @param \Illuminate\Http\Request $request
     *        Contém os dados do lead, número de parcelas, valor e data da primeira cobrança.
     *
     * @return \Illuminate\Http\RedirectResponse
     *         Redireciona para 'show.CRM' com mensagens de sucesso ou erro.
     */
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

        try{
            $num_venda = $this->getNumVendaByVenda($token->access_token);
            $cliente_id = $this->getClienteUUID($lead, $token->access_token); 
            $categoria_id = $this->getCategoriaFinanceiraUUID($token->access_token);
            $centroCusto_id = $this->getCentroCustoFinanceiroUUID($token->access_token);
            $servico_id = $this->getServicoUUID($token->access_token);
            $vendedor_id = $this->getVendedorUUID($lead->vendedor->usuario, $token->access_token);
            $parcelas = $this->gerarParcelas($request->data_primeira_cobranca, $lead->valor_definido, $lead->num_parcelas);

            \Log::info('Preparando lançamento de venda no Conta Azul', [
                'lead_id' => $lead->id,
                'cliente_nome' => $lead->cliente->nome ?? null,
                'cliente_cnpj' => $lead->cliente->cnpj ?? null,
                'cliente_id' => $cliente_id,
                'categoria_id' => $categoria_id,
                'centro_custo_id' => $centroCusto_id,
                'servico_id' => $servico_id,
                'numero_venda' => $num_venda,
                'vendedor_responsável' => $vendedor_id,
                'parcelas' => $parcelas,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $token->access_token,
                'Accept' => 'application/json',
            ])->post('https://api-v2.contaazul.com/v1/venda', [
                    'id_cliente' => $cliente_id,
                    'numero' => $num_venda,
                    'situacao' => 'APROVADO',
                    'data_venda' => Carbon::now()->format('Y-m-d'),
                    'id_categoria' => $categoria_id,
                    'id_centro_custo' => $centroCusto_id,
                    'id_vendedor' => $vendedor_id,
                    'observacoes' => "Venda do cliente {$lead->cliente->nome}, inscrito no CNPJ {$lead->cliente->cnpj}, cadastro feito através de integração",
                    'itens' => [
                        [
                            'descricao' => "Venda do cliente {$lead->cliente->nome}, inscrito no CNPJ {$lead->cliente->cnpj}, no valor de R$ {$lead->valor_definido} em {$lead->num_parcelas}X",
                            'quantidade' => 1,
                            'valor' => (float)$lead->valor_definido,
                            'id' => $servico_id
                        ]
                    ],
                    'condicao_pagamento' => [
                        'tipo_pagamento' => "BOLETO_BANCARIO",
                        'opcao_condicao_pagamento' => $lead->num_parcelas.'x',
                        'parcelas' => $parcelas
                    ]
                ]);

            if($response->ok()){
                \Log::info('LEAD LANÇADO COM SUCESSO', [
                    'lead_id' => $lead->id,
                    'cliente_nome' => $lead->cliente->nome ?? null,
                    'cliente_cnpj' => $lead->cliente->cnpj ?? null,
                    'cliente_id' => $cliente_id,
                    'categoria_id' => $categoria_id,
                    'centro_custo_id' => $centroCusto_id,
                    'servico_id' => $servico_id,
                    'numero_venda' => $num_venda,
                    'vendedor_responsável' => $vendedor_id,
                    'parcelas' => $parcelas,
                ]);
                session()->flash('mensagem', 'Venda lançada no Conta Azul com sucesso!!');
                return redirect()->route('show.CRM');
            }else{
                session()->flash('error', 'Erro ao lançar a venda no CA, favor comunicar o desenvolvedor do sistema');
                \Log::error('Erro ao acessar a API para resgatar o cliente no Conta Azul:', ['status' => $response->status(), 'body' => $response->body()]);
                return redirect()->route('show.CRM');
            }

        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para lança a venda do cliente do CA');
            \Log::error('Erro ao acessar a API para lançar a venda do cliente no Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }
    }

    /**
     * Obtém o UUID do cliente na Conta Azul usando CPF ou CNPJ.
     *
     * Esta função consulta a API da Conta Azul para buscar um cliente pelo documento (CPF ou CNPJ).
     * Se o cliente não existir, chama {@see createClienteCA()} para criar um novo registro.
     *
     * @param object $lead         Objeto lead que deve conter a relação `$lead->cliente->cnpj`.
     * @param string $access_token Token de acesso OAuth2 da Conta Azul.
     *
     * @return string|int|\Illuminate\Http\RedirectResponse
     *         - string: UUID do cliente na Conta Azul, caso exista ou seja criado com sucesso.
     *         - int: Código de status HTTP se a API retornar erro.
     *         - \Illuminate\Http\RedirectResponse: Redireciona para a rota 'show.CRM' em caso de exceção.
     */
    private function getClienteUUID($lead, $access_token){ // CPF ou CNPJ + token 
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

    /**
     * Cria um novo cliente na Conta Azul via API.
     *
     * Esta função envia uma requisição POST para o endpoint de clientes da Conta Azul,
     * criando o cliente com base nos dados do lead fornecido.
     * Determina automaticamente se é Pessoa Física ou Jurídica a partir do comprimento do documento.
     *
     * @param object $lead         Objeto lead que deve conter a relação `$lead->cliente` com `nome` e `cnpj`.
     * @param string $access_token Token de acesso OAuth2 da Conta Azul.
     *
     * @return array|\Illuminate\Http\RedirectResponse
     *         - array: Resposta JSON da API da Conta Azul contendo os dados do cliente criado.
     *         - \Illuminate\Http\RedirectResponse: Redireciona para a rota 'show.CRM' em caso de exceção.
     */
    private function createClienteCa($lead, $access_token){
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

    /**
     * Obtém o UUID da categoria financeira "laudos" na Conta Azul.
     *
     * Esta função consulta a API de categorias da Conta Azul filtrando pelo nome "laudos".
     * Caso a categoria não seja encontrada, exibe mensagem de erro e redireciona o usuário.
     *
     * @param string $access_token Token de acesso OAuth2 da Conta Azul.
     *
     * @return string|int|\Illuminate\Http\RedirectResponse
     *         - string: UUID da categoria "laudos" se encontrada.
     *         - int: Código de status HTTP em caso de falha na resposta da API.
     *         - \Illuminate\Http\RedirectResponse: Redireciona para a rota 'show.CRM' em caso de exceção ou categoria não encontrada.
     *
     * @example
     * $categoriaId = $this->getCategoriaFinanceiraUUID($access_token);
     * // Retorna o UUID da categoria "laudos" para vinculação em lançamentos financeiros.
     */
    private function getCategoriaFinanceiraUUID($access_token){ # de momento sempre será laudos
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $access_token
            ])->get('https://api-v2.contaazul.com/v1/categorias', [
                'campo_ordenado_descendente' => 'NOME',
                'nome'=> env('CA_CATEGORIA_LAUDOS')
            ]);

            if($response->status() == 200){
                $data = $response->json();
                if($data['itens'] == null){
                    session()->flash('error', 'Categoria LAUDOS não encontrada, favor comunicar o desenvolvedor do sistema');
                    \Log::error('Categoria Laudos não encontrada');
                    return null;
                }else{
                    return $data['itens'][0]['id'];
                }
            } else {
                \Log::error('Erro ao acessar a API para resgatar a categoria financeira', ['status' => $response->status(), 'body' => $response->body()]);
                return $response->status();
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para resgatar a Categoria Financeira do CA');
            \Log::error('Erro ao acessar a API para resgatar a Categoria Financeira no Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }
    }

    /**
     * Obtém o UUID do Centro de Custo na Conta Azul.
     *
     * Esta função consulta a API de centro de custo da Conta Azul utilizando a variável de ambiente
     * `CA_CENTRO_CUSTO` como filtro de busca.  
     * Caso não seja encontrado, exibe uma mensagem de erro e registra no log.
     *
     * @param string $access_token Token de acesso OAuth2 da Conta Azul.
     *
     * @return string|int|\Illuminate\Http\RedirectResponse|null
     *         - string: UUID do centro de custo encontrado.
     *         - int: Código de status HTTP em caso de falha na resposta da API.
     *         - null: Se nenhum centro de custo for encontrado.
     *         - \Illuminate\Http\RedirectResponse: Redireciona para 'show.CRM' em caso de exceção.
     */
    private function getCentroCustoFinanceiroUUID($access_token){
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $access_token
            ])->get('https://api-v2.contaazul.com/v1/centro-de-custo', [
                'busca' => env('CA_CENTRO_CUSTO'),
            ]);

            if($response->status() == 200){
                $data = $response->json();
                if($data['itens'] == null){
                    session()->flash('error', 'Centro de Custo SETOR COMERCIAL não encontrado, favor comunicar o desenvolvedor do sistema');
                    \Log::error('Centro de Custo Setor COMERIAL não encontrado');
                    return null;
                }else{
                    return $data['itens'][0]['id'];
                }
            } else {
                \Log::error('Erro ao acessar a API para resgatar o Centro de Custo', ['status' => $response->status(), 'body' => $response->body()]);
                return $response->status();
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para resgatar o Centro de Custo do CA');
            \Log::error('Erro ao acessar a API para resgatar o Centro de Custo no Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }
    }

    /**
     * Obtém o UUID de um Serviço na Conta Azul.
     *
     * Esta função consulta a API de serviços da Conta Azul utilizando a variável de ambiente
     * `CA_ITEM_SERVICO` como filtro de busca textual.  
     * Caso não seja encontrado, exibe uma mensagem de erro e registra no log.
     *
     * @param string $access_token Token de acesso OAuth2 da Conta Azul.
     *
     * @return string|int|\Illuminate\Http\RedirectResponse|null
     *         - string: UUID do serviço encontrado.
     *         - int: Código de status HTTP em caso de falha na resposta da API.
     *         - null: Se nenhum serviço for encontrado.
     *         - \Illuminate\Http\RedirectResponse: Redireciona para 'show.CRM' em caso de exceção.
     */
    private function getServicoUUID($access_token){
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $access_token
            ])->get('https://api-v2.contaazul.com/v1/servicos', [
                'busca_textual' => env('CA_ITEM_SERVICO'),
            ]);

            if($response->status() == 200){
                $data = $response->json();
                if($data['itens'] == null){
                    session()->flash('error', 'Serviço '.env('CA_ITEM_SERVICO').' não encontrado, favor comunicar o desenvolvedor do sistema');
                    \Log::error('Serviço da ENV não encontrado');
                    return null;
                }else{
                    return $data['itens'][0]['id'];
                }
            } else {
                \Log::error('Erro ao acessar a API para resgatar o Serviço', ['status' => $response->status(), 'body' => $response->body()]);
                return $response->status();
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para resgatar o Serviço do CA');
            \Log::error('Erro ao acessar a API para resgatar o Serviço no Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }
    }

    /**
     * Obtém o próximo número de venda a ser utilizado no Conta Azul.
     *
     * Esta função consulta o endpoint `/v1/venda/proximo-numero` da API Conta Azul,
     * utilizando o access_token OAuth2 informado. 
     * Caso o endpoint esteja indisponível ou retorne erro, a função registra logs detalhados
     * e exibe uma mensagem genérica ao usuário.
     *
     * @param string $access_token Token de acesso OAuth2 válido.
     * @return mixed Retorna o número da próxima venda em caso de sucesso, 
     *               ou o código HTTP de erro em caso de falha.
     */
    private function getNumVenda($access_token){
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $access_token
            ])->get('https://api-v2.contaazul.com/api/v1/venda/proximo-numero');

            if($response->status() == 200){
                $data = $response->json();
                return $data;
            } else {
                \Log::error('Erro ao acessar a API para resgatar o próximo numero de venda', ['status' => $response->status(), 'body' => $response->body()]);
                return $response->status();
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para resgatar o próximo numero de venda');
            \Log::error('Erro ao acessar a API para resgatar o próximo numero de venda:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }
    }

    /**
     * Recupera o número da última venda registrada e calcula o próximo número de venda.
     *
     * Esta função é utilizada como alternativa quando o endpoint `/v1/venda/proximo-numero`
     * apresenta inconsistências. Ela busca a última venda cadastrada (ordenada pelo número)
     * e retorna o próximo número incremental.
     *
     * @param string $access_token Token de acesso OAuth2 válido.
     * @return mixed Retorna o número da próxima venda em caso de sucesso, 
     *               null se não houver registros, 
     *               ou o código HTTP de erro em caso de falha.
     */
    private function getNumVendaByVenda($access_token){ # usa o endpoint venda para capturar o num venda até o endpoint proximo-numero ser arrumada
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $access_token
            ])->get('https://api-v2.contaazul.com/v1/venda/busca',[
                'pagina' => 1,
                'campo_ordenado_descendente' => 'numero',
                'tamanho_pagina' => 1,
                'data_criacao_de' => '2025-10-22'
            ]);

            if($response->status() == 200){
                $data = $response->json();
                if($data['itens'] == null){
                    session()->flash('error', 'Erro ao localizar o número da venda, favor comunicar o desenvolvedor do sistema');
                    \Log::error('Erro ao localizar o número da venda pela função getNumVendaByVenda');
                    return null;
                }else{
                    return $data['itens'][0]['numero'] + 1;
                }
            } else {
                \Log::error('Erro ao acessar a API de venda para conseguir o próximo numero de venda pela função getNumVendaByVenda', ['status' => $response->status(), 'body' => $response->body()]);
                return $response->status();
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para resgatar o próximo numero de venda pela função getNumVendaByVenda');
            \Log::error('Erro ao acessar a API para resgatar o próximo numero de vendapela função getNumVendaByVenda:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }
    }

    private function getVendedorUUID($nome, $access_token){
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $access_token
            ])->get('https://api-v2.contaazul.com/v1/venda/vendedores',[
                'nome' => $nome,
            ]);

            if($response->status() == 200){
                $data = $response->json();
                return $data[0]['id'];
            } else {
                \Log::error('Erro ao acessar a API para resgatar o vendedor responsável', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao acessar a API para resgatar o vendedor responsável');
            \Log::error('Erro ao acessar a API para resgatar o vendedor responsável:', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }

    }

    private function gerarParcelas($dataInicial, $valorTotal, $numParcelas){
        $parcelas = [];
        $valorParcela = round($valorTotal / $numParcelas, 2);
        $soma = $valorParcela * $numParcelas;
        $diferenca = round($valorTotal - $soma, 2); // pode ser positivo ou negativo

        for ($i = 0; $i < $numParcelas; $i++) {
            $valor = $valorParcela;
            if ($i === $numParcelas - 1) {
                $valor += $diferenca; // corrige a última parcela
            }

            $parcelas[] = [
                'data_vencimento' => Carbon::parse($dataInicial)->addMonths($i)->format('Y-m-d'),
                'valor' => $valor,
                'descricao' => 'Parcela ' . ($i + 1)
            ];
        }

        return $parcelas;
    }
}

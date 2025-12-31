<?php
namespace App\Services;

use App\Models\Empresas_Soc;
use App\Models\Cliente;
use App\Models\Integracao;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DownloadSocGedService
{
    public $username, $password, $codigoGed;
    public Cliente $cliente;

    public function __construct($username, $password, Cliente $cliente, $codigoGed){
        $this->username = $username;
        $this->password = $password;
        $this->cliente = $cliente;
        $this->codigoGed = $codigoGed;
    }

    public function requestDownload(){
        $endPoint = Integracao::where('slug', 'ws_soc_download_ged')->first()->endpoint;
        try{
            $envelope = $this->buildEnvelope();

            \Log::info('Preparando requisição para realizar o download solicitado', ['xml' => $envelope]);

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8', // CORRETO
                'SOAPAction'   => '', // CORRETO (Geralmente vazio funciona no SOC)
            ])->send('POST', $endPoint, [
                'body' => $envelope, // CORRETO (Isso passa o raw body para o Guzzle por baixo dos panos)
            ]);

            if($response->ok()){
                \Log::info('Requisição bem sucedida');
                dd($response->body());
            }else{
                dd($response->body());
            }
        }catch(\Exception $e){

        }
    }

    private function buildEnvelope(): string{
        $xml =<<<XML
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                    <soapenv:Header>
                        {$this->buildHeaderWSS()}
                    </soapenv:Header>
                    <soapenv:Body>
                        {$this->buildBody()}
                    </soapenv:Body>
                </soapenv:Envelope>
                XML;
        return $xml;
    }

    private function buildBody(): string{
        $codigoEmpresa = Empresas_Soc::where('cnpj', $this->cliente->cnpj)
                                    ->orWhere('nome', 'like', '%' . $this->cliente->nome . '%')
                                    ->first()
                                    ->codigo_soc ?? null;
        $empresaPrincipal = ENV('COD_EMPRESA_SOC');
        $codigoResponsavel = ENV('COD_RESPONSAVEL_SOC');
        $codigoUsuario = ENV('COD_USUARIO_INTEGRA_SOC');
        $codigoGed = $this->codigoGed;
        $body = <<<XML
                    <ser:downloadArquivosPorGed xmlns:ser="http://services.soc.age.com/">
                        <downloadPorGed>
                            <identificacaoWsVo>
                                <codigoEmpresaPrincipal>{$empresaPrincipal}</codigoEmpresaPrincipal>
                                <codigoResponsavel>{$codigoResponsavel}</codigoResponsavel>
                                <codigoUsuario>{$codigoUsuario}</codigoUsuario>
                            </identificacaoWsVo>
                            <codigoEmpresa>{$codigoEmpresa}</codigoEmpresa>
                            <codigoGed>{$codigoGed}</codigoGed>
                        </downloadPorGed>
                    </ser:downloadArquivosPorGed>
                    XML;
        return $body;
    }

    private function buildHeaderWSS(): string{
        $wss = $this->buildWSS();

        return <<<XML
        <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
            <wsu:Timestamp wsu:Id="Timestamp-1">
                <wsu:Created>{$wss['created']}</wsu:Created>
                <wsu:Expires>{$wss['expires']}</wsu:Expires>
            </wsu:Timestamp>
            <wsse:UsernameToken wsu:Id="UsernameToken-1">
                <wsse:Username>{$wss['username']}</wsse:Username>
                <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">{$wss['digest']}</wsse:Password>
                <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">{$wss['nonce']}</wsse:Nonce>
                <wsu:Created>{$wss['created']}</wsu:Created>
            </wsse:UsernameToken>
        </wsse:Security>
        XML;
    }

    private function buildWSS(int $ttlSeconds = 60): array
    {
        // 1. NONCE
        // O padrão exige que geremos bytes aleatórios.
        // Para o XML, enviamos em Base64. Para o Hash, usamos os bytes brutos.
        $nonceBytes = random_bytes(16);
        $nonceBase64 = base64_encode($nonceBytes);

        // 2. TIMESTAMP (O Pulo do Gato)
        // Precisamos de milissegundos exatos (3 casas), como na página 9 da doc.
        // O microtime(true) retorna o timestamp com decimais.
        $t = microtime(true);
        $micro = sprintf("%03d", ($t - floor($t)) * 1000);
        
        // Data de Criação (Created)
        $created = gmdate('Y-m-d\TH:i:s', (int)$t) . '.' . $micro . 'Z';
        
        // Data de Expiração (Expires) - Recomendado 1 minuto na página 9 [cite: 187]
        $expires = gmdate('Y-m-d\TH:i:s', (int)$t + $ttlSeconds) . '.' . $micro . 'Z';

        // 3. PASSWORD DIGEST
        // A fórmula é: SHA1( NonceBytes + CreatedString + Senha )
        // A senha é a chave '6f16...' que você recebeu, usada LIMPA aqui.
        $passwordClean = trim($this->password);
        
        $digest = base64_encode(
            sha1($nonceBytes . $created . $passwordClean, true)
        );

        return [
            'username' => $this->username, // Ex: U3338099
            'digest'   => $digest,
            'nonce'    => $nonceBase64,
            'created'  => $created,
            'expires'  => $expires,
        ];
    }
}
<?php
namespace App\Services;

use App\Models\Empresas_Soc;
use App\Models\Integracao;
use App\Models\Cliente;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DownloadSocGedService
{
    public $username, $password;
    public Cliente $cliente;

    public function __construct($username, $password, Cliente $cliente, $codigoGed = null, $tipoGed = null){
        $this->username = $username;
        $this->password = $password;
        $this->cliente = $cliente;
        $this->codigoGed = $codigoGed;
        $this->tipoGed = $tipoGed;
    }

    private function buildBody(): string{
        $codigoEmpresa = Empresas_Soc::where('cnpj', $this->cliente->cnpj)
                                    ->orWhere('nome', 'like', '%' . $this->cliente->nome . '%')
                                    ->first()
                                    ->codigo_soc ?? null;
        $codigoGed = $this->codigoGed ? $this->codigoGed : $this->getCodigoGed($tipoGed, $codigoEmpresa);
    }

    private function buildHeaderWSS(): string{
        $wss = $this->buildWSS();
        return <<<XML
                <wsse:Security 
                    xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
                    xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">

                    <wsse:UsernameToken>
                        <wsse:Username>{$wss['username']}</wsse:Username>
                        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">
                            {$wss['digest']}
                        </wsse:Password>
                        <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">
                            {$wss['nonce']}
                        </wsse:Nonce>
                        <wsu:Created>{$wss['created']}</wsu:Created>
                    </wsse:UsernameToken>

                    <wsu:Timestamp>
                        <wsu:Created>{$wss['created']}</wsu:Created>
                        <wsu:Expires>{$wss['expires']}</wsu:Expires>
                    </wsu:Timestamp>

                </wsse:Security>
                XML;
    }

    private function buildWSS(int $ttlSeconds = 60): array{
        $nonceBytes = random_bytes(16);
        $nonce = base64_encode($nonceBytes);

        $now = Carbon::now('UTC');

        $created = $now->format('Y-m-d\TH:i:s.v\Z');
        $expires = $now->copy()->addSeconds($ttlSeconds)->format('Y-m-d\TH:i:s.v\Z');

        $digest = base64_encode(
            sha1($nonceBytes . $created . $this->password, true)
        );

        return [
            'username' => $this->username,
            'digest'   => $digest,
            'nonce'    => $nonce,
            'created'  => $created,
            'expires'  => $expires,
        ];
    }
}
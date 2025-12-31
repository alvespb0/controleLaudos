<?php
namespace App\Services;

use App\Models\Empresas_Soc;
use App\Models\Cliente;
use App\Models\Integracao;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DownloadSocGedService
{
    public $username, $password, $codigoGed;
    public Cliente $cliente;

    /**
     * Construtor da classe DownloadSocGedService
     * 
     * Inicializa o service com as credenciais de autenticação WSS e os dados
     * necessários para realizar o download do GED do sistema SOC.
     * 
     * @param string $username Nome de usuário para autenticação WSS (ex: U3338099)
     * @param string $password Senha/chave para autenticação WSS
     * @param Cliente $cliente Instância do modelo Cliente relacionado ao download
     * @param string|int $codigoGed Código do GED a ser baixado
     */
    public function __construct($username, $password, Cliente $cliente, $codigoGed){
        $this->username = $username;
        $this->password = $password;
        $this->cliente = $cliente;
        $this->codigoGed = $codigoGed;
    }

    /**
     * Realiza o download do arquivo GED do sistema SOC
     * 
     * Executa uma requisição SOAP com autenticação WSS para baixar o documento
     * GED especificado. A resposta vem em formato MTOM (multipart/related) e pode
     * conter o PDF diretamente ou compactado em ZIP. O arquivo é extraído,
     * validado e salvo no storage local.
     * 
     * @return array Retorna um array com:
     *               - 'success' => bool: Indica se o download foi bem-sucedido
     *               - 'file' => string: Caminho do arquivo salvo (ex: "storage/app/soc/download_ged_xxx.pdf")
     * @throws \RuntimeException Se houver erro na comunicação com o SOC, na extração do PDF ou na validação
     */
    public function requestDownload(){
        $endPoint = Integracao::where('slug', 'ws_soc_download_ged')->first()->endpoint;
        try{
            $envelope = $this->buildEnvelope();

            \Log::info('Preparando requisição para realizar o download solicitado', ['xml' => $envelope]);

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8', 
                'SOAPAction'   => '',
                'Accept'       => 'application/xop+xml, text/xml, multipart/related, */*',
            ])->send('POST', $endPoint, [
                'body' => $envelope,
            ]);

            if (! $response->successful()) {
                \Log::error('Erro ao requisitar download GED SOC', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                throw new \RuntimeException('Erro ao comunicar com o SOC');
            }

            $rawBody = $response->body();
            
            // Log para debug
            \Log::info('Resposta MTOM recebida', [
                'content_type' => $response->header('Content-Type'),
                'body_length' => strlen($rawBody),
                'has_pdf_marker' => strpos($rawBody, '%PDF') !== false,
                'has_zip_marker' => strpos($rawBody, 'PK') !== false, // ZIP começa com PK
                'pdf_position' => strpos($rawBody, '%PDF'),
                'zip_position' => strpos($rawBody, 'PK'),
            ]);
            
            // Extrai o binário do MTOM
            $binary = $this->extractBinaryFromMtom($rawBody);
            
            \Log::info('Binário extraído', [
                'binary_length' => strlen($binary),
                'starts_with_pdf' => strpos($binary, '%PDF') === 0,
                'starts_with_zip' => strpos($binary, 'PK') === 0,
                'first_bytes_hex' => bin2hex(substr($binary, 0, 10)),
            ]);
            
            if (strpos($binary, 'PK') === 0) {
                \Log::info('Arquivo detectado como ZIP, descompactando...');
                try {
                    $binary = $this->extractFromZip($binary);
                    \Log::info('PDF extraído do ZIP com sucesso', [
                        'pdf_length' => strlen($binary),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao descompactar ZIP', [
                        'error' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }
            
            // Valida se é um PDF válido
            if (strpos($binary, '%PDF') !== 0) {
                \Log::error('Binário extraído não é um PDF válido', [
                    'first_bytes' => substr($binary, 0, 50),
                    'first_bytes_hex' => bin2hex(substr($binary, 0, 20)),
                ]);
                throw new \RuntimeException('O arquivo recebido não é um PDF válido');
            }

            // Nome do arquivo
            $filename = 'download_ged_' . $this->codigoGed . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            Storage::disk('local')->put("soc/{$filename}", $binary);

            \Log::info('Arquivo GED salvo com sucesso', [
                'path' => "storage/app/soc/{$filename}",
            ]);

            return [
                'success' => true,
                'file'    => "storage/app/soc/{$filename}",
            ];
        }catch(\Exception $e){
            \Log::error('Falha no download GED SOC', [
                'exception' => $e,
            ]);

            abort(500, 'Falha ao realizar download do documento');
        }
    }

    /**
     * Constrói o envelope SOAP completo para a requisição
     * 
     * Monta o envelope SOAP com header (autenticação WSS) e body
     * (dados da requisição de download) conforme o padrão SOAP 1.1.
     * 
     * @return string XML do envelope SOAP completo
     */
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

    /**
     * Constrói o body da requisição SOAP
     * 
     * Monta o XML do body contendo os parâmetros necessários para o download:
     * código da empresa principal, responsável, usuário, empresa do cliente
     * e código do GED a ser baixado. Busca o código da empresa SOC através
     * do CNPJ ou nome do cliente.
     * 
     * @return string XML do body da requisição SOAP
     */
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

    /**
     * Constrói o header de segurança WSS (WS-Security) para autenticação SOAP
     * 
     * Monta o XML do header de segurança contendo timestamp, username token,
     * password digest, nonce e created conforme o padrão WS-Security 1.0.
     * Este header é necessário para autenticar a requisição SOAP no sistema SOC.
     * 
     * @return string XML do header de segurança WSS
     */
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

    /**
     * Gera os dados de autenticação WSS (WS-Security)
     * 
     * Cria os componentes necessários para autenticação WSS conforme o padrão:
     * - NONCE: Bytes aleatórios (16 bytes) codificados em Base64
     * - TIMESTAMP: Data de criação e expiração com precisão de milissegundos
     * - PASSWORD DIGEST: Hash SHA1(nonce_bytes + created_string + password) em Base64
     * 
     * O timestamp é gerado com precisão de milissegundos (3 casas decimais) conforme
     * especificação do padrão WS-Security.
     * 
     * @param int $ttlSeconds Tempo de vida do token em segundos (padrão: 60 segundos)
     * @return array Array associativo contendo:
     *               - 'username': Nome de usuário
     *               - 'digest': Password digest em Base64
     *               - 'nonce': Nonce em Base64
     *               - 'created': Timestamp de criação (formato ISO 8601 com milissegundos)
     *               - 'expires': Timestamp de expiração (formato ISO 8601 com milissegundos)
     */
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
    
    /**
     * Extrai o conteúdo binário (PDF) da resposta MTOM
     * 
     * MTOM (Message Transmission Optimization Mechanism) é usado em SOAP
     * para transmitir dados binários. A resposta vem em formato multipart/related:
     * - Primeira parte: XML SOAP com a resposta
     * - Segunda parte: Binário do PDF
     * 
     * @param string $raw Resposta bruta da API
     * @return string Conteúdo binário do PDF
     * @throws \RuntimeException Se não conseguir extrair o binário
     */
    private function extractBinaryFromMtom(string $raw): string{
        // Remove leading/trailing whitespace
        $raw = trim($raw);
        
        // Detecta o boundary (formato: --uuid:xxxxx ou --xxxxx)
        if (!preg_match('/^--([^\r\n]+)/', $raw, $boundaryMatch)) {
            // Se não tem boundary, tenta encontrar PDF diretamente
            $pdfStart = strpos($raw, '%PDF');
            if ($pdfStart !== false) {
                return $this->extractPdfFromPosition($raw, $pdfStart);
            }
            throw new \RuntimeException('Formato MTOM inválido: boundary não encontrado');
        }
        
        $boundary = '--' . $boundaryMatch[1];
        $endBoundary = $boundary . '--';
        
        $parts = preg_split('/' . preg_quote($boundary, '/') . '(?:--)?\r?\n/', $raw);
        
        if (!empty($parts) && empty(trim($parts[0]))) {
            array_shift($parts);
        }
        
        foreach ($parts as $part) {
            if (empty(trim($part))) {
                continue;
            }
            
            if (preg_match('/Content-Type:[^\r\n]+\r?\n(?:[^\r\n]+\r?\n)*\r?\n(.+)$/s', $part, $matches)) {
                $content = $matches[1];
            } else {
                $content = $part;
            }
            
            $content = preg_replace('/\r?\n' . preg_quote($endBoundary, '/') . '.*$/s', '', $content);
            $content = rtrim($content, "\r\n");
            
            if (strpos($content, '%PDF') === 0) {
                return $this->extractPdfFromPosition($content, 0);
            }
            
            if (strpos($content, 'PK') === 0) {
                return $content; // Retorna o ZIP para ser descompactado depois
            }
            
            $pdfStart = strpos($content, '%PDF');
            if ($pdfStart !== false) {
                return $this->extractPdfFromPosition($content, $pdfStart);
            }
            
            $zipStart = strpos($content, 'PK');
            if ($zipStart !== false) {
                return substr($content, $zipStart);
            }
        }
        
        // Última tentativa: procura %PDF ou PK em qualquer lugar da resposta
        $pdfStart = strpos($raw, '%PDF');
        if ($pdfStart !== false) {
            return $this->extractPdfFromPosition($raw, $pdfStart);
        }
        
        $zipStart = strpos($raw, 'PK');
        if ($zipStart !== false) {
            return substr($raw, $zipStart);
        }
        
        throw new \RuntimeException('PDF ou ZIP não encontrado na resposta MTOM');
    }
    
    /**
     * Extrai o PDF completo a partir de uma posição conhecida
     * 
     * @param string $content Conteúdo onde procurar
     * @param int $startPos Posição onde começa o PDF (%PDF)
     * @return string Conteúdo binário do PDF completo
     */
    private function extractPdfFromPosition(string $content, int $startPos): string
    {
        // Extrai a partir do início do PDF
        $binary = substr($content, $startPos);
        
        // Remove qualquer boundary ou trailing content após o PDF
        $binary = preg_replace('/\r?\n--[^\r\n]*.*$/s', '', $binary);
        
        // Tenta encontrar o fim válido do PDF (%%EOF)
        $eofPos = strpos($binary, '%%EOF');
        if ($eofPos !== false) {
            // Retorna até o fim do PDF (%%EOF + 5 caracteres)
            return substr($binary, 0, $eofPos + 5);
        }
        
        // Se não encontrou %%EOF, retorna tudo (pode estar truncado, mas é melhor que nada)
        return rtrim($binary, "\r\n");
    }
    
    /**
     * Extrai o PDF de um arquivo ZIP
     * 
     * @param string $zipData Dados binários do arquivo ZIP
     * @return string Conteúdo binário do PDF extraído
     * @throws \RuntimeException Se não conseguir descompactar ou encontrar PDF no ZIP
     */
    private function extractFromZip(string $zipData): string
    {
        // Cria um arquivo temporário para o ZIP
        $tempZip = tempnam(sys_get_temp_dir(), 'soc_ged_');
        file_put_contents($tempZip, $zipData);
        
        try {
            $zip = new \ZipArchive();
            if ($zip->open($tempZip) !== true) {
                throw new \RuntimeException('Não foi possível abrir o arquivo ZIP');
            }
            
            // Procura por um arquivo PDF dentro do ZIP
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                \Log::info('Arquivo encontrado no ZIP', ['filename' => $filename]);
                
                // Se encontrar um PDF, extrai
                if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf' || 
                    strpos(strtolower($filename), '.pdf') !== false) {
                    $pdfContent = $zip->getFromIndex($i);
                    $zip->close();
                    unlink($tempZip);
                    
                    if ($pdfContent !== false && strpos($pdfContent, '%PDF') === 0) {
                        return $pdfContent;
                    }
                }
            }
            
            // Se não encontrou PDF com extensão .pdf, tenta o primeiro arquivo
            if ($zip->numFiles > 0) {
                $firstFile = $zip->getFromIndex(0);
                $zip->close();
                unlink($tempZip);
                
                if ($firstFile !== false && strpos($firstFile, '%PDF') === 0) {
                    return $firstFile;
                }
            }
            
            $zip->close();
            throw new \RuntimeException('Nenhum PDF encontrado no arquivo ZIP');
            
        } catch (\Exception $e) {
            if (isset($zip)) {
                $zip->close();
            }
            if (file_exists($tempZip)) {
                unlink($tempZip);
            }
            throw new \RuntimeException('Erro ao descompactar ZIP: ' . $e->getMessage());
        }
    }


}
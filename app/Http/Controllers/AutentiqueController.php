<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use vinicinbgs\Autentique\Utils\Api;
use vinicinbgs\Autentique\Documents;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AutentiqueRequest;

class AutentiqueController extends Controller
{
    /**
     * Cria e envia um documento PDF para assinatura através da API Autentique.
     *
     * Esta função realiza as seguintes etapas:
     * - Valida se o arquivo enviado é um PDF.
     * - Gera um nome de arquivo seguro e armazena temporariamente o documento.
     * - Prepara os atributos exigidos pela API Autentique (nome, signatários, arquivo).
     * - Envia o documento para assinatura.
     * - Remove o arquivo temporário após o envio.
     * - Gera mensagens de feedback para o usuário via sessão.
     *
     * @param  \Illuminate\Http\Request  $request
     *         Requisição HTTP contendo os seguintes campos:
     *         - 'documento' => arquivo PDF enviado (obrigatório)
     *         - 'nome_documento' => nome base do documento (sem extensão)
     *         - 'emails' => lista de e-mails dos signatários
     *
     * @return \Illuminate\Http\RedirectResponse
     *         Redireciona para a rota 'show.CRM' com mensagem de sucesso ou erro.
     *
     * @throws \Exception
     *         Em caso de erro na comunicação com a API ou falha inesperada no processo.
     */
    public function createDocument(AutentiqueRequest $request){
        $request->validated();
        $documento = $request->file('documento');
        $mimeType = $documento->getMimeType();

        if($mimeType != 'application/pdf'){
            session()->flash('error', 'Erro ao enviar para assinatura, documento não está em PDF');
            return redirect()->route('show.CRM');
        }

        try{
            $documentsAutentique = new Documents();

            $fileName = $this->escapeForXml($request->nome_documento.'.pdf');
            
            $fileName = preg_replace('/[\/:*?"<>|\\\\]/', '-', $fileName); # remover caracteres não aceitos em repo
            $directory = 'temp';

            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            $filePath = $documento->storeAs($directory, $fileName);

            $fullPath = storage_path('app/' . $filePath);

            /* QUERY DO GRAPH QL PARA CRIAÇÃO DE DOCUMENTOS AUTENTIQUE */
            $attributes = [
                "document" => [
                    "name" => $request->nome_documento,
                ],
                "signers" => $this->returnSigners($request->emails),
                "file" => $fullPath,
            ];

            $documentsAutentique->create($attributes);

            Storage::delete($filePath);

            session()->flash('mensagem', 'Documento enviado aos signatários com sucesso');

            return redirect()->route('show.CRM');

        }catch (\Exception $e) {
            session()->flash('error', 'Erro ao enviar para assinatura, verifique com o responsável do sistema');
            \Log::error('Erro ao enviar para assinatura:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('show.CRM');
        }
    }

    private function returnSigners($emails){
        $signers = [];
        if (is_array($emails)) {
            foreach ($emails as $email) {
                $signers[] = [
                    "email" => $email,
                    "action" => "SIGN",
                ];
            }
        }
        return $signers;
    }

    private function escapeForXml($value) {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}

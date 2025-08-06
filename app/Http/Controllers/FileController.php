<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\GerarOrcamentoRequest;

use App\Models\User;
use App\Models\Cliente;
use App\Models\File;
use App\Models\Lead;

use Carbon\Carbon;

class FileController extends Controller
{
    /**
     * Exibe a tela inicial para geração de orçamento.
     * O usuário poderá escolher entre:
     *  - Gerar orçamento para cliente já cadastrado (pré-carrega dados do cliente)
     *  - Gerar orçamento avulso (formulário em branco)
     * 
     * Esta função apenas redireciona para a tela de seleção/entrada de dados do orçamento.
     * 
     * @return \Illuminate\View\View
     */
    public function entradaOrcamento(){
        $clientes = Cliente::all();
        return view('Orcamento/Orcamento_new0', ['clientes' => $clientes]);
    }

    /**
     * Recebe os dados da seleção inicial para geração de orçamento.
     * Pode conter dados do cliente selecionado ou estar vazio para orçamento avulso.
     * Encaminha os dados para a view de preenchimento do orçamento, 
     * onde o formulário será exibido com os dados já carregados (se houver).
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function formularioOrcamento(Request $request){
        $request->validate([
            'tipo_orcamento' => 'required|in:1,2', // 1 = avulso, 2 = cliente cadastrado
            'cliente' => 'nullable|required_if:tipo_orcamento,2|exists:cliente,id',
        ], [
            'tipo_orcamento.required' => 'o campo tipo de orçamento é obrigatório',
            'tipo_orcamento.in' => 'o campo tipo de orçamento deve ser ou avulso ou de cliente cadastrado',

            'cliente.required_if' => 'Por favor, selecione um cliente para este tipo de orçamento.',
            'cliente.exists' => 'Cliente selecionado não é válido.',
        ]);

        
        if($request->tipo_orcamento == '1'){ # 1 representa um orçamento avulso
            return view('Orcamento/Orcamento_new', ['cliente' => null]);
        }
        
        $cliente = Cliente::findOrFail($request->input('cliente'));
        
        return view('Orcamento/Orcamento_new', ['cliente' => $cliente]);
    }

    /**
     * Recebe os dados da NEW em JSON contendo os dados para o orçamento
     * Os dados recebidos, podem vir ou de um cliente já cadastrado ou de um lançamento avulso pelo operador
     * Controller processa esses dados e passa à TemplateProcessor para usar no modelo (storage/modelo/orcamento_modelo)
     * Retorna o download do arquivo
     * 
     * @param \Illuminate\Http\GerarOrcamentoRequest $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function gerarOrcamento(GerarOrcamentoRequest $request){
        $request->validated();
        
        $templatePath = storage_path('app/modelos/orcamento_modelo.docx');
        $template = new TemplateProcessor($templatePath);

        $investimento = (float)$request->investimento;
        $parcelas = (int)$request->parcelasTexto;
        $valorParcela = $investimento/$parcelas;
        $textoParcela = '';
        $contador = 1;
        $dataInicial = Carbon::now(); 
        for ($i = 1; $i <= $parcelas; $i++) {
            $dataVencimento = $dataInicial->copy()->addMonthsNoOverflow($i)->day(5); // dia 5 de cada mês
            $dataFormatada = $dataVencimento->format('d/m/Y');

            $textoParcela .= $contador . 'ª parcela: R$' . number_format($valorParcela, 2, ',', '.') . " - Vencimento: {$dataFormatada}\n";
            $contador++;
        }

        $descontoAvista = $investimento * 0.95;

        $cnpjOuCpfFormatado = $this->formatarCpfCnpj($request->cnpjCliente);

        $template->setValue('numProposta', $request->numProposta);
        $template->setValue('razaoSocialCliente',  $this->escapeForXml($request->razaoSocialCliente));
        $template->setValue('nomeUnidade', $this->escapeForXml($request->nomeUnidade));
        $template->setValue('cnpjCliente', $cnpjOuCpfFormatado);
        $template->setValue('telefoneCliente', $request->telefoneCliente);
        $template->setValue('emailCliente', $this->escapeForXml($request->emailCliente));
        $template->setValue('nomeContato', $this->escapeForXml($request->nomeContato));
        $template->setValue('numFuncionarios', $request->numFuncionarios);
        $template->setValue('investimento', number_format($request->investimento, 2, ',', '.'));
        $template->setValue('parcelasTexto', $textoParcela);
        $template->setValue('investimentoDesconto', number_format($descontoAvista, 2, ',', '.'));
        
        $dataHoje = Carbon::now()->translatedFormat('d \d\e F \d\e Y');

        $template->setValue('dataHoje', $dataHoje);

        $fileName = $this->escapeForXml('orcamento_'.$request->razaoSocialCliente.'.docx');
        $fileName = preg_replace('/[\/:*?"<>|\\\\]/', '-', $fileName);
        $tempPath = storage_path('app/temp/' . $fileName);

        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        $template->saveAs($tempPath);

        return view('Orcamento/Orcamento_confirm',[
            'fileName' => $fileName,
            'tempPath' => $tempPath,
            'dados' => $request->all(),
        ]);
    }

    /**
     * Recebe os do orcamento_confirm contendo os dados para o CoONTRATO
     * Os dados recebidos, podem vir ou de um cliente já cadastrado ou de um lançamento avulso pelo operador
     * Controller processa esses dados e passa à TemplateProcessor para usar no modelo (storage/modelo/contrato_modelo)
     * Retorna o download do arquivo
     * 
     * @param \Illuminate\Http\GerarOrcamentoRequest $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function gerarContrato(Request $request){
        $dados = $request->input('dados');
        $cliente = Cliente::findOrFail($dados['cliente_id']);

        $templatePath = storage_path('app/modelos/contrato_modelo.docx');
        $template = new TemplateProcessor($templatePath);

        /* STRING DE TEXTO PARCELA */
        $investimento = (float)$dados['investimento'];
        $parcelas = (int)$dados['parcelasTexto'];
        $valorParcela = $investimento/$parcelas;
        $textoParcela = '';
        $contador = 1;
        $dataInicial = Carbon::now(); 
        for ($i = 1; $i <= $parcelas; $i++) {
            $dataVencimento = $dataInicial->copy()->addMonthsNoOverflow($i)->day(5); // dia 5 de cada mês
            $dataFormatada = $dataVencimento->format('d/m/Y');

            $textoParcela .= $contador . 'ª parcela: R$' . number_format($valorParcela, 2, ',', '.') . " - Vencimento: {$dataFormatada}\n";
            $contador++;
        }
        /* ----------------- */
        $cnpjOuCpfFormatado = $this->formatarCpfCnpj($dados['cnpjCliente']);

        $endereco = $cliente->endereco->rua . ' ' . 
                    $cliente->endereco->numero . ', ' . 
                    $cliente->endereco->bairro . ', ' . 
                    $cliente->endereco->cidade . ' - ' . 
                    $cliente->endereco->uf . ', ' . 
                    $cliente->endereco->cep . ', Brasil';

        $template->setValue('razaoSocialCliente',  $this->escapeForXml($dados['razaoSocialCliente']));
        $template->setValue('cnpjCliente', $cnpjOuCpfFormatado);
        $template->setValue('enderecoCliente', $endereco);
        $template->setValue('telefoneCli', $dados['telefoneCliente']);
        $template->setValue('numFuncionarios', $dados['numFuncionarios']);
        $template->setValue('investimento', number_format($dados['investimento'], 2, ',', '.'));
        $template->setValue('parcelasTexto', $textoParcela);        
        $dataHoje = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
        $template->setValue('dataHoje', $dataHoje);

        $fileName = $this->escapeForXml('contrato_'.$request->dados['razaoSocialCliente'].'.docx');
        $fileName = preg_replace('/[\/:*?"<>|\\\\]/', '-', $fileName);
        $tempPath = storage_path('app/temp/' . $fileName);

        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        return view('Orcamento/Contrato/Contrato_confirm',[
            'fileName' => $fileName,
            'lead_id' => $dados['lead_id'],
            'cliente_id' => $dados['cliente_id'],
            'tempPath' => $tempPath,
            'dados' => $request->all(),
        ]);
    }

    public function gerarContratoByIndex(Request $request){
        $lead = Lead::findOrFail($request->lead_id);

        if(!$lead->valor_definido){
            session()->flash('error','Defina o valor do investimento do lead antes de continuar');
            
            return redirect()->route('show.CRM');
        }
        if(!$lead->num_funcionarios){
            session()->flash('error','Defina o número de funcionários do lead antes de continuar');

            return redirect()->route('show.CRM');
        }
        if(!$lead->cliente || !$lead->cliente->endereco){
            session()->flash('error','Defina o endereço do cliente antes de continuar');

            return redirect()->route('show.CRM');
        }

        /* STRING DE PARCELAS */
        $templatePath = storage_path('app/modelos/contrato_modelo.docx');
        $template = new TemplateProcessor($templatePath);
        
        $investimento = (float)$lead->valor_definido;
        $parcelas = (int)$request->num_parcelas;
        $valorParcela = $investimento/$parcelas;
        $textoParcela = '';
        $contador = 1;
        $dataInicial = Carbon::now(); 
        for ($i = 1; $i <= $parcelas; $i++) {
            $dataVencimento = $dataInicial->copy()->addMonthsNoOverflow($i)->day(5); // dia 5 de cada mês
            $dataFormatada = $dataVencimento->format('d/m/Y');

            $textoParcela .= $contador . 'ª parcela: R$' . number_format($valorParcela, 2, ',', '.') . " - Vencimento: {$dataFormatada}\n";
            $contador++;
        }
        /* FIM DA STRING */
        $cnpjOuCpfFormatado = $this->formatarCpfCnpj($lead->cliente->cnpj);

        $endereco = $lead->cliente->endereco->rua . ' ' . 
                    $lead->cliente->endereco->numero . ', ' . 
                    $lead->cliente->endereco->bairro . ', ' . 
                    $lead->cliente->endereco->cidade . ' - ' . 
                    $lead->cliente->endereco->uf . ', ' . 
                    $lead->cliente->endereco->cep . ', Brasil';
        
        $template->setValue('razaoSocialCliente',  $this->escapeForXml($lead->cliente->nome));
        $template->setValue('cnpjCliente', $cnpjOuCpfFormatado);
        $template->setValue('enderecoCliente', $endereco);
        $template->setValue('telefoneCli', $lead->cliente->telefone->first()->telefone);
        $template->setValue('numFuncionarios', $lead->num_funcionarios);
        $template->setValue('investimento', number_format($lead->valor_definido, 2, ',', '.'));
        $template->setValue('parcelasTexto', $textoParcela);        
        $dataHoje = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
        $template->setValue('dataHoje', $dataHoje);

        $fileName = $this->escapeForXml('contrato_'.$lead->cliente->nome.'.docx');
        $fileName = preg_replace('/[\/:*?"<>|\\\\]/', '-', $fileName);
        $tempPath = storage_path('app/temp/' . $fileName);

        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        $template->saveAs($tempPath);

        return view('Orcamento/Contrato/Contrato_confirm',[
            'fileName' => $fileName,
            'tempPath' => $tempPath,
            'lead_id' => $request->lead_id,
            'cliente_id' => $lead->cliente->id,
            'dados' => $request->all(),
        ]);
    }
    /**
     * Recebe um valor, identifica se é CNPJ ou CPF e formata
     */
    private function formatarCpfCnpj($valor) {
        // Remove tudo que não for número
        $num = preg_replace('/\D/', '', $valor);

        if (strlen($num) === 11) {
            // Formata CPF: 000.000.000-00
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $num);
        } elseif (strlen($num) === 14) {
            // Formata CNPJ: 00.000.000/0000-00
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $num);
        } else {
            // Se não for nem CPF nem CNPJ válido, retorna o valor sem formatação
            return $valor;
        }
    }

    private function escapeForXml($value) {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Salva no banco o registro de geração de um orçamento.
     * O arquivo não é armazenado fisicamente, apenas é registrado seu metadado
     * para fins de relatórios e indicadores.
     * 
     * @param string $nome_arquivo Nome fictício do arquivo gerado
     * @return void
     */
    public function saveOrcamento($nome_arquivo, $lead_id = null){
        $caminho = 'orcamento'.$nome_arquivo;
        $data_referencia = date("Y-m-d");
        $criado_por = Auth::user()->id;

        File::create([
            'nome_arquivo' => $nome_arquivo,
            'tipo' => 'orcamento',
            'caminho' => $caminho,
            'data_referencia' => $data_referencia,
            'cliente_id' => null,
            'laudo_id' => null,
            'criado_por' => $criado_por
        ]);

        session()->flash('mensagem','Orçamento aprovado com sucesso');

        if($lead_id != null){
            $this->validaOrcamentoLead($lead_id);
        }

        return response()->json(['success'=>'atendimento aprovado']);
    }

    /**
     * Salva no banco o registro de geração de um Contrato.
     * O arquivo não é armazenado fisicamente, apenas é registrado seu metadado
     * para fins de relatórios e indicadores.
     * 
     * @param string $nome_arquivo Nome fictício do arquivo gerado
     * @return void
     */
    public function saveContrato($nome_arquivo, $lead_id = null){
        $caminho = 'orcamento'.$nome_arquivo;
        $data_referencia = date("Y-m-d");
        $criado_por = Auth::user()->id;

        File::create([
            'nome_arquivo' => $nome_arquivo,
            'tipo' => 'contrato',
            'caminho' => $caminho,
            'data_referencia' => $data_referencia,
            'cliente_id' => null,
            'laudo_id' => null,
            'criado_por' => $criado_por
        ]);

        session()->flash('mensagem','Contrato aprovado com sucesso');

        if($lead_id != null){
            $this->validaContratoLead($lead_id);
        }

        return response()->json(['success'=>'contrato aprovado']);
    }
    /**
     * Faz o download do orçamento dado o file name, após isso, excluí o mesmo da pasta
     */
    public function downloadOrcamento($fileName){
        $tempPath = storage_path('app/temp/' . $fileName);

        if (!file_exists($tempPath)) {
            abort(404, 'Arquivo não encontrado');
        }

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Recebe uma request com os dados do último form de gerar orçamento
     * além disso, recebe um file name para deletar na pasta temporária já
     * @param \Illuminate\Http\Request $request
     * @return view
     */
    public function retificarOrcamento(Request $request){
        $fileName = $request->fileName;

        if ($fileName) {
            $filePath = storage_path('app/temp/' . $fileName);

            if (file_exists($filePath)) {
                unlink($filePath); 
            }
        }

        $dados = $request->dados;

        return view('Orcamento/Orcamento_retificar', ['dados' => $dados]);
    }

    public function validaOrcamentoLead($lead_id){
        $lead = Lead::findOrFail($lead_id);

        return $lead->update([
            'orcamento_gerado' => 1
        ]);
    }

    public function validaContratoLead($lead_id){
        $lead = Lead::findOrFail($lead_id);

        return $lead->update([
            'contrato_gerado' => 1
        ]);
    }
}

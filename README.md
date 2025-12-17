Controle Laudos - Sistema de Gestão para Clínicas de SST
Sistema ERP e CRM desenvolvido sob medida para Segmetre Ambiental Assessoria LTDA. A aplicação gerencia o ciclo de vida completo do serviço: desde a captação do Lead, precificação inteligente, venda e comissionamento, até a execução técnica e entrega dos laudos (PPRA, PCMSO, LTCAT, etc.).

🚀 Visão Geral e Arquitetura
O sistema é construído sobre o framework Laravel, utilizando uma arquitetura MVC que está em processo de evolução para o Service Pattern e Observer Pattern para isolar regras de negócio complexas.

Stack Tecnológico
Backend: PHP 8.x / Laravel 10.x

Frontend: Blade Templates, Bootstrap 5, jQuery.

Database: MySQL.

Libs Visuais: Chart.js (Dashboards), SortableJS (Kanban CRM).

Infra: Integração via APIs REST e OAuth2.

🛠 Módulos Principais
1. CRM & Comercial (Funil de Vendas)
O coração da entrada de receita. Diferente de CRMs genéricos, este módulo possui regras de negócio específicas para o setor:

Kanban Interativo: Gestão visual de Leads por etapas (Contato, Proposta, Negociação, Ganho/Perdido).

Motor de Precificação: Cálculo automático de sugestão de preço (Mín/Máx) baseado em variáveis parametrizáveis:

Distância do deslocamento (Custo Logístico).

Quantidade de "Vidas" (Funcionários/Porte da empresa).

Auditoria: Rastreabilidade completa de alterações no Lead via Spatie/Activitylog.

2. Gestão Financeira (Comissões)
Automação do fluxo de contas a pagar para equipe comercial e parceiros:

Cálculo de Split: Divisão automática de comissão entre Vendedor Interno e Indicador Externo (Recomendador).

Parcelamento: Geração automática de parcelas de comissão espelhando a negociação feita com o cliente (ex: se o cliente paga em 3x, a comissão é gerada em 3x).

Status de Pagamento: Controle de parcelas pendentes e pagas.

3. Operacional & Técnico (Fábrica de Laudos)
Gestão da produção dos documentos de segurança:

Atribuição Múltipla: Um laudo pode ter múltiplos responsáveis técnicos simultâneos atuando em etapas diferentes (Levantamento de Campo, Engenharia, Digitação).

Prazos e Validade: Controle de data_previsao, data_conclusao e vigência contratual.

4. Dashboards Gerenciais
Visualização de KPIs estratégicos utilizando Chart.js, renderizados a partir de serviços de dados agregados:

Performance por Técnico/Engenheiro.

Projeção de Receita (Lucro Presumido).

Taxa de Conversão de Leads e Status de Laudos.

🔌 Integrações Externas
O sistema atua como um Hub integrando diversas ferramentas:

Conta Azul (ERP):

Autenticação via OAuth2.

Sincronização bidirecional de Clientes.

Lançamento automático de Vendas e geração de parcelas financeiras no ERP.

Google Calendar: Agendamento automático de reuniões e visitas técnicas baseadas na agenda dos consultores.

Autentique: Envio e monitoramento de contratos para assinatura digital.

Zappy (WhatsApp): Disparo de notificações e propostas via WhatsApp.

⚙️ Configuração e Instalação
Clone o repositório:

Bash

git clone https://github.com/seu-usuario/controle-laudos.git
Instale as dependências:

Bash

composer install
npm install
Configuração de Ambiente:

Bash

cp .env.example .env
php artisan key:generate
Configure as credenciais de banco de dados e as chaves de API (Conta Azul, Google, etc) no arquivo .env.

Banco de Dados:

Bash

php artisan migrate --seed
Isso criará a estrutura e populará as tabelas de status_crm, variaveis_precificacao e usuários iniciais.

🚧 Roadmap de Refatoração (Em Andamento)
Estamos movendo a lógica de negócio dos Controllers para Camadas de Serviço dedicadas:

[x] PrecificacaoService: Extração da lógica de cálculo de leads.

[x] ComissaoService: Isolamento das regras de split e geração de parcelas.

[ ] ContaAzulService: Refatoração da integração para remover dependência de strings mágicas.

[ ] Unificação de Autenticação: Migração de tabelas separadas (op_tecnicos, op_comercial) para uma tabela users unificada com Roles/Permissions.
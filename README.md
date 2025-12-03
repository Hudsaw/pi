# Sistema de Gestão para Malharia – Projeto PI 
Cliente: Malharia Holz Birck
Equipe: Hudson Borges e Maria Antonia

# Objetivo Geral
Desenvolver um sistema web para gerenciar RH, produção e contabilidade da malharia Holz Birck.

# Objetivos Específicos
Automatizar o cálculo de pagamentos por serviço realizado
Controlar o fluxo de lotes e serviços por costureira
Permitir comunicação direta via chat interno
Gerar relatórios de produção e lucratividade
Garantir segurança e privacidade dos dados cadastrais e financeiros

# Requisitos do sistema

## Requisitos Funcionais (RF)

Código - Nome - Descrição - Prioridade
RF01 - Cadastro de Usuário - O sistema deve permitir que novos usuários se  cadastrem com: nome, CPF, e-mail, telefone, CEP, endereço, tipo de chave PIX, chave PIX, senha criptografada. - Essencial
RF02 - Login de Usuário - O sistema deve autenticar usuários com e-mail e senha, criando uma sessão. - Essencial
RF03 - Gestão de Perfis - O sistema deve diferenciar entre admin e costureira, com menus e permissões distintas. - Essencial
RF04 - Cadastro de Costureiras - O admin deve cadastrar costureiras vinculando a um usuário, incluindo especialidade, nível (Ouro, Prata, Bronze) e dados bancários. - Essencial
RF05 - Cadastro de Lotes - O admin deve cadastrar lotes com: empresa, coleção, nome, data de entrada, data de entrega, status (Aberto, Entregue, Cancelado). - Essencial
RF06 - Cadastro de Peças - O admin deve cadastrar peças vinculadas a um lote, com tipo, cor, tamanho, operação, quantidade e valor unitário. - Essencial
RF07 - Cadastro de Serviços - O admin deve cadastrar serviços vinculados a um lote e operação, com quantidade de peças, valor da operação, datas de envio e finalização. - Essencial
RF08 - Vincular Costureira a Serviço - O admin deve vincular uma costureira a um tipo de operação por serviço com data de início e entrega. O sistema gera mensagem automática para a costureira. - Essencial
RF09 - Controle de Pagamentos - O sistema deve calcular pagamentos automaticamente com base nos serviços finalizados, permitindo descontos e registro de comprovante. - Essencial
RF10 - Gestão de Empresas - O admin deve cadastrar empresas (CNPJ, endereço, contato) para vincular aos lotes. - Importante
RF11 - Sistema de Mensagens - Admin pode enviar mensagens para costureiras específicas ou todas. Mensagens automáticas são geradas ao vincular costureiras a serviços. - Importante
RF12 - Relatórios de Produção - O admin deve gerar relatórios de peças por lote, por costureira e histórico de produção. - Importante
RF13 - Cálculo de Lucro - O sistema deve calcular lucro com base em lotes entregues, pagamentos e compras de materiais. - Essencial
RF14 - Logs do Sistema - Todas as alterações em cadastros devem ser registradas com usuário, data e dados alterados. - Desejável

# Instalação

## Pré-requisitos
PHP 8.0+
MySQL 5.7+
Composer
Servidor web (Apache/Nginx)

## Clone o repositorio

```
git clone https://github.com/hudsaw/pi.git
cd pi
```

## Crie o banco de dados
```
mysql -u root -p < banco.sql
```

## Instale as dependencias

```
pip install composer
```

## Configure acessos
```
config/constants.php
```

## Se precisar do autoloader do Composer
composer dump-autoload

# Modelagem de dados
usuarios: admin e costureiras (com CPF, PIX, dados bancários)
empresas: clientes que enviam lotes
lotes: agrupamento de peças por empresa/coleção
pecas: tipo, cor, tamanho, operação, quantidade
operacoes: ex: “Costura de manga” (com valor unitário)
servicos: contém costureira_id (1:1) → vincula uma costureira a um serviço
pagamentos + pagamento_itens: histórico financeiro por costureira
mensagens: comunicação interna
logs_sistema: auditoria de alterações

# Casos de Uso

## UC01 – Vincular Costureira a Serviço
Ator: Administrador
Descrição: O administrador atribui uma costureira a um serviço específico, definindo data de início e entrega. O sistema envia mensagem automática.
Pré-condições:
Administrador autenticado
Serviço criado
Costureira cadastrada e ativa
Regras de Negócio:
Máximo de 2 serviços ativos por costureira
Um serviço só pode ter uma costureira (relacionamento 1:1)
Sistema gera notificação automática

## UC02 – Cadastrar e Entregar Lote
Ator: Administrador
Descrição: Cadastra um novo lote com empresa, coleção, peças e operações. Posteriormente, registra a entrega com conferência de quantidade.
Pré-condições:
Empresa e operações já cadastradas
Regras de Negócio:
Lote só pode ser entregue se tiver status Aberto
Peças só podem ser adicionadas enquanto o lote estiver não entregue
Geração automática de número único de lote

## UC03 – Gerenciar Pagamento de Costureira
Ator: Administrador
Descrição: Visualiza pagamentos pendentes, aplica descontos (com motivo), registra pagamento efetuado e emite comprovante.
Pré-condições:
Costureira com serviços finalizados
Regras de Negócio:
Valor do pagamento = Σ (quantidade × valor da operação)
Descontos exigem motivo obrigatório
Pagamentos efetuados não podem ter descontos retroativos

## UC04 – Visualizar Serviços e Pagamentos (Costureira)
Ator: Costureira
Descrição: A costureira visualiza seus serviços em andamento e finalizados, além do valor de pagamento parcial e histórico de pagamentos.
Pré-condições:
Costureira autenticada
Ter pelo menos um serviço vinculado
Regras de Negócio:
Acesso restrito apenas aos próprios dados
Visualização de valores atualizados em tempo real

## UC05 – Cadastrar Operações e Peças
Ator: Administrador
Descrição: Define operações (ex: "Arrematar manga") com valor unitário e vincula peças a lotes com cor, tamanho, quantidade e operação.
Pré-condições:
Lote aberto existente (para cadastro de peças)
Regras de Negócio:
Operações com mesmo nome não são permitidas
Valor da operação não afeta serviços já criados (histórico preservado)

## UC06 – Calcular e Visualizar Lucro
Ator: Administrador
Descrição: Visualiza lucro mensal com base em:
Lucro = (Valor dos lotes entregues) – (Pagamentos + Compras de materiais)
Pré-condições:
Pelo menos um lote entregue, um pagamento registrado e uma compra registrada
Regras de Negócio:
Dados agrupados por mês
Detalhamento de receitas e despesas disponível

## UC07 – Enviar e Receber Mensagens
Atores: Administrador e Costureira
Descrição: Comunicação interna via chat. Mensagens automáticas são geradas ao vincular costureira a serviço ou registrar pagamento.
Pré-condições:
Usuários autenticados
Regras de Negócio:
Mensagens armazenadas por no mínimo 12 meses
Notificações para mensagens não lidas

## UC08 – Gerar Relatórios de Produção
Ator: Administrador
Descrição: Gera relatórios de:
Peças por lote
Peças por costureira
Pagamento por serviço
Histórico de produção (com filtros por período, costureira, lote)
Saída: Tela + exportação em PDF
Regras de Negócio:
Filtros dinâmicos obrigatórios
Dados precisos e auditáveis

# Estrutura de arquivos
pi/
├── App/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── BaseController.php
│   │   ├── CosturaController.php
│   │   └── PageController.php
│   ├── Core/
│   │   ├── Container.php
│   │   ├── Database.php
│   │   └── Router.php
│   ├── Middlewares/
│   │   ├── AuthMiddleware.php
│   │   └── RoleMiddleware.php
│   └── Models/
│       ├── AdminModel.php
│       ├── CosturaModel.php
│       ├── EmpresaModel.php
│       ├── LoteModel.php
│       ├── NotificacaoModel.php
│       ├── OperacaoModel.php
│       ├── PageModel.php
│       ├── PecaModel.php
│       ├── ServicoModel.php
│       └── UserModel.php
├── config/
│   └── constants.php
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.php
│   │   ├── fonts/
│   │   │   └── SchibstedGrotesk.ttf
│   │   ├── icones/
│   │   │   ├── editar.svg
│   │   │   ├── reativar.svg
│   │   │   ├── remover.svg
│   │   │   └── visualizar.svg
│   │   ├── img/
│   │   │   ├── banner.png
│   │   │   ├── icon.png
│   │   │   ├── logo.png
│   │   │   ├── malharia.png
│   │   │   └── maquina.png
│   │   └── js/
│   │       └── utils.js
│   ├── .htaccess
│   └── index.php
├── vendor/
├── views/
│   ├── admin/
│   │   ├── criar-empresa.php
│   │   ├── criar-lote.php
│   │   ├── criar-operacao.php
│   │   ├── criar-peca.php
│   │   ├── criar-servico.php
│   │   ├── criar-usuario.php
│   │   ├── editar-empresa.php
│   │   ├── editar-lote.php
│   │   ├── editar-operacao.php
│   │   ├── editar-servico.php
│   │   ├── editar-usuario.php
│   │   ├── empresas.php
│   │   ├── lotes.php
│   │   ├── operacoes.php
│   │   ├── painel.php
│   │   ├── servicos.php
│   │   ├── usuarios.php
│   │   ├── visualizar-empresa.php
│   │   ├── visualizar-lote.php
│   │   ├── visualizar-servico.php
│   │   └── visualizar-usuario.php
│   ├── auth/
│   │   ├── login.php
│   │   ├── nova-senha.php
│   │   ├── politica.php
│   │   ├── resetar-senha.php
│   │   └── termos.php
│   ├── costura/
│   │   ├── editar-perfil.php
│   │   ├── painel.php
│   │   ├── visualizar-perfil.php
│   │   └── visualizar-servico.php
│   └── shared/
│       ├── 404.php
│       ├── footer.php
│       ├── header.php
│       ├── home.php
│       ├── sidebar.php
│       └── sidebar.php
├── .htaccess
├── banco.sql
├── composer.json
├── composer.lock
├── index.php
└── README.md

# Cronograma 

    ✅ Sprint 1 - Estrutura Base e Autenticação
    Página inicial
    Sistema de login
    Página de cadastro, edição e visualização de usuário
    Sistema de rotas
    Controle de sessão
    Painel do admin e costureira
    Criação do CSS
    Integração com banco de dados
    
    ✅ Sprint 2 - Cadastros Básicos e Usuários
    Refatorar pagecontroller para admincontroller
    Aviso de erro nas credenciais
    Refatorar CSS para arquivo único
    Instalação do composer
    Melhorar listagem de usuários (busca por filtro)
    Botões de ações
    Tratamento de exceção
    Máscaras de formulário
    CRUD completo de usuários

    ✅ Sprint 3 - Operações e Tipos de Peça
    Cadastro de Operações (CRUD)
    Estruturar base de produção
    Módulo de operações: cadastro, listagem, edição, exclusão
    Validação de operações
    CRUD de tipos de peça
    CRUD de cores
    CRUD de tamanhos
    
    ✅ Sprint 4 - Gestão de Lotes e Serviços
    CRUD de Lotes (criar, editar, entregar)
    CRUD de Serviços (operações dentro de lotes)
    Geração automática de número de lote
    Entrega de lote com conferência de peças
    Status: aberto/entregue/cancelado
    Cadastro de lotes (coleção, observação, anexos)
    Adição de peças ao lote
    Entrega de lote com data e ajuste de quantidade
    Listagem por status
    
    ✅ Sprint  5 - Vinculação de Costureiras e Empresas
    Vincular costureiras a serviços (operações específicas)
    Limite de 2 serviços ativos por costureira
    Validação de disponibilidade de costureiras
    CRUD completo de empresas
    Validação de CNPJ
    Busca e filtros de empresas
    Dashboard com serviços por costureira
    
    SPRINT 6 - Módulo Financeiro (Pagamentos)
    Cálculo automático de pagamentos (operação × quantidade)
    Gestão de pagamentos por costureira
    Histórico de pagamentos anteriores
    Cadastro de descontos com motivo
    Registro de pagamento efetuado
    Comprovantes de pagamento
    Status de pagamento (Pendente/Pago/Cancelado)
    Relatório de pagamento por período

    SPRINT 7 - Sistema de Mensagens e Notificações
    Envio de mensagens do admin para costureiras
    Mensagens automáticas ao vincular costureira a serviço
    Notificações de pagamentos realizados
    Alertas de prazos de entrega
    Caixa de entrada para costureiras
    Histórico de mensagens
    Status de mensagens lidas/não lidas

    SPRINT 8 - Cálculo de Lucro e Dashboard Financeiro
    Cálculo simplificado de lucro: Lucro = (Valor dos Lotes Entregues) - (Pagamentos)
    Painel de lucro por mês
    Detalhamento de receitas (lotes entregues)
    Detalhamento de despesas (pagamentos)
    Histórico de lucro
    Gráficos de desempenho financeiro
    Métricas de rentabilidade por lote
    
    SPRINT 9 - Relatórios e Histórico de Produção
    Relatório de peças produzidas por lote
    Relatório de peças produzidas por costureira
    Relatório de pagamento por serviço
    Histórico de produção com filtros (período, costureira, lote)
    Exportação em PDF
    Dashboard administrativo com métricas
    Filtros avançados de relatórios
    
    SPRINT 10 - Logs, Auditoria e Entrega Final
    Sistema de logs de alterações
    Histórico de alterações em cadastros
    Backup automático diário
    Testes de usabilidade
    Correção de bugs e otimizações
    Documentação do sistema
    Treinamento do cliente
    Implantação em ambiente real

# Segurança e Confiabilidade
Senhas criptografadas
Dados bancários e PIX protegidos
Permissões por perfil (admin ≠ costureira)
Logs de todas as alterações
Backup diário automático (configurável)

# Licença
Este projeto foi desenvolvido para fins acadêmicos e entregue à empresa Holz Birck.
Não possui licença aberta — uso restrito ao cliente e instituição educacional.

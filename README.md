## Clone the repository

```
git clone https://github.com/hudsaw/pi.git
```

## Install dependencies

```
pip install composer
```

## Se precisar do autoloader do Composer
composer dump-autoload

## Estrutura de arquivos
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
│       ├── NotificacaoModel.php
│       ├── PageModel.php
│       └── UserModel.php
├── config/
│   └── constants.php
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.php
│   │   ├── fonts/
│   │   │   └── SchibstedGrotesk.ttf
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
│   │   ├── criar-usuario.php
│   │   ├── editar-usuario.php
│   │   ├── painel.php
│   │   ├── usuarios.php
│   │   └── visualizar-usuario.php
│   ├── auth/
│   │   ├── login.php
│   │   ├── nova-senha.php
│   │   ├── politica.php
│   │   ├── resetar-senha.php
│   │   └── termos.php
│   └── shared/
│       ├── 404.php
│       ├── footer.php
│       ├── header.php
│       └── home.php
├── .htaccess
├── banco.sql
├── composer.json
├── composer.lock
├── index.php
└── README.md

## Cronograma 

    ✅ Sprint 1 
Página inicial
Sistema de login
Página de cadastro, edição e visualização de usuário
Sistema de rotas
Controle de sessão 
Painel do admin
Criação do css
Integração com banco de dados

    ✅ Sprint 2
Refatorar pagecontroller para admincontroller ter um proprio.
Aviso de erro nas credenciais
Refatorar css para um arquivo único
Instalação do composer
Melhorar listagem dos usuários (busca por filtro) mudar pra tabela
Botões de ações
Tratamento de exceção
Máscaras

    Sprint 3
Cadastro de Operações e Peças
Estruturar base de produção
Cadastro de peças por lote
Módulo de operações: cadastro, listagem, edição, exclusão
Módulo de peças: cadastro, listagem, alteração
Validação: peças só podem ser alteradas em lotes abertos

    Sprint 4
Gestão de Lotes
Criar, editar e entregar lotes
Geração automática de número de lote
Entrega de lote com conferência de peças
Status: aberto / entregue
Cadastro de lotes (coleção, observação, anexos)
Adição de peças ao lote
Entrega de lote com data e ajuste de quantidade
Listagem por status

    Sprint 5
Gestão de Serviços (Cadastro e Vinculação)
Criar serviços e vincular costureiras
Vincular costureira a serviço
Limite de 2 serviços por costureira
Status: em andamento / finalizado
Cadastro de serviços (data, lote, peça, operação)
Vinculação de costureiras
Validação de limite de serviços
Alteração de status (finalizado com data)
    
    Sprint 6
Visualização de Serviços e Pagamentos (Costureira)
Permitir costureiras acompanharem seu trabalho
Tela de serviço atual e finalizado
Visualização de valores parciais, atuais e anteriores
Histórico de pagamentos (sem edição)

    Sprint 7
Gestão de Pagamentos (Admin)
Automatizar cálculos e registro de pagamentos
Cálculo automático (operação × quantidade)
Descontos com motivo
Registro de pagamento efetuado
Módulo de pagamentos por costureira
Histórico de pagamentos anteriores
Cadastro de descontos
Marcação de pagamento como "efetuado"

    Sprint 8
Compras e Lucro
Fechar módulos financeiros
Cálculo: lucro = (lotes entregues) - (pagamentos + compras)
Módulo de compras (cadastro, listagem, edição)
Painel de lucro por mês
Detalhamento de receitas e despesas
Histórico de lucro

    Sprint 9
Relatórios e Histórico de Produção
Gerar relatórios e histórico filtrável
Filtros por período, costureira, lote
Exportação em PDF
Relatório de pagamento por costureira
Relatório de peças por lote e por costureira
Histórico de produção com filtros
Botões de exportação para PDF

    Sprint 10
Logs, Testes e Entrega Final
Validar, ajustar e entregar o sistema
Testes de usabilidade (Objetivo j)
Documentação e treinamento
Sistema testado e homologado
Backup automático diário
Documentação completa (técnica e do usuário)
Treinamento do cliente
Implantação em ambiente real

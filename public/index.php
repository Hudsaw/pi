<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\PageController;
use App\Core\Database;
use App\Core\Router;
use App\Core\Container;

$container = new Container();
$router = new Router($container);

// Rotas principais
$router->get('/', 'PageController@home');
$router->get('/politica', 'PageController@politica');
$router->get('/termos', 'PageController@termos');

// Rotas de autenticação
$router->get('/login', 'AuthController@mostrarLogin');
$router->post('/logar', 'AuthController@logar');
$router->get('/logout', 'AuthController@logout');
$router->get('/resetar-senha', 'AuthController@showResetPassword');
$router->post('/resetar-senha', 'AuthController@handleResetRequest');
$router->get('/resetar-senha/confirmar', 'AuthController@showResetPasswordForm');
$router->post('/resetar-senha/confirmar', 'AuthController@handlePasswordReset');

//Rotas do Administrador
$router->get('/admin/painel', 'AdminController@painel', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/usuarios', 'AdminController@usuarios', ['AuthMiddleware', 'RoleMiddleware::admin']);

//Rotas de cadastro
$router->get('/admin/visualizar-usuario', 'AdminController@visualizarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/editar-usuario', 'AdminController@editarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/admin/salvar-usuario', 'AdminController@salvarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/remover-usuario', 'AdminController@removerUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/criar-usuario', 'AdminController@mostrarCadastro', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/admin/cadastrar-usuario', 'AdminController@cadastrarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/reativar-usuario', 'AdminController@reativarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);

//Rotas de Lotes
$router->get('/admin/lotes', 'AdminController@lotes', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/criar-lote', 'AdminController@mostrarCriarLote', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/admin/criar-lote', 'AdminController@criarLote', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/visualizar-lote', 'AdminController@visualizarLote', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/adicionar-peca', 'AdminController@adicionarPecaLote', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/admin/criar-peca', 'AdminController@criarPeca', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/remover-lote', 'AdminController@removerLote', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/remover-peca', 'AdminController@removerPeca', ['AuthMiddleware', 'RoleMiddleware::admin']);

// Rotas para operações
$router->get('/admin/operacoes', 'AdminController@operacoes', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/criar-operacao', 'AdminController@mostrarCriarOperacao', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/admin/criar-operacao', 'AdminController@criarOperacao', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/remover-operacao', 'AdminController@removerOperacao', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/admin/reativar-operacao', 'AdminController@reativarOperacao', ['AuthMiddleware', 'RoleMiddleware::admin']);

// Rotas da Costureira
$router->get('/costura/painel', 'CosturaController@painel', ['AuthMiddleware', 'RoleMiddleware::costura']);

$router->dispatch();
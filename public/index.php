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

// Rotas da Costureira
$router->get('/costura/painel', 'CostureiraController@painel', ['AuthMiddleware', 'RoleMiddleware::costura']);

$router->dispatch();
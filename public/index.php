<?php
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\PageController;
use App\Core\Database;
use App\Core\Router;
use App\Core\Container;

$router = new Router(Database::getInstance());

// Rotas principais
$router->get('/', 'PageController@home');

// Rotas de autenticação
$router->get('/login', 'AuthController@mostrarLogin');
$router->post('/logar', 'AuthController@logar');
$router->get('/logout', 'AuthController@logout');

//Rotas do Administrador
$router->get('/painel', 'AdminController@painel', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/usuarios', 'AdminController@usuarios', ['AuthMiddleware', 'RoleMiddleware::admin']);

// Rotas de cadastro
$router->get('/visualizar-usuario', 'AdminController@visualizarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/editar-usuario', 'AdminController@editarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/salvar-usuario', 'AdminController@salvarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/remover-usuario', 'AdminController@removerUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/criar-usuario', 'AdminController@mostrarCadastro', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/cadastrar-usuario', 'AdminController@cadastrarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);

$router->dispatch();
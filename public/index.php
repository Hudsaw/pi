<?php
require_once 'constants.php';
require_once __DIR__.'/app/Database.php';
require_once __DIR__.'/app/UserModel.php';
require_once __DIR__.'/app/PageController.php';
require_once __DIR__.'/app/AuthController.php';
require_once __DIR__.'/app/AuthMiddleware.php';
require_once __DIR__.'/app/RoleMiddleware.php';
require_once __DIR__.'/app/Container.php';
require_once __DIR__.'/app/Router.php';

// Inicializa o container de dependências
$container = new Container();

// Configura o roteador
$router = new Router($container);

// Rotas principais
$router->get('/', 'PageController@home');

// Rotas de autenticação
$router->get('/login', 'AuthController@mostrarLogin');
$router->post('/logar', 'AuthController@logar');
$router->get('/logout', 'AuthController@logout');

//Rotas do Administrador
$router->get('/painel', 'PageController@painel', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/usuarios', 'PageController@usuarios', ['AuthMiddleware', 'RoleMiddleware::admin']);

// Rotas de cadastro
$router->get('/visualizar-usuario', 'PageController@visualizarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/editar-usuario', 'PageController@editarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/salvar-usuario', 'AuthController@salvarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/remover-usuario', 'AuthController@removerUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->get('/criar-usuario', 'PageController@mostrarCadastro', ['AuthMiddleware', 'RoleMiddleware::admin']);
$router->post('/cadastrar-usuario', 'AuthController@cadastrarUsuario', ['AuthMiddleware', 'RoleMiddleware::admin']);

$router->dispatch();
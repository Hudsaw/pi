<?php
require_once 'constants.php';
require_once __DIR__.'/app/Database.php';
require_once __DIR__.'/app/UserModel.php';
require_once __DIR__.'/app/PageController.php';
require_once __DIR__.'/app/AuthController.php';
require_once __DIR__.'/app/AuthMiddleware.php';
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
$router->get('/painel', 'PageController@painel', ['AuthMiddleware']);
$router->get('/usuarios', 'PageController@usuarios', ['AuthMiddleware']);
$router->get('/visualizar-usuario', 'PageController@visualizarUsuario', ['AuthMiddleware']);
$router->get('/editar-usuario', 'PageController@editarUsuario', ['AuthMiddleware']);
$router->post('/salvar-usuario', 'AuthController@salvarUsuario', ['AuthMiddleware']);
$router->get('/remover-usuario', 'AuthController@removerUsuario', ['AuthMiddleware']);
$router->get('/criar-usuario', 'PageController@mostrarCadastro', ['AuthMiddleware']);
$router->post('/cadastrar-usuario', 'AuthController@cadastrarUsuario', ['AuthMiddleware']);
$router->get('/logout', 'AuthController@logout');

$router->dispatch();
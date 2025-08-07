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
$router->get('/cadastro', 'PageController@mostrarCadastro');
$router->get('/editar', 'PageController@mostrarCadastro', ['AuthMiddleware']);
$router->get('/logout', 'AuthController@logout');
$router->post('/cadastro', 'AuthController@cadastrar');
$router->post('/atualizar', 'AuthController@atualizar', ['AuthMiddleware']);
$router->post('/login', 'AuthController@login');


$router->dispatch();
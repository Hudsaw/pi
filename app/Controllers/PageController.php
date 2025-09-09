<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\NotificacaoModel;
use App\Models\UserModel;

class PageController
{
    private $notificacaoModel;
    private $userModel;

    public function __construct()
    {
        $this->notificacaoModel = new NotificacaoModel(Database::getInstance());
        $this->userModel = new UserModel(Database::getInstance());
    }

    public function header()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = [
            'usuarioLogado' => isset($_SESSION['user_id']),
            'nomeUsuario' => $_SESSION['usuario_nome'] ?? 'Visitante',
            'tipoUsuario' => $_SESSION['tipo_usuario'] ?? null,
            'BASE_URL' => BASE_URL,
            'notificacoesNaoLidas' => $this->getUnreadNotificationsCount(),
            'dashboardLink' => $this->getDashboardLink(),
        ];

        return $data;
    }

    public function home()
    {
        error_log("Exibindo pagina inicial");
        $user  = $this->getUsuario();
        $especialidade = $this->userModel->getEspecialidade();

        $this->render('home', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'dados'         => [
                'titulo'    => 'Centralize, organize e produza com precisão',
                'descricao' => 'O PontoCerto é um sistema de gerenciamento desenvolvido
                        especialmente para malharias.',
            ],
        ]);
    }

    public function politica()
    {
        require VIEWS_PATH . 'auth/politica.php';
    }

    public function termos()
    {
        require VIEWS_PATH . 'auth/termos.php';
    }    

    // Métodos auxiliares

    private function getUsuario()
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $user = $this->userModel->getUserPeloId($_SESSION['user_id']);
    
    return $user;
}

private function getDashboardLink(): string
{
    if (!isset($_SESSION['tipo_usuario'])) {
        return BASE_URL;
    }

    return match ($_SESSION['tipo_usuario']) {
        'admin' => BASE_URL . 'admin',
        'costureira' => BASE_URL . 'costureira',
        default => BASE_URL
    };
}
    
}

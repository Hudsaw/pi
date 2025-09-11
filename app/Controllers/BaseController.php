<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\UserModel;

class BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel(Database::getInstance());
    }
    
    protected function render($view, $data = [])
    {
        $data['BASE_URL'] = BASE_URL;
        extract($data);
        
        $headerData = $this->header();
        extract($headerData);
        require VIEWS_PATH . 'shared/header.php';
        require VIEWS_PATH . $view . '.php';
        require VIEWS_PATH . 'shared/footer.php';
    }
    
    protected function header()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return [
            'usuarioLogado' => isset($_SESSION['user_id']),
            'nomeUsuario' => $_SESSION['usuario_nome'] ?? 'Visitante',
            'tipoUsuario' => $_SESSION['tipo_usuario'] ?? null,
            'BASE_URL' => BASE_URL,
            'dashboardLink' => $this->getDashboardLink(),
        ];
    }
    
    protected function getDashboardLink(): string
{
    if (!isset($_SESSION['tipo_usuario'])) {
        return BASE_URL;
    }

    return match ($_SESSION['tipo_usuario']) {
        'admin' => BASE_URL . 'admin/painel', 
        'costureira' => BASE_URL . 'costura/painel', 
        default => BASE_URL
    };
}
    
    protected function estaLogado()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $logado = isset($_SESSION['user_id']);
    error_log("Verificando login - User ID: " . ($_SESSION['user_id'] ?? 'null') . ", Logado: " . ($logado ? 'sim' : 'não'));
    
    return $logado;
    }

    protected function getUsuario()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id'])) {
        return $this->userModel->getUserPeloId($_SESSION['user_id']);
    }
    
    return null;
}
}
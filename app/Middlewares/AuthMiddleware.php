<?php
namespace App\Middlewares;

class AuthMiddleware
{
    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("AuthMiddleware - User ID: " . ($_SESSION['user_id'] ?? 'null'));
    
    if (!isset($_SESSION['user_id'])) {
        error_log("AuthMiddleware: Usuário não autenticado, redirecionando para login");
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . 'login');
        exit();
    }

    error_log("AuthMiddleware: Usuário autenticado, permitindo acesso");
    return true;
}
}
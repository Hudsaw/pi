<?php
class AuthMiddleware
{
    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se está autenticado
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        return true;
    }
}
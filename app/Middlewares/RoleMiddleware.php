<?php
class RoleMiddleware
{
    private $requiredRole;

    public function __construct($role = null)
    {
        $this->requiredRole = $role;
    }

    public static function admin()
    {
        return new self('admin');
    }

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

        // Verifica se tem a role necessária
        if ($this->requiredRole && $_SESSION['user_role'] !== $this->requiredRole) {
            $_SESSION['erro_acesso'] = "Acesso restrito a " . $this->requiredRole . "s";
            header('Location: ' . BASE_URL);
            exit();
        }

        return true;
    }
}
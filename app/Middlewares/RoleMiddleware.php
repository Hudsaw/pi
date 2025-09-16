<?php
namespace App\Middlewares;

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

    public static function costura()
    {
        return new self('costura');
    }

    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("RoleMiddleware - User Role: " . ($_SESSION['user_role'] ?? 'null') . ", Required: " . $this->requiredRole);
    
    if (!isset($_SESSION['user_id'])) {
        error_log("RoleMiddleware: Usuário não autenticado");
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . 'login');
        exit();
    }

    if ($this->requiredRole && $_SESSION['user_role'] !== $this->requiredRole) {
        error_log("RoleMiddleware: Acesso negado - Role necessária: " . $this->requiredRole . ", Role atual: " . $_SESSION['user_role']);
        $_SESSION['erro_acesso'] = "Acesso restrito a " . $this->requiredRole . "s";
        header('Location: ' . BASE_URL);
        exit();
    }

    error_log("RoleMiddleware: Acesso permitido");
    return true;
    }
}
<?php
class AuthMiddleware
{
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            return false;
        }
        
        // Verificar se o usuário ainda existe no banco de dados
        $user = $this->userModel->getUserPeloId($_SESSION['user_id']);
        if (!$user) {
            session_destroy();
            header("Location: " . BASE_URL . "login");
            return false;
        }
        
        return true;
    }
}
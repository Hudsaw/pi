<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\UserModel;
use PDO;

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel        = new UserModel(Database::getInstance());
    }

    //Login

    public function mostrarLogin()
    {
        error_log("Exibindo tela de login");
        if ($this->estaLogado()) {
            $this->redirecionaPainel();
        }

        $data = [
            'erro' => $_SESSION['login_error'] ?? null,
            'login' => true
        ];

        $this->render('login', $data);
        unset($_SESSION['login_error']);
    }

    public function logar()
    {
        error_log("Tentativa de login");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectcomErro('Método inválido');
            return;
        }

        sleep(2);
        
        $cpf      = $_POST['cpf'];
        $password = $_POST['senha'];
        
        if (empty($cpf) || empty($senha)) {
            $this->redirectcomErro('CPF e senha são obrigatórios');
            return;
        }

        $user = $this->userModel->autenticar($cpf, $password);
        
        if (! $user) {
            $this->redirectcomErro('Credenciais inválidas');
            return;
        }
        
        if ($user) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_role'] = $user['tipo'];
            
            $redirect = $_SESSION['redirect_url'] ?? '/painel';
            unset($_SESSION['redirect_url']);
            
            $this->redirectPainel();
        }
    }

    private function redirecionaPainel()
    {
        error_log("Redirecionado ao Painel");
        $painel = match ($_SESSION['tipo_usuario']) {
            'admin' => 'admin',
            'costureira' => 'costureira',
            default => ''
        };
        if (! headers_sent()) {
            header('Location: ' . BASE_URL . $painel);
        } else {
            echo '<script>window.location.href="' . BASE_URL . $painel . '";</script>';
        }
        exit();
    }
    
    private function redirectcomErro($message)
    {
        error_log("Redirecionado com erro");
        if (! headers_sent()) {
            $_SESSION['login_error'] = $message;
            header('Location: ' . BASE_URL . 'login');
            exit();
        } else {
            echo '<script>window.location.href="' . BASE_URL . 'login";</script>';
            exit();
        }
    }
    
    private function estaLogado()
    {
        return (isset($_SESSION['user_id']), $_SESSION['logged_in']);
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }

    private function redirect($url)
    {
        $baseUrl = rtrim(BASE_URL, '/') . '/';
        $path    = ltrim($url, '/');
        header("Location: " . $baseUrl . $path);
        exit();
    }

}

<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\UserModel;
use PDO;

class AuthController extends BaseController
{

    public function mostrarLogin()
    {
        error_log("Exibindo tela de login");
        if ($this->estaLogado()) {
            $this->redirecionaPainel();
        }

        $data = [
            'erro' => $_SESSION['login_error'] ?? null,
            'login' => true,
            'title' => 'Login - PontoCerto',
            'usuarioLogado' => false,
            'nomeUsuario' => 'Visitante'
        ];

        $this->render('auth/login', $data);
        unset($_SESSION['login_error']);
    }

    public function logar()
    {
        error_log("Tentativa de login");

        if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    error_log("Tentativa de login - SESSION: " . print_r($_SESSION, true));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectcomErro('Método inválido');
            return;
        }

        sleep(2);
        
        $cpf = preg_replace('/\D/', '', trim($_POST['cpf'] ?? ''));
        $password = trim($_POST['senha'] ?? '');
        
        if (empty($cpf) || empty($password)) {
            $this->redirectcomErro('CPF e senha são obrigatórios');
            return;
        }

        $user = $this->userModel->autenticar($cpf, $password);
        
        if (! $user) {
            $this->redirectcomErro('Credenciais inválidas');
            return;
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['tipo_usuario'] = $user['tipo'];
        $_SESSION['user_role'] = $user['tipo']; 
        
        error_log("Login bem-sucedido para usuario ID: " . $user['id'] . ", Tipo: " . $user['tipo']);
        
        // Redirecionar para o painel baseado no tipo de usuário
        $dashboard = match($user['tipo']) {
            'admin' => '/admin/painel',
            'costureira' => '/costura/painel',
            default => '/'
        };
        
        $redirectUrl = BASE_URL . ltrim($dashboard, '/');
error_log("Redirecionando para: " . $redirectUrl . " - SESSION: " . print_r($_SESSION, true));
session_regenerate_id(true);
header('Location: ' . $redirectUrl);
        exit;
    }
    

    private function redirecionaPainel()
    {
        error_log("Redirecionado ao Painel");
        $tipo = $_SESSION['tipo_usuario'] ?? '';
        
        $dashboard = match ($tipo) {
            'admin' => '/admin/painel',
            'costureira' => '/costura/painel',
            default => '/'
        };

        if (!headers_sent()) {
            header('Location: ' . BASE_URL . ltrim($dashboard, '/'));
        } else {
            echo '<script>window.location.href="' . BASE_URL . ltrim($dashboard, '/') . '";</script>';
        }
        exit();
    }
    
    private function redirectcomErro($message)
    {
        error_log("Redirecionado com erro");
        $_SESSION['login_error'] = $message;
        
        if (!headers_sent()) {
            header('Location: ' . BASE_URL . 'login');
            exit();
        } else {
            echo '<script>window.location.href="' . BASE_URL . 'login";</script>';
            exit();
        }
    }

    public function logout()
    {
        session_destroy();
        session_unset();
        
        header('Location: ' . BASE_URL);
        exit;
    }

    public function showResetPassword()
    {
        if ($this->estaLogado()) {
            $this->redirecionaPainel();
        }

        $data = [
            'title'   => 'Resetar Senha',
            'error'   => $_SESSION['erro_reset'] ?? null,
            'success' => $_SESSION['sucesso_reset'] ?? null,
        ];

        unset($_SESSION['erro_reset']);
        unset($_SESSION['sucesso_reset']);

        $this->render('auth/resetar-senha', $data);
    }

    public function handleResetRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_reset'] = 'Método inválido';
            header('Location: ' . BASE_URL . 'resetar-senha');
            exit();
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        if (empty($email)) {
            $_SESSION['erro_reset'] = 'E-mail é obrigatório';
            header('Location: ' . BASE_URL . 'resetar-senha');
            exit();
        }

        $user = $this->userModel->getUserByEmail($email);

        if (! $user) {
            $_SESSION['erro_reset'] = 'E-mail não encontrado em nosso sistema';
            header('Location: ' . BASE_URL . 'resetar-senha');
            exit();
        }

        $token  = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + 3600);

        if ($this->userModel->createPasswordResetToken($user['id'], $token, $expiry)) {
            // URL deve ser absoluta para o formulário de reset
            $resetUrl = BASE_URL . 'resetar-senha/confirmar?token=' . urlencode($token);
            $enviado  = $this->notificacaoModel->enviarEmailResetSenha($email, $resetUrl);

            if ($enviado) {
                $_SESSION['sucesso_reset'] = 'Um e-mail com instruções foi enviado para ' . $email;
            } else {
                $_SESSION['erro_reset'] = 'Erro ao enviar e-mail. Tente novamente mais tarde.';
            }
        } else {
            $_SESSION['erro_reset'] = 'Erro ao processar solicitação';
        }

        header('Location: ' . BASE_URL . 'resetar-senha');
        exit();
    }

    
    public function showResetPasswordForm($token = null)
{
    if ($this->estaLogado()) {
        $this->redirecionaPainel();
    }

    // Verificar se o token está vindo via GET
    $token = $token ?? ($_GET['token'] ?? null);

    if (!$token) {
        $_SESSION['erro_reset'] = 'Token não fornecido';
        header('Location: ' . BASE_URL . 'resetar-senha');
        exit();
    }

    // Verificar se o token é válido e obter o ID do usuário
    $userId = $this->userModel->getUserIdByResetToken($token);

    if (!$userId) {
        $_SESSION['erro_reset'] = 'Token inválido ou expirado';
        header('Location: ' . BASE_URL . 'resetar-senha');
        exit();
    }

    $data = [
        'title' => 'Nova Senha',
        'token' => $token,
        'error' => $_SESSION['erro_reset'] ?? null,
    ];

    unset($_SESSION['erro_reset']);

    $this->render('auth/nova-senha', $data);
}

    public function handlePasswordReset()
    {
        // Inicia a sessão se não estiver ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica o método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_reset'] = 'Método inválido';
            $this->redirect(BASE_URL . 'resetar-senha');
            return;
        }

        // Validações básicas
        $token           = $_POST['token'] ?? '';
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password) || empty($confirmPassword)) {
            $_SESSION['erro_reset'] = 'Todos os campos são obrigatórios';
            $this->redirect(BASE_URL . 'resetar-senha/confirmar?token=' . urlencode($token));
            return;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['erro_reset'] = 'As senhas não coincidem';
            $this->redirect(BASE_URL . 'resetar-senha/confirmar?token=' . urlencode($token));
            return;
        }

        if (strlen($password) < 8) {
            $_SESSION['erro_reset'] = 'A senha deve ter pelo menos 8 caracteres';
            $this->redirect(BASE_URL . 'resetar-senha/confirmar?token=' . urlencode($token));
            return;
        }

        // Processa o token e atualiza a senha
        $userId = $this->userModel->getUserIdByResetToken($token);
        if (! $userId) {
            $_SESSION['erro_reset'] = 'Token inválido ou expirado';
            $this->redirect(BASE_URL . 'resetar-senha');
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($this->userModel->updatePassword($userId, $passwordHash)) {
            $this->userModel->invalidateResetToken($token);
            $_SESSION['sucesso_reset'] = 'Senha alterada com sucesso! Faça login com sua nova senha.';
            $this->redirect(BASE_URL . 'login');
        } else {
            $_SESSION['erro_reset'] = 'Erro ao atualizar senha';
            $this->redirect(BASE_URL . 'resetar-senha/confirmar?token=' . urlencode($token));
        }
    }

    

}
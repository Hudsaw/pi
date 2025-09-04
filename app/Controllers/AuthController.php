<?php
class AuthController
{
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function mostrarLogin()
    {
        error_log("Exibindo tela de login");
        if ($this->estaLogado()) {
            $this->redirect('/');
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
            $this->redirect('/login');
        }
        
        $cpf      = $_POST['cpf'];
        $password = $_POST['senha'];
        
        $user = $this->userModel->autenticar($cpf, $password);
        
        if ($user) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_role'] = $user['tipo'];

            $redirect = $_SESSION['redirect_url'] ?? '/painel';
            unset($_SESSION['redirect_url']);

            $this->redirect($redirect);
        }

        $_SESSION['login_error'] = "Credenciais inválidas";
        $this->redirect('/login');
    }

    public function cadastrarUsuario()
    {
        error_log("Tentativa de cadastro");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/criar-usuario');
        }

        $data = $this->validarUser($_POST, false);

        if (isset($data['errors'])) {
            error_log("Erros de validacao: " . print_r($data['errors'], true));
            $_SESSION['registrar_erros'] = $data['errors'];
            $_SESSION['registrar_data']  = $_POST;
            $this->redirect('/criar-usuario');
        }

        $userId = $this->userModel->criarUser($data);

        if ($userId) {
            // $_SESSION['user_id']   = $userId;
            // $_SESSION['user_name'] = $data['nome'];
            $this->redirect('/usuarios');
        }

        $_SESSION['registrar_erros'] = ['Falha ao criar usuário'];
        $this->redirect('/criar-usuario');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }

    public function salvarUsuario()
    {
        error_log("Tentativa de atualizacao de curriculo");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/editar-usuario');
        }

        $userId = $_POST['id'];
        $data   = $this->validarUser($_POST, true);

        if (isset($data['errors'])) {
            $_SESSION['registrar_erros'] = $data['errors'];
            $this->redirect('/editar-usuario');
        }

        $success = $this->userModel->atualizarUser($userId, $data);

        if ($success) {
            $_SESSION['success_message'] = 'Currículo atualizado com sucesso!';
            $this->redirect('/visualizar-usuario?id=' . $userId);
        }

        $_SESSION['registrar_erros'] = ['Erro ao atualizar o currículo'];
        $this->redirect('/editar-usuario');
    }

    public function removerUsuario() 
    {
        error_log("Tentativa de remocao de usuario");
        $userId = $_GET['id'];

        if (empty($userId) || !is_numeric($userId)) {
            $_SESSION['error_message'] = 'ID de usuário inválido';
            $this->redirect('/usuarios');
        }

        $success = $this->userModel->removerUser($userId);

        if ($success) {
            $_SESSION['success_message'] = 'Usuário removido com sucesso!';
            $this->redirect('/usuarios');
        }

        $_SESSION['error_message'] = 'Erro ao remover o usuário';
        $this->redirect('/usuarios');
    }

    // Métodos Auxiliares

    private function validarUser($post, $isUpdate = false)
    {
        $errors = [];
        $data   = [
            'nome'           => trim($post['nome'] ?? ''),
            'telefone'       => trim($post['telefone'] ?? ''),
            'email'          => filter_var(trim($post['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'cpf'            => trim($post['cpf'] ?? ''),
            'cep'            => trim($post['cep'] ?? ''),
            'logradouro'     => trim($post['logradouro'] ?? ''),
            'complemento'    => trim($post['complemento'] ?? ''),
            'cidade'         => trim($post['cidade'] ?? ''),
            'tipo_chave_pix' => trim($post['tipo_chave_pix'] ?? ''),
            'chave_pix'      => trim($post['chave_pix'] ?? ''),
            'senha'          => trim($post['senha'] ?? ''),
            'csenha'         => trim($post['csenha'] ?? ''),
        ];

        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }
        
        if (! $isUpdate) {
            if (strlen($data['senha']) < 8) {
                $errors['senha'] = 'Senha deve ter pelo menos 8 caracteres';
            } elseif ($data['senha'] !== $data['csenha']) {
                $errors['csenha'] = 'Senhas não coincidem';
            }
            
            if (empty($data['cpf'])) {
                $errors['cpf'] = 'CPF é obrigatório';
            } elseif (! preg_match('/^\d{11}$/', $data['cpf'])) {
                $errors['cpf'] = 'CPF deve conter exatamente 11 dígitos numéricos';
            } elseif ($this->userModel->cpfExiste($data['cpf'])) {
                $errors['cpf'] = 'CPF já cadastrado';
            }

            if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email inválido';
            } elseif ($this->userModel->emailExiste($data['email'])) {
                $errors['email'] = 'Email já cadastrado';
            }
        } else {
            if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email inválido';
            }
        }

        if (! empty($errors)) {
            return ['errors' => $errors];
        }

        return $data;
    }

    private function estaLogado()
    {
        return isset($_SESSION['user_id']);
    }

    private function redirect($url)
    {
        $baseUrl = rtrim(BASE_URL, '/') . '/';
        $path    = ltrim($url, '/');
        header("Location: " . $baseUrl . $path);
        exit();
    }

    private function render($view, $data = [])
    {
        extract($data);
        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . $view . '.php';
        require VIEWS_PATH . 'footer.php';
    }
}

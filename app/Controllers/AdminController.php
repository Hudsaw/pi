<?php
namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\UserModel;
use Exception;
use PDO;

class AdminController
{
    protected $adminModel;
    protected $userModel;

    public function __construct()
    {
        parent::__construct();

        $this->adminModel       = new AdminModel($this->pdo);
        $this->userModel        = new UserModel($this->pdo);
    }

    public function painel()
    {
        error_log("Exibindo painel");
        $user  = $this->getUsuario();

        $this->render('painel', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
        ]);
    }

    public function usuarios()
    {
        error_log("Exibindo usuarios");    
        $user  = $this->getUsuario();

        $this->render('usuarios', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'listaUsuarios' => $this->userModel->getTodosUser(), 
        ]);
    }

    public function mostrarCadastro()
    {
        error_log("Exibindo tela de cadastro");
        $user  = $this->getUsuario();

        $data = [
            'errors'        => $_SESSION['registrar_erros'] ?? [],
            'old'           => $_SESSION['registrar_data'] ?? [],
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'usuario'   => [
                'nome'           => $_POST['nome'] ?? '',
                'telefone'       => $_POST['telefone'] ?? '',
                'email'          => $_POST['email'] ?? '',
                'cpf'            => $_POST['cpf'] ?? '',
                'cep'            => $_POST['cep'] ?? '',
                'logradouro'     => $_POST['logradouro'] ?? '',
                'complemento'    => $_POST['complemento'] ?? '',
                'cidade'         => $_POST['cidade'] ?? '',
                'tipo_chave_pix' => $_POST['tipo_chave_pix'] ?? '',
                'chave_pix'      => $_POST['chave_pix'] ?? '',
            ]
        ];

        $this->render('criar-usuario', $data);
        unset($_SESSION['registrar_erros'], $_SESSION['registrar_data']);
    }
    public function visualizarUsuario()
    {
        error_log("Exibindo usuarios");
        $user  = $this->getUsuario();
        
        $id = $_GET['id'];

        $this->render('visualizar-usuario', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'usuario'       => $this->userModel->getUserPeloId($id),
        ]);
    }

    public function editarUsuario()
    {
        error_log("Editando usuario");
        $user  = $this->getUsuario();
        
        $id = $_GET['id'];

        $this->render('editar-usuario', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'usuario'       => $this->userModel->getUserPeloId($id),
        ]);
    }

    //Cadastro

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


}
<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CosturaModel;
use App\Models\UserModel;
use Exception;
use PDO;

class CosturaController extends BaseController
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function painel()
    {
        $user = $this->getUsuario();
        error_log("CosturaController - User ID: " . ($user['id'] ?? 'null'));
    error_log("CosturaController - User Role: " . ($_SESSION['user_role'] ?? 'null'));

        // Buscar dados para o dashboard
        $servicosAtivos = $this->servicoModel->getServicosAtivosPorCostureira($user['id']);
        $pagamentoMes = $this->costuraModel->calcularPagamentoMes($user['id']);
        $proximasEntregas = $this->costuraModel->contarProximasEntregas($user['id']);

        $this->render('costura/painel', [
            'title' => 'PontoCerto - Meu Painel',
            'user' => $user,
            'nomeUsuario' => $user['nome'],
            'usuarioLogado' => true,
            'servicosAtivos' => count($servicosAtivos),
            'pagamentoMes' => $pagamentoMes,
            'proximasEntregas' => $proximasEntregas,
            'servicos' => $servicosAtivos
        ]);
    }

    // Visualizar perfil da costureira
public function visualizarPerfil()
{
    $user = $this->getUsuario();
    
    $this->render('costura/visualizar-perfil', [
        'title' => 'PontoCerto - Meu Perfil',
        'user' => $user,
        'nomeUsuario' => $user['nome'],
        'usuarioLogado' => true,
        'usuario' => $this->userModel->getUserPeloId($user['id'])
    ]);
}

// Editar perfil da costureira
public function editarPerfil()
{
    $user = $this->getUsuario();

    $this->render('costura/editar-perfil', [
        'title' => 'PontoCerto - Editar Perfil',
        'user' => $user,
        'nomeUsuario' => $user['nome'],
        'usuarioLogado' => true,
        'usuario' => $this->userModel->getUserPeloId($user['id']),
        'errors' => $_SESSION['perfil_erros'] ?? [],
        'old' => $_SESSION['perfil_data'] ?? []
    ]);
    
    unset($_SESSION['perfil_erros'], $_SESSION['perfil_data']);
}

// Atualizar perfil da costureira
public function atualizarPerfil()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('costura/editar-perfil');
    }

    $user = $this->getUsuario();
    $userId = $_POST['id'];

    // Verificar se o usuário está tentando editar seu próprio perfil
    if ($user['id'] != $userId) {
        $_SESSION['error_message'] = 'Acesso não autorizado';
        $this->redirect('costura/painel');
    }

    $data = $this->validarPerfil($_POST);

    if (isset($data['errors'])) {
        $_SESSION['perfil_erros'] = $data['errors'];
        $_SESSION['perfil_data'] = $_POST;
        $this->redirect('costura/editar-perfil');
    }

    try {
        // Usar o novo método específico para perfil
        $success = $this->userModel->atualizarUser($userId, $data);

        if ($success) {
            $_SESSION['success_message'] = 'Perfil atualizado com sucesso!';
            
            // Atualizar os dados na sessão se o nome foi alterado
            if (isset($_SESSION['user_name']) && $_SESSION['user_name'] !== $data['nome']) {
                $_SESSION['user_name'] = $data['nome'];
            }
            
            $this->redirect('costura/visualizar-perfil');
        } else {
            $_SESSION['perfil_erros'] = ['Erro ao atualizar o perfil. Tente novamente.'];
            $_SESSION['perfil_data'] = $_POST;
            $this->redirect('costura/editar-perfil');
        }
        
    } catch (Exception $e) {
        $_SESSION['perfil_erros'] = ['Erro: ' . $e->getMessage()];
        $_SESSION['perfil_data'] = $_POST;
        $this->redirect('costura/editar-perfil');
    }
}

// Validação específica para perfil de costureira
private function validarPerfil($post)
{
    $errors = [];
    $user = $this->getUsuario();
    
    $data = [
        'nome' => trim($post['nome'] ?? ''),
        'telefone' => trim($post['telefone'] ?? ''),
        'email' => filter_var(trim($post['email'] ?? ''), FILTER_SANITIZE_EMAIL),
        'cep' => trim($post['cep'] ?? ''),
        'logradouro' => trim($post['logradouro'] ?? ''),
        'complemento' => trim($post['complemento'] ?? ''),
        'cidade' => trim($post['cidade'] ?? ''),
        'tipo_chave_pix' => trim($post['tipo_chave_pix'] ?? ''),
        'chave_pix' => trim($post['chave_pix'] ?? ''),
        'senha' => trim($post['senha'] ?? ''),
        'csenha' => trim($post['csenha'] ?? ''),
    ];

    // Validações básicas
    if (empty($data['nome'])) {
        $errors['nome'] = 'Nome é obrigatório';
    } elseif (strlen($data['nome']) < 2) {
        $errors['nome'] = 'Nome deve ter pelo menos 2 caracteres';
    }

    if (empty($data['telefone'])) {
        $errors['telefone'] = 'Telefone é obrigatório';
    } else {
        $data['telefone'] = preg_replace('/\D/', '', $data['telefone']);
        if (strlen($data['telefone']) < 10 || strlen($data['telefone']) > 11) {
            $errors['telefone'] = 'Telefone inválido';
        }
    }

    if (empty($data['email'])) {
        $errors['email'] = 'Email é obrigatório';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email inválido';
    } else {
        // Verificar se email já existe em outro usuário
        $existingUser = $this->userModel->getUserPeloEmail($data['email']);
        if ($existingUser && $existingUser['id'] != $user['id']) {
            $errors['email'] = 'Este email já está em uso por outro usuário';
        }
    }

    // Validação de CEP
    if (!empty($data['cep'])) {
        $data['cep'] = preg_replace('/\D/', '', $data['cep']);
        if (strlen($data['cep']) !== 8) {
            $errors['cep'] = 'CEP inválido';
        }
    }

    // Validação de senha se for fornecida
    if (!empty($data['senha'])) {
        if (strlen($data['senha']) < 8) {
            $errors['senha'] = 'Senha deve ter pelo menos 8 caracteres';
        } elseif ($data['senha'] !== $data['csenha']) {
            $errors['csenha'] = 'Senhas não coincidem';
        }
    } else {
        // Remover senha se não for fornecida
        unset($data['senha']);
    }
    
    // Remover confirmação de senha dos dados finais
    unset($data['csenha']);

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return $data;
}

    // Meus Pagamentos
    public function pagamentos()
    {
        $user = $this->getUsuario();
        $pagamentos = $this->pagamentoModel->getPagamentosPorCostureira($user['id']);

        $this->render('costura/pagamentos', [
            'title' => 'PontoCerto - Meus Pagamentos',
            'user' => $user,
            'nomeUsuario' => $user['nome'],
            'usuarioLogado' => true,
            'pagamentos' => $pagamentos
        ]);
    }
    
}
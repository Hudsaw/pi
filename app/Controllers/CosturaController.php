<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CosturaModel;
use App\Models\NotificacaoModel;
use App\Models\PagamentoModel;
use App\Models\ServicoModel;
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
        
        // Buscar dados para o dashboard
        $servicosAtivos = $this->servicoModel->getServicosAtivosPorCostureira($user['id']);
        $pagamentoMes = $this->pagamentoModel->calcularPagamentoMes($user['id']);
        $proximasEntregas = $this->pagamentoModel->contarProximasEntregas($user['id']);
        $mensagensNaoLidas = $this->notificacaoModel->getNotificacoesPorUsuario($user['id'], 50);

        $this->render('costura/painel', [
            'title' => 'PontoCerto - Meu Painel',
            'user' => $user,
            'nomeUsuario' => $user['nome'],
            'usuarioLogado' => true,
            'servicosAtivos' => count($servicosAtivos),
            'pagamentoMes' => $pagamentoMes,
            'proximasEntregas' => $proximasEntregas,
            'servicos' => $servicosAtivos,
            'mensagensNaoLidas' => $mensagensNaoLidas
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

    //Serviços
    public function servicos() {
    $user = $this->getUsuario();
    
    // Busca os 10 serviços mais recentes (inclui ativos e finalizados)
    $servicosRecentes = $this->servicoModel->getServicosRecentesPorCostureira($user['id'], 10);
    
    $this->render('costura/servicos', [
        'title' => 'PontoCerto - Meus Serviços',
        'user' => $user,
        'nomeUsuario' => $user['nome'],
        'usuarioLogado' => true,
        'servicos' => $servicosRecentes
    ]);
}

    public function visualizarServico()
{
    // Verificar se é POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error_message'] = 'Método não permitido';
        $this->redirect('costura/servicos');
        return;
    }

    $user = $this->getUsuario();
    
    // Verificar se o usuário está logado
    if (!$user) {
        $this->redirect('login');
        return;
    }
    
    $servicoId = $_POST['servico_id'] ?? null;

    if (!$servicoId) {
        $_SESSION['error_message'] = 'Serviço não identificado';
        $this->redirect('costura/servicos');
        return;
    }
    
    // Buscar o serviço pelo ID
    $servico = $this->servicoModel->getServicoPorId($servicoId);
    
    // Verificar se o serviço existe
    if (!$servico) {
        $_SESSION['error_message'] = 'Serviço não encontrado';
        $this->redirect('costura/servicos');
        return;
    }
    
    // Verificar se o serviço pertence à costureira
    if ($servico['costureira_id'] != $user['id']) {
        $_SESSION['error_message'] = 'Acesso não autorizado';
        $this->redirect('costura/servicos');
        return;
    }

    $this->render('costura/visualizar-servico', [
        'title' => 'PontoCerto - Detalhes do Serviço',
        'user' => $user,
        'nomeUsuario' => $user['nome'],
        'usuarioLogado' => true,
        'servico' => $servico
    ]);
}

    // Método para atualizar progresso do serviço
    public function atualizarProgresso()
{
    // Verificar se é POST via AJAX ou formulário normal
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error_message'] = 'Método não permitido';
        $this->redirect('costura/servicos');
        return;
    }

    $user = $this->getUsuario();
    
    // Pode receber via POST normal ou JSON (para AJAX)
    if (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $servicoId = $input['servico_id'] ?? null;
        $pecasConcluidas = isset($input['pecas_concluidas']) ? (int)$input['pecas_concluidas'] : null;
    } else {
        $servicoId = $_POST['servico_id'] ?? null;
        $pecasConcluidas = isset($_POST['pecas_concluidas']) ? (int)$_POST['pecas_concluidas'] : null;
    }

    if (!$servicoId) {
        $_SESSION['error_message'] = 'Serviço não identificado';
        $this->redirect('costura/servicos');
        return;
    }

    if ($pecasConcluidas === null) {
        $_SESSION['error_message'] = 'Informe a quantidade de peças concluídas';
        $this->redirect('costura/visualizar-servico', ['servico_id' => $servicoId]);
        return;
    }

    // Buscar o serviço
    $servico = $this->servicoModel->getServicoPorId($servicoId);
    
    if (!$servico) {
        $_SESSION['error_message'] = 'Serviço não encontrado';
        $this->redirect('costura/servicos');
        return;
    }

    // Verificar se o serviço pertence à costureira
    if ($servico['costureira_id'] != $user['id']) {
        $_SESSION['error_message'] = 'Acesso não autorizado';
        $this->redirect('costura/servicos');
        return;
    }

    // Verificar se o serviço não está finalizado
    if ($servico['status'] == 'Finalizado') {
        $_SESSION['error_message'] = 'Este serviço já foi finalizado.';
        $this->redirect('costura/visualizar-servico', ['servico_id' => $servicoId]);
        return;
    }

    try {
        $this->servicoModel->atualizarProgresso($servicoId, $pecasConcluidas);
        $_SESSION['success_message'] = 'Progresso atualizado com sucesso!';
        
        // Se for requisição AJAX, retornar JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Progresso atualizado com sucesso!']);
            exit;
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro: ' . $e->getMessage();
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    $this->redirect('costura/painel');
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

    public function visualizarPagamento()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error_message'] = 'Método não permitido';
        $this->redirect('costura/pagamentos');
    }

    $user = $this->getUsuario();
    $pagamentoId = $_POST['pagamento_id'] ?? null;

    if (!$pagamentoId) {
        $_SESSION['error_message'] = 'Pagamento não identificado';
        $this->redirect('costura/pagamentos');
    }
    
    // Buscar o pagamento pelo ID
    $pagamento = $this->pagamentoModel->getPagamentoPorId($pagamentoId);
    
    // Verificar se o pagamento existe
    if (!$pagamento) {
        $_SESSION['error_message'] = 'Pagamento não encontrado';
        $this->redirect('costura/pagamentos');
    }
    
    // Verificar se o pagamento pertence à costureira
    if ($pagamento['costureira_id'] != $user['id']) {
        $_SESSION['error_message'] = 'Acesso não autorizado';
        $this->redirect('costura/pagamentos');
    }

    $itens = $this->pagamentoModel->getItensPagamento($pagamentoId);

    $this->render('costura/visualizar-pagamento', [
        'title' => 'PontoCerto - Detalhes do Pagamento',
        'user' => $user,
        'nomeUsuario' => $user['nome'],
        'usuarioLogado' => true,
        'pagamento' => $pagamento,
        'itens' => $itens
    ]);
}

    // Notificações
    public function mensagens()
    {
        $user = $this->getUsuario();
        
        // Buscar mensagens (notificações) do usuário
        $mensagens = $this->notificacaoModel->getNotificacoesPorUsuario($user['id'], 100);

        $this->render('costura/mensagens', [
            'title' => 'PontoCerto - Minhas Mensagens',
            'user' => $user,
            'nomeUsuario' => $user['nome'],
            'usuarioLogado' => true,
            'mensagens' => $mensagens
        ]);
    }

    public function excluirMensagem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('costura/mensagens');
        }

        $user = $this->getUsuario();
        $mensagemId = $_POST['mensagem_id'] ?? null;

        if (!$mensagemId) {
            $_SESSION['error_message'] = 'Mensagem não identificada';
            $this->redirect('costura/mensagens');
        }

        try {
            // Excluir a notificação
            $excluido = $this->notificacaoModel->excluirNotificacoes($user['id'], [$mensagemId]);

            if ($excluido) {
                $_SESSION['success_message'] = 'Mensagem excluída com sucesso!';
            } else {
                $_SESSION['error_message'] = 'Erro ao excluir mensagem';
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erro ao excluir mensagem: ' . $e->getMessage();
        }

        $this->redirect('costura/mensagens');
    }
    
}
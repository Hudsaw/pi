<?php
namespace App\Controllers;

use Exception;
use PDO;

class AdminController extends BaseController
{

    // Painel
    public function painel()
    {
        error_log("Exibindo painel");
        $user  = $this->getUsuario();

        $this->render('admin/painel', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
        ]);
    }

    // Usuarios
    public function usuarios()
{
    $user  = $this->getUsuario();
    
    $termoBusca = $_GET['search'] ?? '';
    
    if (!empty($termoBusca)) {
        $listaUsuarios = $this->userModel->buscarUsuarios($termoBusca);
    } else {
        $listaUsuarios = $this->userModel->getTodosUser(); 
    }

    $this->render('admin/usuarios', [
        'title'         => 'PontoCerto',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'listaUsuarios' => $listaUsuarios,
        'termoBusca'    => $termoBusca 
    ]);
}

    public function mostrarCadastro()
    {
        $user  = $this->getUsuario();
        $especialidades = $this->userModel->getEspecialidade();

        $data = [
            'errors'        => $_SESSION['registrar_erros'] ?? [],
            'old'           => $_SESSION['registrar_data'] ?? [],
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'especialidades'=> $especialidades,
            'usuario'   => [
                'nome'           => '',
                'telefone'       => '',
                'email'          => '',
                'cpf'            => '',
                'cep'            => '',
                'logradouro'     => '',
                'complemento'    => '',
                'cidade'         => '',
                'especialidade_id' => '',
                'tipo_chave_pix' => '',
                'chave_pix'      => '',
            ]
        ];

        $this->render('admin/criar-usuario', $data);
        unset($_SESSION['registrar_erros'], $_SESSION['registrar_data']);
    }

    public function visualizarUsuario()
    {
        $user  = $this->getUsuario();
        $id = $_GET['id'];

        $this->render('admin/visualizar-usuario', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'usuario'       => $this->userModel->getUserPeloId($id),
        ]);
    }

    public function editarUsuario()
    {
        $user  = $this->getUsuario();
        $id = $_GET['id'];

        $this->render('admin/editar-usuario', [
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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/criar-usuario');
    }

    $data = $this->validarUser($_POST, false);

    if (isset($data['errors'])) {
        $_SESSION['registrar_erros'] = $data['errors'];
        $_SESSION['registrar_data'] = $_POST; 
        $this->redirect('admin/criar-usuario');
    }

    try {
        $userId = $this->userModel->criarUser($data);

        if ($userId) {
            $_SESSION['success_message'] = 'Usuário criado com sucesso!';
            $this->redirect('admin/usuarios');
        }

        $_SESSION['registrar_erros'] = ['Falha ao criar usuário'];
        $_SESSION['registrar_data'] = $_POST;
        $this->redirect('admin/criar-usuario');
        
    } catch (Exception $e) {
        $_SESSION['registrar_erros'] = [$e->getMessage()];
        $_SESSION['registrar_data'] = $_POST;
        $this->redirect('admin/criar-usuario');
    }
}
    
    public function salvarUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/editar-usuario');
        }

        $userId = $_POST['id'];
        $data   = $this->validarUser($_POST, true);

        if (isset($data['errors'])) {
            $_SESSION['registrar_erros'] = $data['errors'];
            $this->redirect('admin/editar-usuario');
        }

        $success = $this->userModel->atualizarUser($userId, $data);

        if ($success) {
            $_SESSION['success_message'] = 'Currículo atualizado com sucesso!';
            $this->redirect('admin/visualizar-usuario?id=' . $userId);
        }

        $_SESSION['registrar_erros'] = ['Erro ao atualizar o currículo'];
        $this->redirect('admin/editar-usuario');
    }

    public function removerUsuario() 
    {
        $userId = $_GET['id'];

        if (empty($userId) || !is_numeric($userId)) {
            $_SESSION['error_message'] = 'ID de usuário inválido';
            $this->redirect('admin/usuarios');
        }

        $success = $this->userModel->removerUser($userId);

        if ($success) {
            $_SESSION['success_message'] = 'Usuário removido com sucesso!';
            $this->redirect('admin/usuarios');
        }

        $_SESSION['error_message'] = 'Erro ao remover o usuário';
        $this->redirect('admin/usuarios');
    }

    // Lotes
    public function lotes()
    {
        error_log("Exibindo lotes");
        $user = $this->getUsuario();
        $filtro = $_GET['filtro'] ?? 'ativos';
        
        $listaLotes = $this->loteModel->getLotes($filtro);

        $this->render('admin/lotes', [
            'title'         => 'PontoCerto - Lotes',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'listaLotes'    => $listaLotes,
            'filtro'        => $filtro
        ]);
    }

    public function mostrarCriarLote()
    {
        error_log("Exibindo tela de criação de lote");
        $user = $this->getUsuario();

        $this->render('admin/criar-lote', [
            'title'         => 'PontoCerto - Criar Lote',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'errors'        => $_SESSION['lote_erros'] ?? [],
            'old'           => $_SESSION['lote_data'] ?? []
        ]);
        
        unset($_SESSION['lote_erros'], $_SESSION['lote_data']);
    }

    public function criarLote()
    {
        error_log("Tentativa de criação de lote");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/criar-lote');
        }

        $data = $this->validarLote($_POST);

        if (isset($data['errors'])) {
            error_log("Erros de validação: " . print_r($data['errors'], true));
            $_SESSION['lote_erros'] = $data['errors'];
            $_SESSION['lote_data'] = $_POST;
            $this->redirect('admin/criar-lote');
        }

        try {
            $loteId = $this->loteModel->criarLote($data);
            $_SESSION['success_message'] = 'Lote criado com sucesso!';
            $this->redirect('admin/lotes');
        } catch (Exception $e) {
            error_log("Erro ao criar lote: " . $e->getMessage());
            $_SESSION['lote_erros'] = ['Falha ao criar lote: ' . $e->getMessage()];
            $this->redirect('admin/criar-lote');
        }
    }

    public function visualizarLote()
    {
        error_log("Exibindo detalhes do lote");
        $user = $this->getUsuario();
        $id = $_GET['id'];
        
        $lote = $this->loteModel->getLotePorId($id);
        $pecas = $this->pecaModel->getPecasPorLote($id);

        $this->render('admin/visualizar-lote', [
            'title'         => 'PontoCerto - Visualizar Lote',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'lote'          => $lote,
            'pecas'         => $pecas
        ]);
    }

    public function operacoes()
    {
        error_log("Exibindo operações");
        $user = $this->getUsuario();
        $filtro = $_GET['filtro'] ?? 'ativos';
        
        $listaOperacoes = $this->operacaoModel->getOperacoes($filtro);

        $this->render('admin/operacoes', [
            'title'           => 'PontoCerto - Operações',
            'user'            => $user,
            'nomeUsuario'     => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado'   => $this->estaLogado(),
            'listaOperacoes'  => $listaOperacoes,
            'filtro'          => $filtro
        ]);
    }

    public function mostrarCriarOperacao()
    {
        error_log("Exibindo tela de criação de operação");
        $user = $this->getUsuario();

        $this->render('admin/criar-operacao', [
            'title'         => 'PontoCerto - Criar Operação',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'errors'        => $_SESSION['operacao_erros'] ?? [],
            'old'           => $_SESSION['operacao_data'] ?? []
        ]);
        
        unset($_SESSION['operacao_erros'], $_SESSION['operacao_data']);
    }

    public function criarOperacao()
    {
        error_log("Tentativa de criação de operação");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/criar-operacao');
        }

        $data = $this->validarOperacao($_POST);

        if (isset($data['errors'])) {
            error_log("Erros de validação: " . print_r($data['errors'], true));
            $_SESSION['operacao_erros'] = $data['errors'];
            $_SESSION['operacao_data'] = $_POST;
            $this->redirect('admin/criar-operacao');
        }

        try {
            $operacaoId = $this->operacaoModel->criarOperacao($data);
            $_SESSION['success_message'] = 'Operação criada com sucesso!';
            $this->redirect('admin/operacoes');
        } catch (Exception $e) {
            error_log("Erro ao criar operação: " . $e->getMessage());
            $_SESSION['operacao_erros'] = ['Falha ao criar operação: ' . $e->getMessage()];
            $this->redirect('admin/criar-operacao');
        }
    }

    public function adicionarPecaLote()
    {
        error_log("Adicionando peça ao lote");
        $user = $this->getUsuario();
        $loteId = $_GET['lote_id'];
        
        $lote = $this->loteModel->getLotePorId($loteId);
        $operacoes = $this->operacaoModel->getOperacoes('ativos');

        $this->render('admin/adicionar-peca', [
            'title'         => 'PontoCerto - Adicionar Peça',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'lote'          => $lote,
            'operacoes'     => $operacoes,
            'errors'        => $_SESSION['peca_erros'] ?? [],
            'old'           => $_SESSION['peca_data'] ?? []
        ]);
        
        unset($_SESSION['peca_erros'], $_SESSION['peca_data']);
    }

    public function criarPeca()
    {
        error_log("Tentativa de criação de peça");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/lotes');
        }

        $data = $this->validarPeca($_POST);

        if (isset($data['errors'])) {
            error_log("Erros de validação: " . print_r($data['errors'], true));
            $_SESSION['peca_erros'] = $data['errors'];
            $_SESSION['peca_data'] = $_POST;
            $this->redirect('admin/adicionar-peca?lote_id=' . $data['lote_id']);
        }

        try {
            $pecaId = $this->pecaModel->criarPeca($data);
            $_SESSION['success_message'] = 'Peça adicionada com sucesso!';
            $this->redirect('admin/visualizar-lote?id=' . $data['lote_id']);
        } catch (Exception $e) {
            error_log("Erro ao criar peça: " . $e->getMessage());
            $_SESSION['peca_erros'] = ['Falha ao adicionar peça: ' . $e->getMessage()];
            $this->redirect('admin/adicionar-peca?lote_id=' . $data['lote_id']);
        }
    }

    public function removerLote()
    {
        error_log("Tentativa de remoção de lote");
        $loteId = $_GET['id'];

        if (empty($loteId) || !is_numeric($loteId)) {
            $_SESSION['error_message'] = 'ID de lote inválido';
            $this->redirect('admin/lotes');
        }

        try {
            $success = $this->loteModel->desativarLote($loteId);

            if ($success) {
                $_SESSION['success_message'] = 'Lote removido com sucesso!';
            } else {
                $_SESSION['error_message'] = 'Erro ao remover o lote';
            }
        } catch (Exception $e) {
            error_log("Erro ao remover lote: " . $e->getMessage());
            $_SESSION['error_message'] = 'Erro ao remover o lote: ' . $e->getMessage();
        }

        $this->redirect('admin/lotes');
    }

    public function removerOperacao()
    {
        error_log("Tentativa de remoção de operação");
        $operacaoId = $_GET['id'];

        if (empty($operacaoId) || !is_numeric($operacaoId)) {
            $_SESSION['error_message'] = 'ID de operação inválido';
            $this->redirect('admin/operacoes');
        }

        try {
            $success = $this->operacaoModel->desativarOperacao($operacaoId);

            if ($success) {
                $_SESSION['success_message'] = 'Operação removida com sucesso!';
            } else {
                $_SESSION['error_message'] = 'Erro ao remover a operação';
            }
        } catch (Exception $e) {
            error_log("Erro ao remover operação: " . $e->getMessage());
            $_SESSION['error_message'] = 'Erro ao remover a operação: ' . $e->getMessage();
        }

        $this->redirect('admin/operacoes');
    }

    public function removerPeca()
    {
        error_log("Tentativa de remoção de peça");
        $pecaId = $_GET['id'];
        $loteId = $_GET['lote_id'];

        if (empty($pecaId) || !is_numeric($pecaId)) {
            $_SESSION['error_message'] = 'ID de peça inválido';
            $this->redirect('admin/visualizar-lote?id=' . $loteId);
        }

        try {
            $success = $this->pecaModel->removerPeca($pecaId);

            if ($success) {
                $_SESSION['success_message'] = 'Peça removida com sucesso!';
            } else {
                $_SESSION['error_message'] = 'Erro ao remover a peça';
            }
        } catch (Exception $e) {
            error_log("Erro ao remover peça: " . $e->getMessage());
            $_SESSION['error_message'] = 'Erro ao remover a peça: ' . $e->getMessage();
        }

        $this->redirect('admin/visualizar-lote?id=' . $loteId);
    }

    // Métodos de validação
    private function validarLote($post)
    {
        $errors = [];
        $data = [
            'empresa_id' => trim($post['empresa_id'] ?? ''),
            'descricao' => trim($post['descricao'] ?? ''),
            'quantidade' => trim($post['quantidade'] ?? ''),
            'valor' => trim($post['valor'] ?? ''),
            'data_inicio' => trim($post['data_inicio'] ?? ''),
            'data_prazo' => trim($post['data_prazo'] ?? '')
        ];

        if (empty($data['empresa_id'])) {
            $errors['empresa_id'] = 'ID da empresa é obrigatório';
        }

        if (empty($data['descricao'])) {
            $errors['descricao'] = 'Descrição é obrigatória';
        }

        if (empty($data['quantidade']) || !is_numeric($data['quantidade']) || $data['quantidade'] <= 0) {
            $errors['quantidade'] = 'Quantidade deve ser um número positivo';
        }

        if (empty($data['valor']) || !is_numeric($data['valor']) || $data['valor'] <= 0) {
            $errors['valor'] = 'Valor deve ser um número positivo';
        }

        if (empty($data['data_inicio'])) {
            $errors['data_inicio'] = 'Data de início é obrigatória';
        }

        if (empty($data['data_prazo'])) {
            $errors['data_prazo'] = 'Data de prazo é obrigatória';
        } elseif ($data['data_prazo'] < $data['data_inicio']) {
            $errors['data_prazo'] = 'Data de prazo não pode ser anterior à data de início';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return $data;
    }

    private function validarOperacao($post)
    {
        $errors = [];
        $data = [
            'nome' => trim($post['nome'] ?? ''),
            'descricao' => trim($post['descricao'] ?? ''),
            'valor' => trim($post['valor'] ?? ''),
            'tempo_estimado' => trim($post['tempo_estimado'] ?? '')
        ];

        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }

        if (empty($data['descricao'])) {
            $errors['descricao'] = 'Descrição é obrigatória';
        }

        if (empty($data['valor']) || !is_numeric($data['valor']) || $data['valor'] <= 0) {
            $errors['valor'] = 'Valor deve ser um número positivo';
        }

        if (empty($data['tempo_estimado']) || !is_numeric($data['tempo_estimado']) || $data['tempo_estimado'] <= 0) {
            $errors['tempo_estimado'] = 'Tempo estimado deve ser um número positivo';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return $data;
    }

    private function validarPeca($post)
    {
        $errors = [];
        $data = [
            'lote_id' => trim($post['lote_id'] ?? ''),
            'operacao_id' => trim($post['operacao_id'] ?? ''),
            'quantidade' => trim($post['quantidade'] ?? ''),
            'valor_unitario' => trim($post['valor_unitario'] ?? ''),
            'observacao' => trim($post['observacao'] ?? '')
        ];

        if (empty($data['lote_id']) || !is_numeric($data['lote_id'])) {
            $errors['lote_id'] = 'Lote é obrigatório';
        }

        if (empty($data['operacao_id']) || !is_numeric($data['operacao_id'])) {
            $errors['operacao_id'] = 'Operação é obrigatória';
        }

        if (empty($data['quantidade']) || !is_numeric($data['quantidade']) || $data['quantidade'] <= 0) {
            $errors['quantidade'] = 'Quantidade deve ser um número positivo';
        }

        if (empty($data['valor_unitario']) || !is_numeric($data['valor_unitario']) || $data['valor_unitario'] <= 0) {
            $errors['valor_unitario'] = 'Valor unitário deve ser um número positivo';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return $data;
    }
    
    
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
        'especialidade_id' => trim($post['especialidade_id'] ?? ''),
        'tipo_chave_pix' => trim($post['tipo_chave_pix'] ?? ''),
        'chave_pix'      => trim($post['chave_pix'] ?? ''),
        'senha'          => trim($post['senha'] ?? ''),
        'csenha'         => trim($post['csenha'] ?? ''),
    ];

    if (empty($data['nome'])) {
        $errors['nome'] = 'Nome é obrigatório';
    }
    
    if (!empty($data['telefone'])) {
        $data['telefone'] = preg_replace('/\D/', '', $data['telefone']);
    }
    
    if (!empty($data['cpf'])) {
        $cpfNumeros = preg_replace('/\D/', '', $data['cpf']);
        $data['cpf'] = $cpfNumeros;
    }
    
    if (!empty($data['cep'])) {
        $cepNumeros = preg_replace('/\D/', '', $data['cep']);
        $data['cep'] = $cepNumeros;
    }
    
    if (! $isUpdate) {
        if (strlen($data['senha']) < 8) {
            $errors['senha'] = 'Senha deve ter pelo menos 8 caracteres';
        } elseif ($data['senha'] !== $data['csenha']) {
            $errors['csenha'] = 'Senhas não coincidem';
        }
        
        if (empty($data['cpf'])) {
            $errors['cpf'] = 'CPF é obrigatório';
        } elseif (strlen($data['cpf']) !== 11) {
            $errors['cpf'] = 'CPF deve conter exatamente 11 dígitos numéricos';
        } elseif ($this->userModel->cpfExiste($data['cpf'])) {
            $errors['cpf'] = 'CPF já cadastrado';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        } elseif ($this->userModel->emailExiste($data['email'])) {
            $errors['email'] = 'Email já cadastrado';
        }
    } else {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        }
    }
    
    // Validação de especialidade
    if (empty($data['especialidade_id']) || !is_numeric($data['especialidade_id'])) {
        $errors['especialidade_id'] = 'Especialidade é obrigatória';
    }
    
    // Validação de telefone
    if (!empty($data['telefone']) && (strlen($data['telefone']) < 10 || strlen($data['telefone']) > 11)) {
        $errors['telefone'] = 'Telefone inválido';
    }

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return $data;
}

private function validarCPF($cpf)
{
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais (CPF inválido)
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Validação do primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }
    
    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;
    
    if ($cpf[9] != $digito1) {
        return false;
    }
    
    // Validação do segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }
    
    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;
    
    if ($cpf[10] != $digito2) {
        return false;
    }
    
    return true;
}      



}
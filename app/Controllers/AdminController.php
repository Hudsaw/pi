<?php
namespace App\Controllers;

use Exception;
use PDO;

class AdminController extends BaseController
{

    // Painel
    public function painel()
{
    error_log("Exibindo painel administrativo");
    $user = $this->getUsuario();

    // Buscar dados para o dashboard
    $totalUsuarios = $this->userModel->getTotalUsuarios();
    $totalEmpresas = $this->empresaModel->getTotalEmpresas();
    $totalLotes = $this->loteModel->getTotalLotes();
    $totalServicos = $this->servicoModel->getTotalServicos();
    $servicosAtivos = $this->servicoModel->getServicosAtivos();
    $lotesRecentes = $this->loteModel->getLotesRecentes(5);
    $servicosRecentes = $this->servicoModel->getServicosRecentes(5);

    $this->render('admin/painel', [
        'title' => 'PontoCerto - Painel Administrativo',
        'user' => $user,
        'nomeUsuario' => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'totalUsuarios' => $totalUsuarios,
        'totalEmpresas' => $totalEmpresas,
        'totalLotes' => $totalLotes,
        'totalServicos' => $totalServicos,
        'servicosAtivos' => $servicosAtivos,
        'lotesRecentes' => $lotesRecentes,
        'servicosRecentes' => $servicosRecentes
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

    public function reativarUsuario() 
    {
        $userId = $_GET['id'];

        if (empty($userId) || !is_numeric($userId)) {
            $_SESSION['error_message'] = 'ID de usuário inválido';
            $this->redirect('admin/usuarios');
        }

        $success = $this->userModel->reativarUser($userId);

        if ($success) {
            $_SESSION['success_message'] = 'Usuário reativado com sucesso!';
            $this->redirect('admin/usuarios');
        }

        $_SESSION['error_message'] = 'Erro ao reativar o usuário';
        $this->redirect('admin/usuarios');
    }


    //Empresas
    public function empresas()
{
    error_log("Exibindo empresas");
    $user = $this->getUsuario();
    $filtro = $_GET['filtro'] ?? 'ativos';
    $termoBusca = $_GET['search'] ?? '';
    
    if (!empty($termoBusca)) {
        $listaEmpresas = $this->empresaModel->buscarEmpresas($termoBusca);
    } else {
        $listaEmpresas = $this->empresaModel->getEmpresas($filtro);
    }

    $this->render('admin/empresas', [
        'title'         => 'PontoCerto - Empresas',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'listaEmpresas' => $listaEmpresas,
        'filtro'        => $filtro,
        'termoBusca'    => $termoBusca
    ]);
}

public function mostrarCriarEmpresa()
{
    error_log("Exibindo tela de criação de empresa");
    $user = $this->getUsuario();

    $this->render('admin/criar-empresa', [
        'title'         => 'PontoCerto - Criar Empresa',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'errors'        => $_SESSION['empresa_erros'] ?? [],
        'old'           => $_SESSION['empresa_data'] ?? []
    ]);
    
    unset($_SESSION['empresa_erros'], $_SESSION['empresa_data']);
}

public function criarEmpresa()
{
    error_log("Tentativa de criação de empresa");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/criar-empresa');
    }

    $data = $this->validarEmpresa($_POST);

    if (isset($data['errors'])) {
        $_SESSION['empresa_erros'] = $data['errors'];
        $_SESSION['empresa_data'] = $_POST;
        $this->redirect('admin/criar-empresa');
    }

    try {
        $empresaId = $this->empresaModel->criarEmpresa($data);
        $_SESSION['success_message'] = 'Empresa criada com sucesso!';
        $this->redirect('admin/empresas');
    } catch (Exception $e) {
        $_SESSION['empresa_erros'] = ['Falha ao criar empresa: ' . $e->getMessage()];
        $this->redirect('admin/criar-empresa');
    }
}

public function mostrarEmpresa()
{
    error_log("Exibindo detalhes da empresa");
    $user = $this->getUsuario();
    $id = $_GET['id'];
    
    $empresa = $this->empresaModel->getEmpresaPorId($id);

    $this->render('admin/visualizar-empresa', [
        'title'         => 'PontoCerto - Visualizar Empresa',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'empresa'       => $empresa
    ]);
}

public function mostrarEditarEmpresa()
{
    error_log("Exibindo tela de edição de empresa");
    $user = $this->getUsuario();
    $id = $_GET['id'];

    $empresa = $this->empresaModel->getEmpresaPorId($id);

    $this->render('admin/editar-empresa', [
        'title'         => 'PontoCerto - Editar Empresa',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'empresa'       => $empresa,
        'errors'        => $_SESSION['empresa_erros'] ?? [],
        'old'           => $_SESSION['empresa_data'] ?? []
    ]);
    
    unset($_SESSION['empresa_erros'], $_SESSION['empresa_data']);
}

public function atualizarEmpresa()
{
    error_log("Tentativa de atualização de empresa");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/empresas');
    }

    $empresaId = $_POST['id'];
    $data = $this->validarEmpresa($_POST, true);

    if (isset($data['errors'])) {
        $_SESSION['empresa_erros'] = $data['errors'];
        $_SESSION['empresa_data'] = $_POST;
        $this->redirect('admin/editar-empresa?id=' . $empresaId);
    }

    try {
        $success = $this->empresaModel->atualizarEmpresa($empresaId, $data);
        
        if ($success) {
            $_SESSION['success_message'] = 'Empresa atualizada com sucesso!';
            $this->redirect('admin/visualizar-empresa?id=' . $empresaId);
        } else {
            $_SESSION['error_message'] = 'Erro ao atualizar a empresa';
            $this->redirect('admin/editar-empresa?id=' . $empresaId);
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao atualizar a empresa: ' . $e->getMessage();
        $this->redirect('admin/editar-empresa?id=' . $empresaId);
    }
}

public function removerEmpresa()
{
    error_log("Tentativa de remoção de empresa");
    $empresaId = $_GET['id'];

    if (empty($empresaId) || !is_numeric($empresaId)) {
        $_SESSION['error_message'] = 'ID de empresa inválido';
        $this->redirect('admin/empresas');
    }

    try {
        $success = $this->empresaModel->desativarEmpresa($empresaId);

        if ($success) {
            $_SESSION['success_message'] = 'Empresa removida com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao remover a empresa';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao remover a empresa: ' . $e->getMessage();
    }

    $this->redirect('admin/empresas');
}

public function reativarEmpresa()
{
    error_log("Tentativa de reativação de empresa");
    $empresaId = $_GET['id'];

    if (empty($empresaId) || !is_numeric($empresaId)) {
        $_SESSION['error_message'] = 'ID de empresa inválido';
        $this->redirect('admin/empresas');
    }

    try {
        $success = $this->empresaModel->reativarEmpresa($empresaId);

        if ($success) {
            $_SESSION['success_message'] = 'Empresa reativada com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao reativar a empresa';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao reativar a empresa: ' . $e->getMessage();
    }

    $this->redirect('admin/empresas');
}

// Método de validação para empresa
private function validarEmpresa($post, $isUpdate = false)
{
    $errors = [];
    $data = [
        'nome' => trim($post['nome'] ?? ''),
        'cnpj' => trim($post['cnpj'] ?? ''),
        'email' => trim($post['email'] ?? ''),
        'telefone' => trim($post['telefone'] ?? ''),
        'endereco' => trim($post['endereco'] ?? ''),
        'cidade' => trim($post['cidade'] ?? ''),
        'estado' => trim($post['estado'] ?? ''),
        'cep' => trim($post['cep'] ?? ''),
        'observacao' => trim($post['observacao'] ?? '')
    ];

    // Validações
    if (empty($data['nome'])) {
        $errors['nome'] = 'Nome é obrigatório';
    }

    if (empty($data['cnpj'])) {
        $errors['cnpj'] = 'CNPJ é obrigatório';
    } elseif (!$this->validarCNPJ($data['cnpj'])) {
        $errors['cnpj'] = 'CNPJ inválido';
    } elseif (!$isUpdate && $this->empresaModel->getEmpresaPorCnpj($data['cnpj'])) {
        $errors['cnpj'] = 'CNPJ já cadastrado';
    }

    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email inválido';
    }

    if (empty($data['telefone'])) {
        $errors['telefone'] = 'Telefone é obrigatório';
    }

    // Limpar números
    $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']);
    $data['telefone'] = preg_replace('/\D/', '', $data['telefone']);
    $data['cep'] = preg_replace('/\D/', '', $data['cep']);

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return $data;
}

// Método para validar CNPJ
private function validarCNPJ($cnpj)
{
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    if (strlen($cnpj) != 14) {
        return false;
    }
    
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }
    
    // Validação do primeiro dígito verificador
    $pesos = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $soma = 0;
    for ($i = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $pesos[$i];
    }
    
    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;
    
    if ($cnpj[12] != $digito1) {
        return false;
    }
    
    // Validação do segundo dígito verificador
    $pesos = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $soma = 0;
    for ($i = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $pesos[$i];
    }
    
    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;
    
    if ($cnpj[13] != $digito2) {
        return false;
    }
    
    return true;
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

    // Buscar todos os dados do banco
    $empresas = $this->empresaModel->getEmpresas(1); 
    $tiposPeca = $this->pecaModel->getTiposAtivos();
    $cores = $this->pecaModel->getCoresAtivas();
    $tamanhos = $this->pecaModel->getTamanhosAtivos();
    $operacoes = $this->operacaoModel->getOperacoesAtivas(); 

    $this->render('admin/criar-lote', [
        'title'         => 'PontoCerto - Criar Lote',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'errors'        => $_SESSION['lote_erros'] ?? [],
        'old'           => $_SESSION['lote_data'] ?? [],
        'empresas'      => $empresas, 
        'tiposPeca'     => $tiposPeca,
        'cores'         => $cores,        
        'tamanhos'      => $tamanhos,
        'operacoes'     => $operacoes 
    ]);
    
    unset($_SESSION['lote_erros'], $_SESSION['lote_data']);
}

public function criarLote()
{
    error_log("Tentativa de criação de lote");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/criar-lote');
    }

    // Processar upload de anexo se existir
    $anexoNome = null;
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $anexoNome = $this->processarUploadAnexo($_FILES['anexo']);
        if ($anexoNome) {
            error_log("Anexo processado: " . $anexoNome);
        } else {
            error_log("Falha ao processar anexo");
        }
    } else {
        error_log("Nenhum anexo enviado ou erro no upload: " . ($_FILES['anexo']['error'] ?? 'N/A'));
    }

    $data = $this->validarLoteComPecas($_POST);

    if (isset($data['errors'])) {
        $_SESSION['lote_erros'] = $data['errors'];
        $_SESSION['lote_data'] = $_POST;
        $this->redirect('admin/criar-lote');
    }

    // Adicionar nome do anexo aos dados
    if ($anexoNome) {
        $data['anexos'] = $anexoNome;
        error_log("Anexo adicionado aos dados: " . $anexoNome);
    }

    try {
        $loteId = $this->loteModel->criarLote($data);
        $_SESSION['success_message'] = 'Lote criado com sucesso!';
        $this->redirect('admin/visualizar-lote?id=' . $loteId);
    } catch (Exception $e) {
        error_log("Erro ao criar lote: " . $e->getMessage());
        $_SESSION['lote_erros'] = ['Falha ao criar lote: ' . $e->getMessage()];
        $this->redirect('admin/criar-lote');
    }
}

private function processarUploadAnexo($anexo)
{
    // Verificar se o upload foi bem sucedido
    if ($anexo['error'] !== UPLOAD_ERR_OK) {
        error_log("Erro no upload: " . $anexo['error']);
        return null;
    }

    // Validar tamanho do arquivo (5MB máximo)
    if ($anexo['size'] > 5 * 1024 * 1024) {
        error_log("Arquivo muito grande: " . $anexo['size']);
        return null;
    }

    // Validar tipo de arquivo
    $tiposPermitidos = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    $extensao = strtolower(pathinfo($anexo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extensao, $tiposPermitidos)) {
        error_log("Tipo de arquivo não permitido: " . $extensao);
        return null;
    }

    $diretorioUpload = UPLOADS_PATH . 'lotes/';
    
    // Criar diretório se não existir
    if (!is_dir($diretorioUpload)) {
        mkdir($diretorioUpload, 0755, true);
    }

    // Gerar nome único para o arquivo
    $nomeArquivo = uniqid() . '_' . date('Y-m-d') . '.' . $extensao;
    $caminhoCompleto = $diretorioUpload . $nomeArquivo;

    if (move_uploaded_file($anexo['tmp_name'], $caminhoCompleto)) {
        error_log("Arquivo salvo com sucesso: " . $nomeArquivo);
        return $nomeArquivo;
    } else {
        error_log("Falha ao mover arquivo para: " . $caminhoCompleto);
        return null;
    }
}

private function validarLoteComPecas($post)
{
    $errors = [];
    $data = [
        'empresa_id' => trim($post['empresa_id'] ?? ''),
        'colecao' => trim($post['colecao'] ?? ''),
        'nome' => trim($post['nome'] ?? ''),
        'observacao' => trim($post['observacao'] ?? ''),
        'data_entrada' => trim($post['data_entrada'] ?? ''),
        'data_entrega' => trim($post['data_entrega'] ?? ''),
        'pecas' => []
    ];

    // Validações do lote
    if (empty($data['empresa_id'])) {
        $errors['empresa_id'] = 'ID da empresa é obrigatório';
    }

    if (empty($data['colecao'])) {
        $errors['colecao'] = 'Coleção é obrigatória';
    }

    if (empty($data['nome'])) {
        $errors['nome'] = 'Nome do lote é obrigatório';
    }

    if (empty($data['data_entrada'])) {
        $errors['data_entrada'] = 'Data de entrada é obrigatória';
    }

    if (empty($data['data_entrega'])) {
        $errors['data_entrega'] = 'Data de entrega é obrigatória';
    } elseif ($data['data_entrega'] < $data['data_entrada']) {
        $errors['data_entrega'] = 'Data de entrega não pode ser anterior à data de entrada';
    }

    // Validações das peças
    if (isset($post['pecas']) && is_array($post['pecas'])) {
        foreach ($post['pecas'] as $index => $pecaData) {
            $peca = [
                'tipo_peca_id' => trim($pecaData['tipo_peca_id'] ?? ''),
                'cor_id' => trim($pecaData['cor_id'] ?? ''),
                'tamanho_id' => trim($pecaData['tamanho_id'] ?? ''),
                'operacao_id' => trim($pecaData['operacao_id'] ?? ''),
                'quantidade' => trim($pecaData['quantidade'] ?? ''),
                'valor_unitario' => trim($pecaData['valor_unitario'] ?? '')
            ];

            // Validar cada peça
            if (empty($peca['tipo_peca_id'])) {
                $errors['pecas'][$index]['tipo_peca_id'] = 'Tipo da peça é obrigatório';
            }

            if (empty($peca['cor_id'])) {
                $errors['pecas'][$index]['cor_id'] = 'Cor é obrigatória';
            }

            if (empty($peca['tamanho_id'])) {
                $errors['pecas'][$index]['tamanho_id'] = 'Tamanho é obrigatório';
            }

            if (empty($peca['operacao_id'])) {
                $errors['pecas'][$index]['operacao_id'] = 'Operação é obrigatória';
            }

            if (empty($peca['quantidade']) || !is_numeric($peca['quantidade']) || $peca['quantidade'] <= 0) {
                $errors['pecas'][$index]['quantidade'] = 'Quantidade deve ser um número positivo';
            }

            if (empty($peca['valor_unitario']) || !is_numeric($peca['valor_unitario']) || $peca['valor_unitario'] <= 0) {
                $errors['pecas'][$index]['valor_unitario'] = 'Valor unitário deve ser um número positivo';
            }

            $data['pecas'][] = $peca;
        }
    } else {
        $errors['pecas'] = 'É necessário adicionar pelo menos uma peça ao lote';
    }

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return $data;
}

public function visualizarLote()
{
    error_log("Exibindo detalhes do lote");
    $user = $this->getUsuario();
    $id = $_GET['id'];
    
    // Configuração da paginação
    $itensPorPagina = 10;
    $paginaAtual = $_GET['page'] ?? 1;
    $offset = ($paginaAtual - 1) * $itensPorPagina;
    
    // Busca
    $search = $_GET['search'] ?? '';
    
    $lote = $this->loteModel->getLotePorId($id);
    
    // Buscar peças com paginação
    $pecasData = $this->pecaModel->getPecasPorLoteComPaginacao($id, $itensPorPagina, $offset, $search);
    $pecas = $pecasData['pecas'];
    $totalPecas = $pecasData['total'];
    
    // Calcular totais
    $totalPaginas = ceil($totalPecas / $itensPorPagina);
    $inicio = $offset + 1;
    $fim = min($offset + $itensPorPagina, $totalPecas);
    
    $this->render('admin/visualizar-lote', [
        'title'         => 'PontoCerto - Visualizar Lote',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'lote'          => $lote,
        'pecas'         => $pecas,
        'totalPecas'    => $totalPecas,
        'paginaAtual'   => $paginaAtual,
        'totalPaginas'  => $totalPaginas,
        'inicio'        => $inicio,
        'fim'           => $fim,
        'search'        => $search
    ]);
}

public function atualizarLote()
{
    error_log("Tentativa de atualização de lote");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/lotes');
    }

    $loteId = $_POST['id'];
    
    // Processar upload de anexo se existir
    $anexoNome = null;
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $anexoNome = $this->processarUploadAnexo($_FILES['anexo']);
        if ($anexoNome) {
            error_log("Novo anexo processado: " . $anexoNome);
        }
    } else {
        error_log("Nenhum novo anexo enviado ou erro no upload: " . ($_FILES['anexo']['error'] ?? 'N/A'));
    }

    $data = $this->validarLoteComPecas($_POST);

    if (isset($data['errors'])) {
        $_SESSION['lote_erros'] = $data['errors'];
        $_SESSION['lote_data'] = $_POST;
        $this->redirect('admin/editar-lote?id=' . $loteId);
    }

    // Adicionar nome do anexo aos dados se foi feito upload
    if ($anexoNome) {
        $data['anexos'] = $anexoNome;
    } else {
        // Manter o anexo atual se não foi feito upload de novo
        $loteAtual = $this->loteModel->getLotePorId($loteId);
        $data['anexos'] = $loteAtual['anexos'] ?? null;
    }

    $data['id'] = $loteId;

    try {
        $success = $this->loteModel->atualizarLote($loteId, $data);
        
        if ($success) {
            $_SESSION['success_message'] = 'Lote atualizado com sucesso!';
            $this->redirect('admin/visualizar-lote?id=' . $loteId);
        } else {
            $_SESSION['error_message'] = 'Erro ao atualizar o lote';
            $this->redirect('admin/editar-lote?id=' . $loteId);
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao atualizar o lote: ' . $e->getMessage();
        $this->redirect('admin/editar-lote?id=' . $loteId);
    }
}

    public function operacoes()
    {
        error_log("Exibindo operações");
        $user = $this->getUsuario();
        $filtro = $_GET['filtro'] ?? 'ativos';
        
        $listaOperacoes = $this->operacaoModel->getOperacoes();

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
            $_SESSION['operacao_erros'] = $data['errors'];
            $_SESSION['operacao_data'] = $_POST;
            $this->redirect('admin/criar-operacao');
        }

        try {
            $operacaoId = $this->operacaoModel->criarOperacao($data);
            $_SESSION['success_message'] = 'Operação criada com sucesso!';
            $this->redirect('admin/operacoes');
        } catch (Exception $e) {
            $_SESSION['operacao_erros'] = ['Falha ao criar operação: ' . $e->getMessage()];
            $this->redirect('admin/criar-operacao');
        }
    }

    public function mostrarEditarOperacao()
{
    error_log("Exibindo tela de edição de operação");
    $user = $this->getUsuario();
    $id = $_GET['id'];

    $operacao = $this->operacaoModel->getOperacaoPorId($id);

    $this->render('admin/editar-operacao', [
        'title'         => 'PontoCerto - Editar Operação',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'operacao'      => $operacao,
        'errors'        => $_SESSION['operacao_erros'] ?? [],
        'old'           => $_SESSION['operacao_data'] ?? []
    ]);
    
    unset($_SESSION['operacao_erros'], $_SESSION['operacao_data']);
}

public function atualizarOperacao()
{
    error_log("Tentativa de atualização de operação");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/operacoes');
    }

    $operacaoId = $_POST['id'];
    $data = $this->validarOperacao($_POST);

    if (isset($data['errors'])) {
        $_SESSION['operacao_erros'] = $data['errors'];
        $_SESSION['operacao_data'] = $_POST;
        $this->redirect('admin/editar-operacao?id=' . $operacaoId);
    }

    try {
        $success = $this->operacaoModel->atualizarOperacao($operacaoId, $data);
        
        if ($success) {
            $_SESSION['success_message'] = 'Operação atualizada com sucesso!';
            $this->redirect('admin/operacoes');
        } else {
            $_SESSION['error_message'] = 'Erro ao atualizar a operação';
            $this->redirect('admin/editar-operacao?id=' . $operacaoId);
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao atualizar a operação: ' . $e->getMessage();
        $this->redirect('admin/editar-operacao?id=' . $operacaoId);
    }
}

public function editarLote()
{
    error_log("Editando lote");
    $user = $this->getUsuario();
    $loteId = $_GET['id'];
    
    $lote = $this->loteModel->getLotePorId($loteId);
    $empresas = $this->empresaModel->getEmpresas(1);
    $tiposPeca = $this->pecaModel->getTiposAtivos();
    $cores = $this->pecaModel->getCoresAtivas();
    $tamanhos = $this->pecaModel->getTamanhosAtivos();
    $operacoes = $this->operacaoModel->getOperacoesAtivas();
    
    // Buscar peças existentes do lote
    $pecasExistentes = $this->pecaModel->getPecasPorLote($loteId);

    $this->render('admin/editar-lote', [
        'title'         => 'PontoCerto - Editar Lote',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'lote'          => $lote,
        'empresas'      => $empresas,
        'tiposPeca'     => $tiposPeca,
        'cores'         => $cores,
        'tamanhos'      => $tamanhos,
        'operacoes'     => $operacoes,
        'pecasExistentes' => $pecasExistentes, 
        'errors'        => $_SESSION['lote_erros'] ?? [],
        'old'           => $_SESSION['lote_data'] ?? []
    ]);
    
    unset($_SESSION['lote_erros'], $_SESSION['lote_data']);
}

    public function criarPeca()
    {
        error_log("Tentativa de criação de peça");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/lotes');
        }

        $data = $this->validarPeca($_POST);

        if (isset($data['errors'])) {
            $_SESSION['peca_erros'] = $data['errors'];
            $_SESSION['peca_data'] = $_POST;
            $this->redirect('admin/adicionar-peca?lote_id=' . $data['lote_id']);
        }

        try {
            $pecaId = $this->pecaModel->criarPeca($data);
            $_SESSION['success_message'] = 'Peça adicionada com sucesso!';
            $this->redirect('admin/visualizar-lote?id=' . $data['lote_id']);
        } catch (Exception $e) {
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
            $_SESSION['error_message'] = 'Erro ao remover a operação: ' . $e->getMessage();
        }

        $this->redirect('admin/operacoes');
    }

    public function reativarOperacao()
    {
        error_log("Tentativa de reativação de operação");
        $operacaoId = $_GET['id'];

        if (empty($operacaoId) || !is_numeric($operacaoId)) {
            $_SESSION['error_message'] = 'ID de operação inválido';
            $this->redirect('admin/operacoes');
        }

        try {
            $success = $this->operacaoModel->reativarOperacao($operacaoId);

            if ($success) {
                $_SESSION['success_message'] = 'Operação reativada com sucesso!';
            } else {
                $_SESSION['error_message'] = 'Erro ao reativar a operação';
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erro ao reativar a operação: ' . $e->getMessage();
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
            'valor' => trim($post['valor'] ?? ''),
        ];

        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }
        
        $data['valor'] = str_replace(['.', ','], ['', '.'], $data['valor']);
        if (empty($data['valor']) || !is_numeric($data['valor']) || $data['valor'] <= 0) {
            $errors['valor'] = 'Valor deve ser um número positivo';
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

// Listar serviços
public function servicos()
{
    error_log("Exibindo servicos");
    $user = $this->getUsuario();
    $filtro = $_GET['filtro'] ?? 'ativos';
    $termoBusca = $_GET['search'] ?? '';
    
    if (!empty($termoBusca)) {
        $listaServicos = $this->servicoModel->buscarServicos($termoBusca);
    } else {
        // Se for "todos", não aplicar filtro de status
        if ($filtro === 'todos') {
            $listaServicos = $this->servicoModel->getServicos();
        } else {
            $listaServicos = $this->servicoModel->getServicos($filtro);
        }
    }

    $this->render('admin/servicos', [
        'title'         => 'PontoCerto - Serviços',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'listaServicos' => $listaServicos,
        'filtro'        => $filtro,
        'termoBusca'    => $termoBusca
    ]);
}

// Mostrar formulário de criação de serviço
public function mostrarCriarServico()
{
    error_log("Exibindo tela de criacao de servico");
    $user = $this->getUsuario();

    // Buscar dados necessários
    $lotes = $this->servicoModel->getLotesAtivos(); 
    $costureiras = $this->servicoModel->getCostureirasDisponiveis();
    $operacoes = $this->operacaoModel->getOperacoesAtivas();

    $this->render('admin/criar-servico', [
        'title'         => 'PontoCerto - Criar Serviço',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'lotes'         => $lotes,
        'costureiras'   => $costureiras,
        'operacoes'     => $operacoes,
        'errors'        => $_SESSION['servico_erros'] ?? [],
        'old'           => $_SESSION['servico_data'] ?? []
    ]);
    
    unset($_SESSION['servico_erros'], $_SESSION['servico_data']);
}

// Criar serviço
public function criarServico()
{
    error_log("Tentativa de criacao de servico");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/criar-servico');
    }

    $data = $this->validarServico($_POST);

    if (isset($data['errors'])) {
        $_SESSION['servico_erros'] = $data['errors'];
        $_SESSION['servico_data'] = $_POST;
        $this->redirect('admin/criar-servico');
    }

    try {
        $servicoId = $this->servicoModel->criarServico($data);
        $_SESSION['success_message'] = 'Serviço criado com sucesso!';
        $this->redirect('admin/visualizar-servico?id=' . $servicoId);
    } catch (Exception $e) {
        $_SESSION['servico_erros'] = ['Falha ao criar serviço: ' . $e->getMessage()];
        $this->redirect('admin/criar-servico');
    }
}

// Visualizar serviço
public function visualizarServico()
{
    error_log("Exibindo detalhes do servico");
    $user = $this->getUsuario();
    $id = $_GET['id'];
    
    $servico = $this->servicoModel->getServicoPorId($id);
    
    if (!$servico) {
        $_SESSION['error_message'] = 'Servico não encontrado';
        $this->redirect('admin/servicos');
    }

    $costureiras = $this->servicoModel->getCostureirasAtivas(); 

    $this->render('admin/visualizar-servico', [
        'title'         => 'PontoCerto - Visualizar Serviço',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'servico'       => $servico,
        'costureiras'   => $costureiras 
    ]);
}

// Mostrar editar serviço
public function mostrarEditarServico()
{
    error_log("Exibindo tela de edicao de servico");
    $user = $this->getUsuario();
    $id = $_GET['id'];

    $servico = $this->servicoModel->getServicoPorId($id);
    $lotes = $this->servicoModel->getLotesAtivos();
    $operacoes = $this->servicoModel->getOperacoesAtivas();
    $costureiras = $this->servicoModel->getCostureirasAtivas(); 

    $this->render('admin/editar-servico', [
        'title'         => 'PontoCerto - Editar Serviço',
        'user'          => $user,
        'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->estaLogado(),
        'servico'       => $servico,
        'lotes'         => $lotes,
        'operacoes'     => $operacoes,
        'costureiras'   => $costureiras, 
        'errors'        => $_SESSION['servico_erros'] ?? [],
        'old'           => $_SESSION['servico_data'] ?? []
    ]);
    
    unset($_SESSION['servico_erros'], $_SESSION['servico_data']);
}

// Atualizar serviço
public function atualizarServico()
{
    error_log("Tentativa de atualizacao de servico");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/servicos');
    }

    $servicoId = $_POST['id'];
    
    // Passar true como segundo parâmetro para indicar que é uma atualização
    $data = $this->validarServico($_POST, true);

    if (isset($data['errors'])) {
        $_SESSION['servico_erros'] = $data['errors'];
        $_SESSION['servico_data'] = $_POST;
        $this->redirect('admin/editar-servico?id=' . $servicoId);
    }

    try {
        $success = $this->servicoModel->atualizarServico($servicoId, $data);
        
        if ($success) {
            $_SESSION['success_message'] = 'Serviço atualizado com sucesso!';
            $this->redirect('admin/visualizar-servico?id=' . $servicoId);
        } else {
            $_SESSION['error_message'] = 'Erro ao atualizar o serviço';
            $this->redirect('admin/editar-servico?id=' . $servicoId);
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao atualizar o serviço: ' . $e->getMessage();
        $this->redirect('admin/editar-servico?id=' . $servicoId);
    }
}

// Desvincular costureira
public function desvincularCostureira()
{
    $servicoId = $_GET['servico_id'];
    
    try {
        $success = $this->servicoModel->desvincularCostureira($servicoId);
        
        if ($success) {
            $_SESSION['success_message'] = 'Costureira desvinculada com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao desvincular costureira';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao desvincular costureira: ' . $e->getMessage();
    }

    $this->redirect('admin/visualizar-servico?id=' . $servicoId);
}

// Vincular costureira a serviço
public function vincularCostureira()
{
    // Este método não é mais necessário pois a costureira é definida na criação/edição
    $_SESSION['error_message'] = 'Método não disponível. A costureira deve ser selecionada ao criar/editar o serviço.';
    $this->redirect('admin/servicos');
}

// Finalizar serviço
public function finalizarServico()
{
    error_log("Finalizando servico");
    $servicoId = $_GET['id'];
    $dataFinalizacao = $_POST['data_finalizacao'] ?? date('Y-m-d');

    try {
        $success = $this->servicoModel->finalizarServico($servicoId, $dataFinalizacao);
        
        if ($success) {
            $_SESSION['success_message'] = 'Serviço finalizado com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao finalizar serviço';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao finalizar serviço: ' . $e->getMessage();
    }

    $this->redirect('admin/visualizar-servico?id=' . $servicoId);
}

// Remover serviço
public function removerServico()
{
    error_log("Tentativa de remoção de serviço");
    $servicoId = $_GET['id'];

    if (empty($servicoId) || !is_numeric($servicoId)) {
        $_SESSION['error_message'] = 'ID de serviço inválido';
        $this->redirect('admin/servicos');
    }

    try {
        $success = $this->servicoModel->desativarServico($servicoId);

        if ($success) {
            $_SESSION['success_message'] = 'Serviço removido com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao remover o serviço';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao remover o serviço: ' . $e->getMessage();
    }

    $this->redirect('admin/servicos');
}

// Validação de serviço
private function validarServico($post, $isUpdate = false)
{
    $errors = [];
    $data = [
        'lote_id' => trim($post['lote_id'] ?? ''),
        'operacao_id' => trim($post['operacao_id'] ?? ''),
        'quantidade_pecas' => trim($post['quantidade_pecas'] ?? ''),
        'valor_operacao' => trim($post['valor_operacao'] ?? ''),
        'data_envio' => trim($post['data_envio'] ?? ''),
        'observacao' => trim($post['observacao'] ?? ''),
        'costureira_id' => trim($post['costureira_id'] ?? '')
    ];

    // Validações
    if (empty($data['lote_id']) || !is_numeric($data['lote_id'])) {
        $errors['lote_id'] = 'Lote é obrigatório';
    }

    if (empty($data['operacao_id']) || !is_numeric($data['operacao_id'])) {
        $errors['operacao_id'] = 'Operação é obrigatória';
    }

    if (empty($data['quantidade_pecas']) || !is_numeric($data['quantidade_pecas']) || $data['quantidade_pecas'] <= 0) {
        $errors['quantidade_pecas'] = 'Quantidade de peças deve ser um número positivo';
    }

    $valor = str_replace(['.', ','], ['', '.'], $data['valor_operacao']);
    if (empty($valor) || !is_numeric($valor) || $valor <= 0) {
        $errors['valor_operacao'] = 'Valor da operação deve ser um número positivo';
    } else {
        $data['valor_operacao'] = $valor;
    }

    if (empty($data['data_envio'])) {
        $errors['data_envio'] = 'Data de envio é obrigatória';
    }

    if (empty($data['costureira_id']) || !is_numeric($data['costureira_id'])) {
        $errors['costureira_id'] = 'Costureira é obrigatória';
    }

    // Verificar se já existe serviço do mesmo tipo no lote (excluindo o próprio serviço em caso de atualização)
    if (empty($errors)) {
        $servicoId = $isUpdate ? ($post['id'] ?? null) : null;
        if ($this->servicoModel->servicoDoMesmoTipoExiste($data['lote_id'], $data['operacao_id'], $servicoId)) {
            $errors['operacao_id'] = 'Já existe um serviço deste tipo no lote selecionado';
        }
    }

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return $data;
}

public function pecas()
{
    error_log("Exibindo peças");
    $user = $this->getUsuario();
    
    // Buscar todos os dados
    $tiposPeca = $this->pecaModel->getTiposAtivos();
    $cores = $this->pecaModel->getCoresAtivas();
    $tamanhos = $this->pecaModel->getTamanhosAtivos();

    $this->render('admin/pecas', [
        'title'           => 'PontoCerto - Peças',
        'user'            => $user,
        'nomeUsuario'     => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado'   => $this->estaLogado(),
        'tiposPeca'       => $tiposPeca,
        'cores'           => $cores,
        'tamanhos'        => $tamanhos,
        'errors'          => $_SESSION['pecas_erros'] ?? [],
        'old'             => $_SESSION['pecas_data'] ?? []
    ]);
    
    unset($_SESSION['pecas_erros'], $_SESSION['pecas_data']);
}

public function criarTipoPeca()
{
    error_log("Tentativa de criação de tipo de peça");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/pecas');
    }

    $data = $this->validarTipoPeca($_POST);

    if (isset($data['errors'])) {
        $_SESSION['pecas_erros']['tipo'] = $data['errors'];
        $_SESSION['pecas_data']['tipo'] = $_POST;
        $this->redirect('admin/pecas');
    }

    try {
        $tipoId = $this->pecaModel->criarTipo($data);
        $_SESSION['success_message'] = 'Tipo de peça criado com sucesso!';
        $this->redirect('admin/pecas');
    } catch (Exception $e) {
        $_SESSION['pecas_erros']['tipo'] = ['Falha ao criar tipo de peça: ' . $e->getMessage()];
        $this->redirect('admin/pecas');
    }
}

public function criarCor()
{
    error_log("Tentativa de criação de cor");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/pecas');
    }

    $data = $this->validarCor($_POST);

    if (isset($data['errors'])) {
        $_SESSION['pecas_erros']['cor'] = $data['errors'];
        $_SESSION['pecas_data']['cor'] = $_POST;
        $this->redirect('admin/pecas');
    }

    try {
        $corId = $this->pecaModel->criarCor($data);
        $_SESSION['success_message'] = 'Cor criada com sucesso!';
        $this->redirect('admin/pecas');
    } catch (Exception $e) {
        $_SESSION['pecas_erros']['cor'] = ['Falha ao criar cor: ' . $e->getMessage()];
        $this->redirect('admin/pecas');
    }
}

public function criarTamanho()
{
    error_log("Tentativa de criação de tamanho");
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('admin/pecas');
    }

    $data = $this->validarTamanho($_POST);

    if (isset($data['errors'])) {
        $_SESSION['pecas_erros']['tamanho'] = $data['errors'];
        $_SESSION['pecas_data']['tamanho'] = $_POST;
        $this->redirect('admin/pecas');
    }

    try {
        $tamanhoId = $this->pecaModel->criarTamanho($data);
        $_SESSION['success_message'] = 'Tamanho criado com sucesso!';
        $this->redirect('admin/pecas');
    } catch (Exception $e) {
        $_SESSION['pecas_erros']['tamanho'] = ['Falha ao criar tamanho: ' . $e->getMessage()];
        $this->redirect('admin/pecas');
    }
}

public function removerTipoPeca()
{
    error_log("Tentativa de remoção de tipo de peça");
    $tipoId = $_GET['id'];

    if (empty($tipoId) || !is_numeric($tipoId)) {
        $_SESSION['error_message'] = 'ID de tipo de peça inválido';
        $this->redirect('admin/pecas');
    }

    try {
        $success = $this->pecaModel->desativarTipo($tipoId);

        if ($success) {
            $_SESSION['success_message'] = 'Tipo de peça removido com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao remover o tipo de peça';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao remover o tipo de peça: ' . $e->getMessage();
    }

    $this->redirect('admin/pecas');
}

public function removerCor()
{
    error_log("Tentativa de remoção de cor");
    $corId = $_GET['id'];

    if (empty($corId) || !is_numeric($corId)) {
        $_SESSION['error_message'] = 'ID de cor inválido';
        $this->redirect('admin/pecas');
    }

    try {
        $success = $this->pecaModel->desativarCor($corId);

        if ($success) {
            $_SESSION['success_message'] = 'Cor removida com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao remover a cor';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao remover a cor: ' . $e->getMessage();
    }

    $this->redirect('admin/pecas');
}

public function removerTamanho()
{
    error_log("Tentativa de remoção de tamanho");
    $tamanhoId = $_GET['id'];

    if (empty($tamanhoId) || !is_numeric($tamanhoId)) {
        $_SESSION['error_message'] = 'ID de tamanho inválido';
        $this->redirect('admin/pecas');
    }

    try {
        $success = $this->pecaModel->desativarTamanho($tamanhoId);

        if ($success) {
            $_SESSION['success_message'] = 'Tamanho removido com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao remover o tamanho';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erro ao remover o tamanho: ' . $e->getMessage();
    }

    $this->redirect('admin/pecas');
}

// Métodos de validação
private function validarTipoPeca($post)
{
    $errors = [];
    $data = [
        'nome' => trim($post['nome'] ?? ''),
        'descricao' => trim($post['descricao'] ?? '')
    ];

    if (empty($data['nome'])) {
        $errors['nome'] = 'Nome é obrigatório';
    }

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return $data;
}

private function validarCor($post)
{
    $errors = [];
    $data = [
        'nome' => trim($post['nome'] ?? ''),
        'codigo_hex' => trim($post['codigo_hex'] ?? '')
    ];

    if (empty($data['nome'])) {
        $errors['nome'] = 'Nome é obrigatório';
    }

    if (empty($data['codigo_hex'])) {
        $errors['codigo_hex'] = 'Código hexadecimal é obrigatório';
    } elseif (!preg_match('/^#[0-9A-F]{6}$/i', $data['codigo_hex'])) {
        $errors['codigo_hex'] = 'Código hexadecimal inválido (formato: #FFFFFF)';
    }

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return $data;
}

private function validarTamanho($post)
{
    $errors = [];
    $data = [
        'nome' => trim($post['nome'] ?? ''),
        'ordem' => trim($post['ordem'] ?? '')
    ];

    if (empty($data['nome'])) {
        $errors['nome'] = 'Nome é obrigatório';
    }

    if (empty($data['ordem']) || !is_numeric($data['ordem'])) {
        $errors['ordem'] = 'Ordem deve ser um número';
    }

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return $data;
}



}



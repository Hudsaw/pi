<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\AdminModel;
use App\Models\EmpresaModel;
use App\Models\LoteModel;
use App\Models\OperacaoModel;
use App\Models\PecaModel;
use App\Models\ServicoModel;
use App\Models\UserModel;

class BaseController
{
    protected $adminModel;
    protected $empresaModel;
    protected $loteModel;
    protected $operacaoModel;
    protected $pecaModel;
    protected $servicoModel;
    protected $userModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel(Database::getInstance());
        $this->empresaModel = new EmpresaModel(Database::getInstance());
        $this->loteModel = new LoteModel(Database::getInstance());
        $this->operacaoModel = new OperacaoModel(Database::getInstance());
        $this->pecaModel = new PecaModel(Database::getInstance());
        $this->servicoModel = new ServicoModel(Database::getInstance());
        $this->userModel = new UserModel(Database::getInstance());
    }
    
    protected function render($view, $data = [])
    {
        $data['BASE_URL'] = BASE_URL;
        
        // Extrair o nome da página atual do caminho da view
        $viewParts = explode('/', $view);
        $data['paginaAtual'] = end($viewParts);
        
        extract($data);
        
        $headerData = $this->header();
        extract($headerData);
        require VIEWS_PATH . 'shared/header.php';
        require VIEWS_PATH . $view . '.php';
        require VIEWS_PATH . 'shared/footer.php';
    }

    protected function redirect($url)
    {
        $baseUrl = rtrim(BASE_URL, '/') . '/';
        $path    = ltrim($url, '/');
        header("Location: " . $baseUrl . $path);
        exit();
    }

    
    protected function header()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return [
            'usuarioLogado' => isset($_SESSION['user_id']),
            'nomeUsuario' => $_SESSION['usuario_nome'] ?? 'Visitante',
            'tipoUsuario' => $_SESSION['user_role'] ?? null,
            'BASE_URL' => BASE_URL,
            'dashboardLink' => $this->getDashboardLink(),
        ];
    }
    
    protected function getDashboardLink(): string
{
    if (!isset($_SESSION['user_role'])) {
        return BASE_URL;
    }

    return match ($_SESSION['user_role']) {
        'admin' => BASE_URL . 'admin/painel', 
        'costura' => BASE_URL . 'costura/painel', 
        default => BASE_URL
    };
}
    
    protected function estaLogado()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $logado = isset($_SESSION['user_id']);
    error_log("Verificando login - User ID: " . ($_SESSION['user_id'] ?? 'null') . ", Logado: " . ($logado ? 'sim' : 'não'));
    
    return $logado;
    }

    protected function getUsuario()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id'])) {
        return $this->userModel->getUserPeloId($_SESSION['user_id']);
    }
    
    return null;
}

// Adicionar no BaseController
protected function formatarCNPJ($cnpj)
{
    $cnpj = preg_replace('/\D/', '', $cnpj);
    if (strlen($cnpj) === 14) {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
    return $cnpj;
}

protected function formatarTelefone($telefone)
{
    $telefone = preg_replace('/\D/', '', $telefone);
    if (strlen($telefone) === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    } elseif (strlen($telefone) === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    }
    return $telefone;
}

protected function formatarCEP($cep)
{
    $cep = preg_replace('/\D/', '', $cep);
    if (strlen($cep) === 8) {
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
    }
    return $cep;
}
}
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

    // Meus Serviços
    public function servicos()
    {
        $user = $this->getUsuario();

        $servicosAtivos = $this->servicoModel->getServicosAtivosPorCostureira($user['id']);
        $servicosFinalizados = $this->servicoModel->getServicosFinalizadosPorCostureira($user['id']);

        $this->render('costura/servicos', [
            'title' => 'PontoCerto - Meus Serviços',
            'user' => $user,
            'nomeUsuario' => $user['nome'],
            'usuarioLogado' => true,
            'servicosAtivos' => $servicosAtivos,
            'servicosFinalizados' => $servicosFinalizados
        ]);
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
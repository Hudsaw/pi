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
        error_log("Exibindo painel");
        $user  = $this->getUsuario();

        $this->render('painel', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
        ]);
    }
}
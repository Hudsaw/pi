<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\UserModel;
require_once __DIR__ . '/BaseController.php';


class PageController extends BaseController
{
    public function home()
    {
        error_log("Exibindo pagina inicial");
        $user = $this->getUsuario();
        $especialidade = $this->userModel->getEspecialidade();

        $this->render('shared/home', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'dados'         => [
                'titulo'    => 'Centralize, organize e produza com precisão',
                'descricao' => 'O PontoCerto é um sistema de gerenciamento desenvolvido
                        especialmente para malharias.',
            ],
        ]);
    }

    public function politica()
    {
        $this->render('auth/politica', [
            'title' => 'Política de Privacidade',
            'usuarioLogado' => $this->estaLogado(),
            'nomeUsuario' => $_SESSION['usuario_nome'] ?? 'Visitante',
        ]);
    }

    public function termos()
    {
        $this->render('auth/termos', [
            'title' => 'Termos de Uso',
            'usuarioLogado' => $this->estaLogado(),
            'nomeUsuario' => $_SESSION['usuario_nome'] ?? 'Visitante',
        ]);
    }

    // Métodos auxiliares
    protected function getUsuario()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return $this->userModel->getUserPeloId($_SESSION['user_id']);
    }
}
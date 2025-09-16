<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class AdminModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUsuariosData($tipoFiltro, int $paginaAtual = 1, int $itensPorPagina = 10): array
    {
        $termo = $_GET['search'] ?? null;

        try {
            if ($tipoFiltro == 'medico') {
                $especialidades = $this->getUserModel()->getEspecialidades();
            } else {
                $especialidades = $this->getTiposExameModel()->getTiposExameAtivos();
            }
        } catch (PDOException $e) {
            $especialidades = [];
            error_log("Erro ao buscar especialidades: " . $e->getMessage());
        }

        if ($termo) {
            $usuarios = $this->getUserModel()->buscarUsuarios($termo, $tipoFiltro);
            $totalUsuarios = count($usuarios);

            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode($usuarios);
                exit();
            }
        } else {
            $usuarios = $this->getUserModel()->gerenciarUsuarios(
                $itensPorPagina,
                ($paginaAtual - 1) * $itensPorPagina,
                $tipoFiltro,
                false
            );
            $totalUsuarios = $this->getUserModel()->getTotalUsers($tipoFiltro);
        }

        return [
            'usuarios' => $usuarios,
            'totalUsuarios' => $totalUsuarios,
            'especialidades' => $especialidades,
            'paginaAtual' => $paginaAtual,
            'itensPorPagina' => $itensPorPagina,
            'tipoFiltro' => $tipoFiltro
        ];
    }

}
<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class PageModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getEspecialidades(): array
    {
        return $this->pdo->query("SELECT * FROM especialidade")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNivel(): array
    {
        return $this->pdo->query("SELECT * FROM nivel")->fetchAll(PDO::FETCH_ASSOC);
    }
}

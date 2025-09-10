<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class CosturaModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

}
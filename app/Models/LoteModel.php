<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class LoteModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function criarLote($dados)
    {
        $sql = "INSERT INTO lotes (empresa_id, descricao, quantidade, valor, data_inicio, data_prazo, ativo) 
                VALUES (:empresa_id, :descricao, :quantidade, :valor, :data_inicio, :data_prazo, 1)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':empresa_id' => $dados['empresa_id'],
            ':descricao' => $dados['descricao'],
            ':quantidade' => $dados['quantidade'],
            ':valor' => $dados['valor'],
            ':data_inicio' => $dados['data_inicio'],
            ':data_prazo' => $dados['data_prazo']
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getLotes($filtro = 'ativos')
    {
        $where = '';
        if ($filtro === 'ativos') {
            $where = 'WHERE ativo = 1';
        } elseif ($filtro === 'inativos') {
            $where = 'WHERE ativo = 0';
        }

        $sql = "SELECT * FROM lotes $where ORDER BY data_inicio DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLotePorId($id)
    {
        $sql = "SELECT * FROM lotes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarLote($id, $dados)
    {
        $sql = "UPDATE lotes SET 
                descricao = :descricao, 
                quantidade = :quantidade, 
                valor = :valor, 
                data_inicio = :data_inicio, 
                data_prazo = :data_prazo 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':descricao' => $dados['descricao'],
            ':quantidade' => $dados['quantidade'],
            ':valor' => $dados['valor'],
            ':data_inicio' => $dados['data_inicio'],
            ':data_prazo' => $dados['data_prazo'],
            ':id' => $id
        ]);
    }

    public function desativarLote($id)
    {
        $sql = "UPDATE lotes SET ativo = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function verificarLoteAberto($id)
    {
        $sql = "SELECT ativo FROM lotes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $lote = $stmt->fetch(PDO::FETCH_ASSOC);

        return $lote && $lote['ativo'] == 1;
    }
}
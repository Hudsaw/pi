<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class OperacaoModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function criarOperacao($dados)
    {
        $sql = "INSERT INTO operacoes (nome, valor, ativo) 
                VALUES (:nome, :valor, 1)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':valor' => $dados['valor']
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getOperacoes()
    {
        $sql = "SELECT * FROM operacoes ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOperacoesAtivas()
{
    try {
        $sql = "SELECT * FROM operacoes WHERE ativo = 1 ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar operações ativas: " . $e->getMessage());
        return [];
    }
}

/**
 * Obter operação por ID
 */
public function getOperacaoPorId($id)
{
    try {
        $sql = "SELECT * FROM operacoes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar operação por ID: " . $e->getMessage());
        return null;
    }
}

    public function atualizarOperacao($id, $dados)
    {
        $sql = "UPDATE operacoes SET 
                nome = :nome,  
                valor = :valor  
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $dados['nome'],
            ':valor' => $dados['valor'],
            ':id' => $id
        ]);
    }

    public function desativarOperacao($id)
    {
        $sql = "UPDATE operacoes SET ativo = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function reativarOperacao($id)
    {
        $sql = "UPDATE operacoes SET ativo = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
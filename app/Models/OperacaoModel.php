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
        $sql = "INSERT INTO operacoes (nome, descricao, valor, tempo_estimado, ativo) 
                VALUES (:nome, :descricao, :valor, :tempo_estimado, 1)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao'],
            ':valor' => $dados['valor'],
            ':tempo_estimado' => $dados['tempo_estimado']
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

    public function getOperacaoPorId($id)
    {
        $sql = "SELECT * FROM operacoes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarOperacao($id, $dados)
    {
        $sql = "UPDATE operacoes SET 
                nome = :nome, 
                descricao = :descricao, 
                valor = :valor, 
                tempo_estimado = :tempo_estimado 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao'],
            ':valor' => $dados['valor'],
            ':tempo_estimado' => $dados['tempo_estimado'],
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
<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class PecaModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function criarPeca($dados)
    {
        // Verificar se o lote está aberto antes de adicionar peças
        $loteModel = new LoteModel($this->pdo);
        if (!$loteModel->verificarLoteAberto($dados['lote_id'])) {
            throw new Exception("Não é possível adicionar peças a um lote fechado");
        }

        $sql = "INSERT INTO pecas (lote_id, operacao_id, quantidade, valor_unitario, observacao) 
                VALUES (:lote_id, :operacao_id, :quantidade, :valor_unitario, :observacao)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':lote_id' => $dados['lote_id'],
            ':operacao_id' => $dados['operacao_id'],
            ':quantidade' => $dados['quantidade'],
            ':valor_unitario' => $dados['valor_unitario'],
            ':observacao' => $dados['observacao'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getPecasPorLote($loteId)
    {
        $sql = "SELECT p.*, o.nome as operacao_nome 
                FROM pecas p 
                INNER JOIN operacoes o ON p.operacao_id = o.id 
                WHERE p.lote_id = :lote_id 
                ORDER BY p.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':lote_id' => $loteId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPecaPorId($id)
    {
        $sql = "SELECT p.*, o.nome as operacao_nome, l.descricao as lote_descricao 
                FROM pecas p 
                INNER JOIN operacoes o ON p.operacao_id = o.id 
                INNER JOIN lotes l ON p.lote_id = l.id 
                WHERE p.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarPeca($id, $dados)
    {
        // Verificar se o lote está aberto antes de atualizar peças
        $peca = $this->getPecaPorId($id);
        $loteModel = new LoteModel($this->pdo);
        
        if (!$loteModel->verificarLoteAberto($peca['lote_id'])) {
            throw new Exception("Não é possível alterar peças de um lote fechado");
        }

        $sql = "UPDATE pecas SET 
                operacao_id = :operacao_id, 
                quantidade = :quantidade, 
                valor_unitario = :valor_unitario, 
                observacao = :observacao 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':operacao_id' => $dados['operacao_id'],
            ':quantidade' => $dados['quantidade'],
            ':valor_unitario' => $dados['valor_unitario'],
            ':observacao' => $dados['observacao'] ?? null,
            ':id' => $id
        ]);
    }

    public function removerPeca($id)
    {
        // Verificar se o lote está aberto antes de remover peças
        $peca = $this->getPecaPorId($id);
        $loteModel = new LoteModel($this->pdo);
        
        if (!$loteModel->verificarLoteAberto($peca['lote_id'])) {
            throw new Exception("Não é possível remover peças de um lote fechado");
        }

        $sql = "DELETE FROM pecas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
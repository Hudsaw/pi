<?php
namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class LoteModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function criarLote($dados)
    {
        // Iniciar transação para garantir consistência
        $this->pdo->beginTransaction();
        
        try {
            // 1. Criar o lote
            $sql = "INSERT INTO lotes (empresa_id, colecao, nome, observacao, data_entrada, data_entrega, valor_total, status, anexos) 
                    VALUES (:empresa_id, :colecao, :nome, :observacao, :data_entrada, :data_entrega, 0, 'Aberto', :anexos)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':empresa_id' => $dados['empresa_id'],
                ':colecao' => $dados['colecao'],
                ':nome' => $dados['nome'],
                ':observacao' => $dados['observacao'],
                ':data_entrada' => $dados['data_entrada'],
                ':data_entrega' => $dados['data_entrega'],
                ':anexos' => $dados['anexos'] ?? null
            ]);

            $loteId = $this->pdo->lastInsertId();

            // 2. Inserir peças vinculadas ao lote
            $this->criarPecasParaLote($loteId, $dados['pecas']);

            // 3. Calcular e atualizar valor total
            $this->atualizarValorTotalLote($loteId);

            $this->pdo->commit();
            return $loteId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function atualizarLote($loteId, $dados)
{
    // Iniciar transação para garantir consistência
    $this->pdo->beginTransaction();
    
    try {
        // 1. Atualizar o lote
        $sql = "UPDATE lotes SET 
                empresa_id = :empresa_id, 
                colecao = :colecao, 
                nome = :nome, 
                observacao = :observacao, 
                data_entrada = :data_entrada, 
                data_entrega = :data_entrega, 
                anexos = :anexos 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':empresa_id' => $dados['empresa_id'],
            ':colecao' => $dados['colecao'],
            ':nome' => $dados['nome'],
            ':observacao' => $dados['observacao'],
            ':data_entrada' => $dados['data_entrada'],
            ':data_entrega' => $dados['data_entrega'],
            ':anexos' => $dados['anexos'] ?? null,
            ':id' => $loteId
        ]);

        // 2. Remover peças existentes
        $this->removerPecasLote($loteId);

        // 3. Inserir novas peças vinculadas ao lote
        if (!empty($dados['pecas'])) {
            $this->criarPecasParaLote($loteId, $dados['pecas']);
        }

        // 4. Calcular e atualizar valor total
        $this->atualizarValorTotalLote($loteId);

        $this->pdo->commit();
        return true;

    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}

private function criarPecasParaLote($loteId, $pecas)
    {
        $sql = "INSERT INTO pecas (lote_id, tipo_peca_id, cor_id, tamanho_id, operacao_id, quantidade, valor_unitario) 
                VALUES (:lote_id, :tipo_peca_id, :cor_id, :tamanho_id, :operacao_id, :quantidade, :valor_unitario)";
        
        $stmt = $this->pdo->prepare($sql);

        foreach ($pecas as $peca) {
            $stmt->execute([
                ':lote_id' => $loteId,
                ':tipo_peca_id' => $peca['tipo_peca_id'],
                ':cor_id' => $peca['cor_id'],
                ':tamanho_id' => $peca['tamanho_id'],
                ':operacao_id' => $peca['operacao_id'],
                ':quantidade' => $peca['quantidade'],
                ':valor_unitario' => $peca['valor_unitario']
            ]);
        }
    }

    private function atualizarValorTotalLote($loteId)
    {
        $sql = "UPDATE lotes SET valor_total = (
                    SELECT COALESCE(SUM(quantidade * valor_unitario), 0) 
                    FROM pecas 
                    WHERE lote_id = :lote_id
                ) WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':lote_id' => $loteId, ':id' => $loteId]);
    }

    public function getLotePorId($id)
    {
        $sql = "SELECT l.*, e.nome as empresa_nome 
                FROM lotes l 
                LEFT JOIN empresas e ON l.empresa_id = e.id 
                WHERE l.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    

    private function removerPecasLote($loteId)
    {
        $sql = "DELETE FROM pecas WHERE lote_id = :lote_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':lote_id' => $loteId]);
    }

    public function getLotes()
    {
        $sql = "SELECT l.*, e.nome as empresa_nome 
                FROM lotes l 
                LEFT JOIN empresas e ON l.empresa_id = e.id 
                ORDER BY l.data_entrada DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLotesAtivos()
    {
        $sql = "SELECT * FROM lotes WHERE status = 'Aberto' AND ativo = 1 ORDER BY data_entrada DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarLotes($termo)
    {
        $sql = "SELECT l.*, e.nome as empresa_nome 
                FROM lotes l 
                LEFT JOIN empresas e ON l.empresa_id = e.id 
                WHERE (l.nome LIKE :termo 
                   OR l.colecao LIKE :termo 
                   OR l.observacao LIKE :termo
                   OR e.nome LIKE :termo)
                AND l.ativo = 1
                ORDER BY l.data_entrada DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':termo' => "%$termo%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function desativarLote($loteId)
    {
        $sql = "UPDATE lotes SET ativo = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $loteId]);
    }

    public function reativarLote($loteId)
    {
        $sql = "UPDATE lotes SET ativo = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $loteId]);
    }

    public function entregarLote($loteId, $dataEntrega)
    {
        $sql = "UPDATE lotes SET status = 'Entregue', data_entrega = :data_entrega WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':data_entrega' => $dataEntrega,
            ':id' => $loteId
        ]);
    }

    public function getValorTotalLote($loteId)
    {
        $sql = "SELECT valor_total FROM lotes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $loteId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['valor_total'] : 0;
    }

    public function podeEditar($loteId)
    {
        $sql = "SELECT status FROM lotes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $loteId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['status'] === 'Aberto';
    }

    public function getTotalLotes()
    {
        $sql = "SELECT COUNT(*) as total FROM lotes WHERE ativo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getLotesRecentes($limit = 5)
    {
        $sql = "SELECT l.*, e.nome as empresa_nome 
                FROM lotes l 
                LEFT JOIN empresas e ON l.empresa_id = e.id 
                WHERE l.ativo = 1 
                ORDER BY l.data_entrada DESC 
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPecasPorLote($loteId)
    {
        $sql = "SELECT p.*, 
                       tp.nome as tipo_peca_nome,
                       c.nome as cor_nome, 
                       c.codigo_hex,
                       t.nome as tamanho_nome,
                       o.nome as operacao_nome 
                FROM pecas p 
                INNER JOIN tipos_peca tp ON p.tipo_peca_id = tp.id 
                INNER JOIN cores c ON p.cor_id = c.id 
                INNER JOIN tamanhos t ON p.tamanho_id = t.id 
                INNER JOIN operacoes o ON p.operacao_id = o.id 
                WHERE p.lote_id = :lote_id 
                ORDER BY p.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':lote_id' => $loteId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
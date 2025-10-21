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
        // Iniciar transação para garantir consistência
        $this->pdo->beginTransaction();
        
        try {
            // 1. Criar o lote
            $sql = "INSERT INTO lotes (empresa_id, colecao, nome, observacao, data_entrada, valor_total, status) 
                    VALUES (:empresa_id, :colecao, :nome, :observacao, :data_entrada, 0, 'Aberto')";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':empresa_id' => $dados['empresa_id'],
                ':colecao' => $dados['colecao'],
                ':nome' => $dados['nome'],
                ':observacao' => $dados['observacao'],
                ':data_entrada' => $dados['data_entrada']
            ]);

            $loteId = $this->pdo->lastInsertId();

            // 2. Calcular valor total das peças
            $valorTotal = $this->calcularValorTotalLote($dados['pecas']);

            // 3. Atualizar lote com valor total
            $sqlUpdate = "UPDATE lotes SET valor_total = :valor_total WHERE id = :id";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':valor_total' => $valorTotal,
                ':id' => $loteId
            ]);

            // 4. Inserir peças vinculadas ao lote
            $this->criarPecasParaLote($loteId, $dados['pecas']);

            $this->pdo->commit();
            return $loteId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function calcularValorTotalLote($pecas)
    {
        $valorTotal = 0;
        foreach ($pecas as $peca) {
            $valorTotal += ($peca['valor_unitario'] * $peca['quantidade']);
        }
        return $valorTotal;
    }

    private function criarPecasParaLote($loteId, $pecas)
    {
        $sql = "INSERT INTO pecas (lote_id, tipo_peca, cor, tamanho, quantidade, valor_unitario, operacao_id) 
                VALUES (:lote_id, :tipo_peca, :cor, :tamanho, :quantidade, :valor_unitario, :operacao_id)";
        
        $stmt = $this->pdo->prepare($sql);

        foreach ($pecas as $peca) {
            $stmt->execute([
                ':lote_id' => $loteId,
                ':tipo_peca' => $peca['tipo_peca'],
                ':cor' => $peca['cor'],
                ':tamanho' => $peca['tamanho'],
                ':quantidade' => $peca['quantidade'],
                ':valor_unitario' => $peca['valor_unitario'],
                ':operacao_id' => $peca['operacao_id']
            ]);
        }
    }

    public function getLoteComPecas($id)
    {
        // Buscar lote
        $sqlLote = "SELECT * FROM lotes WHERE id = :id";
        $stmtLote = $this->pdo->prepare($sqlLote);
        $stmtLote->execute([':id' => $id]);
        $lote = $stmtLote->fetch(PDO::FETCH_ASSOC);

        if (!$lote) {
            return null;
        }

        // Buscar peças do lote
        $sqlPecas = "SELECT p.*, o.nome as operacao_nome 
                     FROM pecas p 
                     LEFT JOIN operacoes o ON p.operacao_id = o.id 
                     WHERE p.lote_id = :lote_id";
        $stmtPecas = $this->pdo->prepare($sqlPecas);
        $stmtPecas->execute([':lote_id' => $id]);
        $pecas = $stmtPecas->fetchAll(PDO::FETCH_ASSOC);

        $lote['pecas'] = $pecas;
        return $lote;
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
                data_entrega = :data_entrega 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':descricao' => $dados['descricao'],
            ':quantidade' => $dados['quantidade'],
            ':valor' => $dados['valor'],
            ':data_inicio' => $dados['data_inicio'],
            ':data_entrega' => $dados['data_entrega'],
            ':id' => $id
        ]);
    }

    /**
 * Obter lotes ativos (status 'Aberto')
 */
public function getLotesAtivos()
{
    $sql = "SELECT * FROM lotes WHERE status = 'Aberto' ORDER BY data_entrada DESC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obter lotes por filtro
 */
public function getLotes($filtro = 'ativos')
{
    $where = '';
    if ($filtro === 'ativos') {
        $where = "WHERE status = 'Aberto'";
    } elseif ($filtro === 'finalizados') {
        $where = "WHERE status = 'Entregue'";
    } elseif ($filtro === 'inativos') {
        $where = "WHERE status = 'Inativo'";
    }

    $sql = "SELECT * FROM lotes $where ORDER BY data_entrada DESC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Buscar lotes por termo
 */
public function buscarLotes($termo)
{
    $sql = "SELECT * FROM lotes 
            WHERE nome LIKE :termo 
               OR colecao LIKE :termo 
               OR observacao LIKE :termo
            ORDER BY data_entrada DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':termo' => "%$termo%"]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Desativar lote
 */
public function desativarLote($loteId)
{
    $sql = "UPDATE lotes SET status = 'Inativo' WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([':id' => $loteId]);
}

/**
 * Reativar lote
 */
public function reativarLote($loteId)
{
    $sql = "UPDATE lotes SET status = 'Aberto' WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([':id' => $loteId]);
}

/**
 * Entregar lote
 */
public function entregarLote($loteId, $dataEntrega)
{
    $sql = "UPDATE lotes SET status = 'Entregue', data_entrega = :data_entrega WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':data_entrega' => $dataEntrega,
        ':id' => $loteId
    ]);
}

/**
 * Obter valor total do lote
 */
public function getValorTotalLote($loteId)
{
    $sql = "SELECT valor_total FROM lotes WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $loteId]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['valor_total'] : 0;
}

/**
 * Verificar se lote pode ser editado (apenas lotes abertos)
 */
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

}
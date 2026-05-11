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

    public function finalizarLote($loteId, $dataEntrega)
{
    // Iniciar transação para garantir consistência
    $this->pdo->beginTransaction();
    
    try {
        // Verificar se o lote existe e está com status Aberto
        $lote = $this->getLotePorId($loteId);
        
        if (!$lote) {
            throw new Exception('Lote não encontrado');
        }
        
        if ($lote['status'] !== 'Aberto') {
            throw new Exception('Apenas lotes com status "Aberto" podem ser finalizados');
        }
        
        // 1. Atualizar status do lote para Entregue
        $sql = "UPDATE lotes SET 
                status = 'Entregue', 
                data_entrega = :data_entrega 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':data_entrega' => $dataEntrega,
            ':id' => $loteId
        ]);
        
        // 2. Buscar dados completos do lote para criar o pagamento
        $sqlLote = "SELECT l.*, e.nome as empresa_nome 
                    FROM lotes l 
                    LEFT JOIN empresas e ON l.empresa_id = e.id 
                    WHERE l.id = :id";
        
        $stmtLote = $this->pdo->prepare($sqlLote);
        $stmtLote->execute([':id' => $loteId]);
        $loteAtualizado = $stmtLote->fetch(PDO::FETCH_ASSOC);
        
        // Commit da transação
        $this->pdo->commit();
        
        return $loteAtualizado;
        
    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}

// Método para criar pagamento recebido (entrada de dinheiro)
public function criarPagamentoRecebido($loteId, $valorRecebido, $observacao = '')
{
    $this->pdo->beginTransaction();
    
    try {
        // Buscar dados do lote
        $lote = $this->getLotePorId($loteId);
        
        if (!$lote) {
            throw new Exception('Lote não encontrado');
        }
        
        // Inserir registro de pagamento recebido
        $sql = "INSERT INTO pagamentos 
                (costureira_id, periodo_referencia, valor_bruto, 
                 valor_liquido, status, data_pagamento, observacao, 
                 created_at) 
                VALUES 
                (:empresa_id, :periodo_referencia, :valor_bruto, 
                 :valor_liquido, 'Recebido', :data_pagamento, :observacao, 
                 NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':empresa_id' => $lote['empresa_id'],
            ':periodo_referencia' => $lote['data_entrega'] ?? date('Y-m-d'),
            ':valor_bruto' => $lote['valor_total'],
            ':valor_liquido' => $valorRecebido,
            ':data_pagamento' => date('Y-m-d'),
            ':observacao' => "Pagamento recebido - Lote #{$loteId}: {$lote['nome']} (Coleção: {$lote['colecao']})\n" . 
                            ($observacao ? "Obs: {$observacao}" : "")
        ]);
        
        $pagamentoId = $this->pdo->lastInsertId();
        
        // Registrar item do pagamento (vinculando o lote como referência)
        $sqlItem = "INSERT INTO pagamento_itens 
                    (pagamento_id, servico_id, valor_calculado) 
                    VALUES 
                    (:pagamento_id, :lote_id, :valor_calculado)";
        
        $stmtItem = $this->pdo->prepare($sqlItem);
        $stmtItem->execute([
            ':pagamento_id' => $pagamentoId,
            ':lote_id' => $loteId,
            ':valor_calculado' => $lote['valor_total']
        ]);
        
        $this->pdo->commit();
        
        return [
            'pagamento_id' => $pagamentoId,
            'valor_recebido' => $valorRecebido,
            'lote' => $lote
        ];
        
    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}

// Método completo para finalizar lote e registrar pagamento
public function finalizarLoteComPagamento($loteId, $dataEntrega, $valorRecebido = null, $observacao = '')
{
    // Iniciar transação principal
    $this->pdo->beginTransaction();
    
    try {
        // 1. Buscar o lote e verificar se pode ser finalizado
        $sqlBusca = "SELECT l.*, e.nome as empresa_nome 
                     FROM lotes l 
                     LEFT JOIN empresas e ON l.empresa_id = e.id 
                     WHERE l.id = :id";
        
        $stmtBusca = $this->pdo->prepare($sqlBusca);
        $stmtBusca->execute([':id' => $loteId]);
        $lote = $stmtBusca->fetch(PDO::FETCH_ASSOC);
        
        if (!$lote) {
            throw new Exception('Lote não encontrado');
        }
        
        if ($lote['status'] !== 'Aberto') {
            throw new Exception('Apenas lotes com status "Aberto" podem ser finalizados');
        }
        
        // 2. Atualizar status do lote para Entregue
        $sqlUpdate = "UPDATE lotes SET 
                      status = 'Entregue', 
                      data_entrega = :data_entrega,
                      updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id AND status = 'Aberto'";
        
        $stmtUpdate = $this->pdo->prepare($sqlUpdate);
        $resultado = $stmtUpdate->execute([
            ':data_entrega' => $dataEntrega,
            ':id' => $loteId
        ]);
        
        if (!$resultado || $stmtUpdate->rowCount() === 0) {
            throw new Exception('Erro ao atualizar status do lote');
        }
        
        error_log("Lote {$loteId} atualizado para Entregue com sucesso");
        
        // 3. Criar registro de pagamento recebido
        $valorFinal = $valorRecebido ?? $lote['valor_total'];
        
        $sqlPagamento = "INSERT INTO pagamentos 
                        (costureira_id, periodo_referencia, valor_bruto, 
                         valor_liquido, status, data_pagamento, observacao, 
                         created_at) 
                        VALUES 
                        (:empresa_id, :periodo_referencia, :valor_bruto, 
                         :valor_liquido, 'Recebido', :data_pagamento, :observacao, 
                         NOW())";
        
        $stmtPagamento = $this->pdo->prepare($sqlPagamento);
        $stmtPagamento->execute([
            ':empresa_id' => $lote['empresa_id'],
            ':periodo_referencia' => $dataEntrega,
            ':valor_bruto' => $lote['valor_total'],
            ':valor_liquido' => $valorFinal,
            ':data_pagamento' => date('Y-m-d'),
            ':observacao' => "Pagamento recebido - Lote #{$loteId}: {$lote['nome']}\n" .
                            "Coleção: {$lote['colecao']}\n" .
                            "Empresa: {$lote['empresa_nome']}\n" .
                            ($observacao ? "Obs: {$observacao}" : "")
        ]);
        
        $pagamentoId = $this->pdo->lastInsertId();
        error_log("Pagamento {$pagamentoId} criado com sucesso para o lote {$loteId}");
        
        // 4. Registrar item do pagamento (usando servico_id como referência ao lote)
        $sqlItem = "INSERT INTO pagamento_itens 
                    (pagamento_id, servico_id, valor_calculado) 
                    VALUES 
                    (:pagamento_id, :lote_id, :valor_calculado)";
        
        $stmtItem = $this->pdo->prepare($sqlItem);
        $stmtItem->execute([
            ':pagamento_id' => $pagamentoId,
            ':lote_id' => $loteId,
            ':valor_calculado' => $lote['valor_total']
        ]);
        
        error_log("Item do pagamento criado com sucesso");
        
        // 5. Commit da transação
        $this->pdo->commit();
        error_log("Transação concluída com sucesso para o lote {$loteId}");
        
        return [
            'success' => true,
            'lote' => $lote,
            'pagamento' => [
                'id' => $pagamentoId,
                'valor_recebido' => $valorFinal
            ]
        ];
        
    } catch (Exception $e) {
        // Rollback em caso de erro
        $this->pdo->rollBack();
        error_log("Erro ao finalizar lote {$loteId}: " . $e->getMessage());
        throw $e;
    }
}
}
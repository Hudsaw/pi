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

        $sql = "INSERT INTO pecas (lote_id, tipo_peca_id, cor_id, tamanho_id, quantidade, valor_unitario, operacao_id, observacao) 
                VALUES (:lote_id, :tipo_peca_id, :cor_id, :tamanho_id, :quantidade, :valor_unitario, :operacao_id, :observacao)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':lote_id' => $dados['lote_id'],
            ':tipo_peca_id' => $dados['tipo_peca_id'],
            ':cor_id' => $dados['cor_id'],
            ':tamanho_id' => $dados['tamanho_id'],
            ':quantidade' => $dados['quantidade'],
            ':valor_unitario' => $dados['valor_unitario'],
            ':operacao_id' => $dados['operacao_id'],
            ':observacao' => $dados['observacao'] ?? null
        ]);

        return $this->pdo->lastInsertId();
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

    public function getPecaPorId($id)
    {
        $sql = "SELECT p.*, 
                       tp.nome as tipo_peca_nome,
                       c.nome as cor_nome,
                       t.nome as tamanho_nome,
                       o.nome as operacao_nome,
                       l.descricao as lote_descricao 
                FROM pecas p 
                INNER JOIN tipos_peca tp ON p.tipo_peca_id = tp.id 
                INNER JOIN cores c ON p.cor_id = c.id 
                INNER JOIN tamanhos t ON p.tamanho_id = t.id 
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

    public function getPecasPorLoteComPaginacao($loteId, $itensPorPagina, $offset, $search = '')
{
    // Query base
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
            WHERE p.lote_id = :lote_id";
    
    // Adicionar busca se existir
    if (!empty($search)) {
        $sql .= " AND (tp.nome LIKE :search OR c.nome LIKE :search OR t.nome LIKE :search OR o.nome LIKE :search)";
    }
    
    $sql .= " ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':lote_id', $loteId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    if (!empty($search)) {
        $stmt->bindValue(':search', '%' . $search . '%');
    }
    
    $stmt->execute();
    $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar total
    $sqlCount = "SELECT COUNT(*) as total 
                 FROM pecas p 
                 INNER JOIN tipos_peca tp ON p.tipo_peca_id = tp.id 
                 INNER JOIN cores c ON p.cor_id = c.id 
                 INNER JOIN tamanhos t ON p.tamanho_id = t.id 
                 INNER JOIN operacoes o ON p.operacao_id = o.id 
                 WHERE p.lote_id = :lote_id";
    
    if (!empty($search)) {
        $sqlCount .= " AND (tp.nome LIKE :search OR c.nome LIKE :search OR t.nome LIKE :search OR o.nome LIKE :search)";
    }
    
    $stmtCount = $this->pdo->prepare($sqlCount);
    $stmtCount->bindValue(':lote_id', $loteId, PDO::PARAM_INT);
    
    if (!empty($search)) {
        $stmtCount->bindValue(':search', '%' . $search . '%');
    }
    
    $stmtCount->execute();
    $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    
    return [
        'pecas' => $pecas,
        'total' => $total
    ];
}

public function getTiposAtivos()
    {
        $sql = "SELECT * FROM tipos_peca WHERE ativo = 1 ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTipoPorId($id)
    {
        $sql = "SELECT * FROM tipos_peca WHERE id = :id AND ativo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criarTipo($dados)
    {
        $sql = "INSERT INTO tipos_peca (nome, descricao) VALUES (:nome, :descricao)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao']
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function desativarTipo($id)
    {
        $sql = "UPDATE tipos_peca SET ativo = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getCoresAtivas()
    {
        $sql = "SELECT * FROM cores WHERE ativo = 1 ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCorPorId($id)
    {
        $sql = "SELECT * FROM cores WHERE id = :id AND ativo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criarCor($dados)
    {
        $sql = "INSERT INTO cores (nome, codigo_hex) VALUES (:nome, :codigo_hex)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':codigo_hex' => $dados['codigo_hex']
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function getTamanhosAtivos()
    {
        $sql = "SELECT * FROM tamanhos WHERE ativo = 1 ORDER BY ordem, nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTamanhoPorId($id)
    {
        $sql = "SELECT * FROM tamanhos WHERE id = :id AND ativo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criarTamanho($dados)
    {
        $sql = "INSERT INTO tamanhos (nome, ordem) VALUES (:nome, :ordem)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':ordem' => $dados['ordem']
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function desativarCor($id)
{
    $sql = "UPDATE cores SET ativo = 0 WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([':id' => $id]);
}

public function desativarTamanho($id)
{
    $sql = "UPDATE tamanhos SET ativo = 0 WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([':id' => $id]);
}



}
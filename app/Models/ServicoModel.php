<?php
namespace App\Models;

use PDO;
use Exception;

class ServicoModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Verificar se já existe serviço do mesmo tipo no lote
    public function servicoDoMesmoTipoExiste($loteId, $operacaoId, $servicoId = null)
{
    $sql = "SELECT COUNT(*) as total FROM servicos 
            WHERE lote_id = :lote_id AND operacao_id = :operacao_id AND status != 'Inativo'";
    
    if ($servicoId) {
        $sql .= " AND id != :servico_id";
    }
    
    $stmt = $this->pdo->prepare($sql);
    $params = [
        ':lote_id' => $loteId,
        ':operacao_id' => $operacaoId
    ];
    
    if ($servicoId) {
        $params[':servico_id'] = $servicoId;
    }
    
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['total'] > 0;
}

    // Criar serviço (operação dentro de um lote)
    public function criarServico($dados)
    {
        // Verificar se já existe serviço do mesmo tipo no lote
        if ($this->servicoDoMesmoTipoExiste($dados['lote_id'], $dados['operacao_id'])) {
            throw new Exception('Já existe um serviço deste tipo no lote selecionado.');
        }
        
        $sql = "INSERT INTO servicos (lote_id, operacao_id, quantidade_pecas, valor_operacao, data_envio, observacao, status, costureira_id) 
                VALUES (:lote_id, :operacao_id, :quantidade_pecas, :valor_operacao, :data_envio, :observacao, 'Em andamento', :costureira_id)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':lote_id' => $dados['lote_id'],
            ':operacao_id' => $dados['operacao_id'],
            ':quantidade_pecas' => $dados['quantidade_pecas'],
            ':valor_operacao' => $dados['valor_operacao'],
            ':data_envio' => $dados['data_envio'],
            ':observacao' => $dados['observacao'],
            ':costureira_id' => $dados['costureira_id']
        ]);

        return $this->pdo->lastInsertId();
    }

    // Obter todos os serviços (operações)
    public function getServicos($filtro = null)
{
    $where = "";
    if ($filtro === 'ativos') {
        $where = "WHERE s.status = 'Em andamento'";
    } elseif ($filtro === 'finalizados') {
        $where = "WHERE s.status = 'Finalizado'";
    } elseif ($filtro === 'inativos') {
        $where = "WHERE s.status = 'Inativo'";
    }
    // Se $filtro for null ou 'todos', não aplica filtro

    $sql = "SELECT s.*, 
                   l.nome as lote_nome,
                   l.colecao,
                   o.nome as operacao_nome,
                   o.valor as valor_base_operacao,
                   u.nome as costureira_nome,
                   e.nome as costureira_especialidade
            FROM servicos s
            INNER JOIN lotes l ON s.lote_id = l.id
            INNER JOIN operacoes o ON s.operacao_id = o.id
            LEFT JOIN usuarios u ON s.costureira_id = u.id
            LEFT JOIN especialidade e ON u.especialidade_id = e.id
            $where
            ORDER BY s.data_envio DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Buscar serviços
    public function buscarServicos($termo)
{
    $sql = "SELECT s.*, 
                   l.nome as lote_nome,
                   l.colecao,
                   o.nome as operacao_nome,
                   u.nome as costureira_nome,
                   s.status
            FROM servicos s
            INNER JOIN lotes l ON s.lote_id = l.id
            INNER JOIN operacoes o ON s.operacao_id = o.id
            LEFT JOIN usuarios u ON s.costureira_id = u.id
            WHERE l.nome LIKE :termo 
               OR l.colecao LIKE :termo
               OR o.nome LIKE :termo 
               OR u.nome LIKE :termo
               OR s.status LIKE :termo
            ORDER BY s.data_envio DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':termo' => "%$termo%"]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Obter serviço por ID
    public function getServicoPorId($id)
    {
        $sql = "SELECT s.*, 
                       l.nome as lote_nome,
                       l.colecao,
                       o.nome as operacao_nome,
                       o.valor as valor_base_operacao,
                       u.id as costureira_id,
                       u.nome as costureira_nome,
                       e.nome as costureira_especialidade
                FROM servicos s
                INNER JOIN lotes l ON s.lote_id = l.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                LEFT JOIN usuarios u ON s.costureira_id = u.id
                LEFT JOIN especialidade e ON u.especialidade_id = e.id
                WHERE s.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obter costureiras ativas para vinculação
    public function getCostureirasAtivas()
    {
        $sql = "SELECT u.id, u.nome, e.nome as especialidade
                FROM usuarios u
                LEFT JOIN especialidade e ON u.especialidade_id = e.id
                WHERE u.tipo = 'costureira' AND u.ativo = 1 
                ORDER BY u.nome";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obter lotes ativos para criação de serviços
    public function getLotesAtivos()
    {
        $sql = "SELECT id, nome, colecao FROM lotes WHERE status = 'Aberto' ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obter operações ativas
    public function getOperacoesAtivas()
    {
        $sql = "SELECT id, nome, valor FROM operacoes WHERE ativo = 1 ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Finalizar serviço
    public function finalizarServico($servicoId, $dataFinalizacao)
    {
        $sql = "UPDATE servicos SET status = 'Finalizado', data_finalizacao = :data_finalizacao 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':data_finalizacao' => $dataFinalizacao,
            ':id' => $servicoId
        ]);
    }

    // Atualizar serviço
    public function atualizarServico($id, $dados)
    {
        // Verificar se já existe outro serviço do mesmo tipo no lote
        if ($this->servicoDoMesmoTipoExiste($dados['lote_id'], $dados['operacao_id'], $id)) {
            throw new Exception('Já existe um serviço deste tipo no lote selecionado.');
        }
        
        $sql = "UPDATE servicos 
                SET lote_id = :lote_id, 
                    operacao_id = :operacao_id, 
                    quantidade_pecas = :quantidade_pecas, 
                    valor_operacao = :valor_operacao, 
                    data_envio = :data_envio, 
                    observacao = :observacao,
                    costureira_id = :costureira_id
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':lote_id' => $dados['lote_id'],
            ':operacao_id' => $dados['operacao_id'],
            ':quantidade_pecas' => $dados['quantidade_pecas'],
            ':valor_operacao' => $dados['valor_operacao'],
            ':data_envio' => $dados['data_envio'],
            ':observacao' => $dados['observacao'],
            ':costureira_id' => $dados['costureira_id'],
            ':id' => $id
        ]);
    }

    public function getCostureirasDisponiveis()
    {
        // falta innerjoin e selecionar quem nao tem servico
        $sql = "SELECT u.id, u.nome, e.nome as especialidade
                FROM usuarios u
                LEFT JOIN especialidade e ON u.especialidade_id = e.id
                WHERE u.tipo = 'costureira' AND u.ativo = 1 
                ORDER BY u.nome";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Desvincular costureira de um serviço
    public function desvincularCostureira($servicoId)
    {
        $sql = "UPDATE servicos SET costureira_id = NULL WHERE id = :servico_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':servico_id' => $servicoId]);
    }

    // Desativar serviço
    public function desativarServico($servicoId)
    {
        $sql = "UPDATE servicos SET status = 'Inativo' WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $servicoId]);
    }

    public function getServicosFinalizadosPorCostureira($costureiraId)
    {
        $sql = "SELECT s.*, 
                       l.nome as lote_nome,
                       o.nome as operacao_nome
                FROM servicos s
                INNER JOIN lotes l ON s.lote_id = l.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                WHERE s.costureira_id = :costureira_id 
                AND s.status = 'Finalizado'
                ORDER BY s.data_finalizacao DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':costureira_id' => $costureiraId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalServicos()
    {
        $sql = "SELECT COUNT(*) as total FROM servicos";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getServicosAtivos()
    {
        $sql = "SELECT COUNT(*) as total FROM servicos WHERE status = 'Em andamento'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getServicosRecentes($limit = 5)
    {
        $sql = "SELECT s.*, o.nome as operacao_nome, l.nome as lote_nome 
                FROM servicos s 
                LEFT JOIN operacoes o ON s.operacao_id = o.id 
                LEFT JOIN lotes l ON s.lote_id = l.id 
                ORDER BY s.created_at DESC 
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServicosAtivosPorCostureira($costureiraId)
    {
        $sql = "SELECT s.*, 
                       l.nome as lote_nome,
                       o.nome as operacao_nome
                FROM servicos s
                INNER JOIN lotes l ON s.lote_id = l.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                WHERE s.costureira_id = :costureira_id 
                AND s.status = 'em_andamento'
                ORDER BY s.data_envio DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':costureira_id' => $costureiraId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
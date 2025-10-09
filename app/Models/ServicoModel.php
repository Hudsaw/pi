<?php
namespace App\Models;

use PDO;

class ServicoModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Criar serviço
    public function criarServico($dados)
    {
        $sql = "INSERT INTO servicos (lote_id, operacao_id, quantidade_pecas, valor_operacao, data_envio, observacao, status) 
                VALUES (:lote_id, :operacao_id, :quantidade_pecas, :valor_operacao, :data_envio, :observacao, 'Em andamento')";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':lote_id' => $dados['lote_id'],
            ':operacao_id' => $dados['operacao_id'],
            ':quantidade_pecas' => $dados['quantidade_pecas'],
            ':valor_operacao' => $dados['valor_operacao'],
            ':data_envio' => $dados['data_envio'],
            ':observacao' => $dados['observacao']
        ]);

        return $this->pdo->lastInsertId();
    }

    // Obter todos os serviços
    public function getServicos($filtro = 'ativos')
    {
        $where = '';
        if ($filtro === 'ativos') {
            $where = "WHERE s.status = 'Em andamento'";
        } elseif ($filtro === 'finalizados') {
            $where = "WHERE s.status = 'Finalizado'";
        } elseif ($filtro === 'inativos') {
            $where = "WHERE s.status = 'Inativo'";
        }

        $sql = "SELECT s.*, 
                       l.nome as lote_nome,
                       o.nome as operacao_nome,
                       u.nome as costureira_nome
                FROM servicos s
                LEFT JOIN lotes l ON s.lote_id = l.id
                LEFT JOIN operacoes o ON s.operacao_id = o.id
                LEFT JOIN servico_costureiras sc ON s.id = sc.servico_id
                LEFT JOIN usuarios u ON sc.costureira_id = u.id
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
                       o.nome as operacao_nome,
                       u.nome as costureira_nome
                FROM servicos s
                LEFT JOIN lotes l ON s.lote_id = l.id
                LEFT JOIN operacoes o ON s.operacao_id = o.id
                LEFT JOIN servico_costureiras sc ON s.id = sc.servico_id
                LEFT JOIN usuarios u ON sc.costureira_id = u.id
                WHERE l.nome LIKE :termo 
                   OR o.nome LIKE :termo 
                   OR u.nome LIKE :termo
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
                       o.valor as valor_base_operacao
                FROM servicos s
                LEFT JOIN lotes l ON s.lote_id = l.id
                LEFT JOIN operacoes o ON s.operacao_id = o.id
                WHERE s.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($servico) {
            // Buscar costureiras vinculadas
            $servico['costureiras'] = $this->getCostureirasPorServico($id);
        }

        return $servico;
    }

    // Obter costureiras vinculadas a um serviço
    public function getCostureirasPorServico($servicoId)
    {
        $sql = "SELECT u.id, u.nome, u.especialidade, sc.data_inicio, sc.data_entrega
                FROM servico_costureiras sc
                JOIN usuarios u ON sc.costureira_id = u.id
                WHERE sc.servico_id = :servico_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':servico_id' => $servicoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vincular costureira a serviço
    public function vincularCostureira($servicoId, $costureiraId, $dataInicio, $dataEntrega)
{
    // Verificar se costureira pode ser vinculada
    $sqlCheck = "CALL verificar_vinculo_costureira(:costureira_id, @pode_ser_vinculada, @mensagem)";
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute([':costureira_id' => $costureiraId]);
    
    // Obter resultado da procedure
    $sqlResult = "SELECT @pode_ser_vinculada as pode_ser_vinculada, @mensagem as mensagem";
    $stmtResult = $this->pdo->prepare($sqlResult);
    $stmtResult->execute();
    $result = $stmtResult->fetch(PDO::FETCH_ASSOC);
    
    if (!$result['pode_ser_vinculada']) {
        throw new Exception($result['mensagem']);
    }
    
    // Se pode ser vinculada, fazer o vínculo
    $sql = "INSERT INTO servico_costureiras (servico_id, costureira_id, data_inicio, data_entrega) 
            VALUES (:servico_id, :costureira_id, :data_inicio, :data_entrega)";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':servico_id' => $servicoId,
        ':costureira_id' => $costureiraId,
        ':data_inicio' => $dataInicio,
        ':data_entrega' => $dataEntrega
    ]);
}

    // Obter serviços ativos por costureira
    public function getServicosAtivosPorCostureira($costureiraId)
    {
        $sql = "SELECT s.* 
                FROM servicos s
                JOIN servico_costureiras sc ON s.id = sc.servico_id
                WHERE sc.costureira_id = :costureira_id 
                AND s.status = 'Em andamento'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':costureira_id' => $costureiraId]);

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

    // Desativar serviço
    public function desativarServico($servicoId)
    {
        $sql = "UPDATE servicos SET status = 'Inativo' WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $servicoId]);
    }

    // Obter lotes ativos
    public function getLotesAtivos()
    {
        $sql = "SELECT id, nome, colecao FROM lotes WHERE status = 'Aberto' ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
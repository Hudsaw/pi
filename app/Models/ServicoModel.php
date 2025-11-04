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

    // Criar serviço (operação dentro de um lote)
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

    // Obter todos os serviços (operações)
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
                       l.colecao,
                       o.nome as operacao_nome,
                       o.valor as valor_base_operacao,
                       GROUP_CONCAT(DISTINCT u.nome SEPARATOR ', ') as costureiras_vinculadas
                FROM servicos s
                INNER JOIN lotes l ON s.lote_id = l.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                LEFT JOIN servico_costureiras sc ON s.id = sc.servico_id
                LEFT JOIN usuarios u ON sc.costureira_id = u.id
                $where
                GROUP BY s.id
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
                       GROUP_CONCAT(DISTINCT u.nome SEPARATOR ', ') as costureiras_vinculadas
                FROM servicos s
                INNER JOIN lotes l ON s.lote_id = l.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                LEFT JOIN servico_costureiras sc ON s.id = sc.servico_id
                LEFT JOIN usuarios u ON sc.costureira_id = u.id
                WHERE l.nome LIKE :termo 
                   OR o.nome LIKE :termo 
                   OR u.nome LIKE :termo
                   OR l.colecao LIKE :termo
                GROUP BY s.id
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
                INNER JOIN lotes l ON s.lote_id = l.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                WHERE s.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($servico) {
            // Buscar costureiras vinculadas a ESTE SERVIÇO (operação específica)
            $servico['costureiras'] = $this->getCostureirasPorServico($id);
        }

        return $servico;
    }

    // Obter costureiras vinculadas a um serviço específico
    public function getCostureirasPorServico($servicoId)
    {
        $sql = "SELECT u.id, u.nome, e.nome as especialidade, sc.data_inicio, sc.data_entrega
                FROM servico_costureiras sc
                INNER JOIN usuarios u ON sc.costureira_id = u.id
                LEFT JOIN especialidade e ON u.especialidade_id = e.id
                WHERE sc.servico_id = :servico_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':servico_id' => $servicoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vincular costureira a um serviço específico
    public function vincularCostureira($servicoId, $costureiraId, $dataInicio, $dataEntrega)
{
    error_log("ServicoModel::vincularCostureira - Servico: $servicoId, Costureira: $costureiraId");
    
    // Verificar se já está vinculada a este mesmo serviço
    $sqlCheck = "SELECT COUNT(*) as total FROM servico_costureiras 
                 WHERE servico_id = :servico_id AND costureira_id = :costureira_id";
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute([
        ':servico_id' => $servicoId,
        ':costureira_id' => $costureiraId
    ]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($result['total'] > 0) {
        throw new Exception('Costureira já está vinculada a este serviço.');
    }

    // VERIFICAR LIMITE DE 2 SERVIÇOS ATIVOS
    $servicosAtivos = $this->getServicosAtivosPorCostureira($costureiraId);
    if (count($servicosAtivos) >= 2) {
        throw new Exception('Costureira já possui 2 serviços ativos. Limite máximo atingido.');
    }

    // Fazer o vínculo
    $sql = "INSERT INTO servico_costureiras (servico_id, costureira_id, data_inicio, data_entrega) 
            VALUES (:servico_id, :costureira_id, :data_inicio, :data_entrega)";
    
    $stmt = $this->pdo->prepare($sql);
    $success = $stmt->execute([
        ':servico_id' => $servicoId,
        ':costureira_id' => $costureiraId,
        ':data_inicio' => $dataInicio,
        ':data_entrega' => $dataEntrega
    ]);

    error_log("Resultado do INSERT: " . ($success ? 'SUCESSO' : 'FALHA'));

    // ENVIAR MENSAGEM AUTOMÁTICA PARA A COSTUREIRA
    if ($success) {
        $this->enviarMensagemVinculacao($servicoId, $costureiraId, $dataEntrega);
    }

    return $success;
}

// Método para enviar mensagem automática
private function enviarMensagemVinculacao($servicoId, $costureiraId, $dataEntrega)
{
    $servico = $this->getServicoPorId($servicoId);
    $mensagem = "Você foi vinculada ao serviço: {$servico['operacao_nome']} - Lote: {$servico['lote_nome']}. Data de entrega: " . date('d/m/Y', strtotime($dataEntrega));
    
    $sql = "INSERT INTO mensagens (remetente_id, destinatario_id, mensagem, lida) 
            VALUES (1, :costureira_id, :mensagem, 0)";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':costureira_id' => $costureiraId,
        ':mensagem' => $mensagem
    ]);
}

    // Obter serviços ativos por costureira
    public function getServicosAtivosPorCostureira($costureiraId)
    {
        $sql = "SELECT s.*, 
                       l.nome as lote_nome,
                       o.nome as operacao_nome
                FROM servicos s
                INNER JOIN servico_costureiras sc ON s.id = sc.servico_id
                INNER JOIN lotes l ON s.lote_id = l.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                WHERE sc.costureira_id = :costureira_id 
                AND s.status = 'Em andamento'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':costureira_id' => $costureiraId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $sql = "UPDATE servicos 
                SET lote_id = :lote_id, 
                    operacao_id = :operacao_id, 
                    quantidade_pecas = :quantidade_pecas, 
                    valor_operacao = :valor_operacao, 
                    data_envio = :data_envio, 
                    observacao = :observacao 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':lote_id' => $dados['lote_id'],
            ':operacao_id' => $dados['operacao_id'],
            ':quantidade_pecas' => $dados['quantidade_pecas'],
            ':valor_operacao' => $dados['valor_operacao'],
            ':data_envio' => $dados['data_envio'],
            ':observacao' => $dados['observacao'],
            ':id' => $id
        ]);
    }

    // Desvincular costureira de um serviço
    public function desvincularCostureira($servicoId, $costureiraId)
    {
        $sql = "DELETE FROM servico_costureiras 
                WHERE servico_id = :servico_id AND costureira_id = :costureira_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':servico_id' => $servicoId,
            ':costureira_id' => $costureiraId
        ]);
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
                   o.nome as operacao_nome,
                   sc.data_inicio,
                   sc.data_entrega
            FROM servicos s
            INNER JOIN servico_costureiras sc ON s.id = sc.servico_id
            INNER JOIN lotes l ON s.lote_id = l.id
            INNER JOIN operacoes o ON s.operacao_id = o.id
            WHERE sc.costureira_id = :costureira_id 
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
    $sql = "SELECT COUNT(*) as total FROM servicos WHERE status = 'ativo'";
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
}
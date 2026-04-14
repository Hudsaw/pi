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

        // Verificar se a costureira já tem serviço em andamento
    if ($this->costureiraTemServicoEmAndamento($dados['costureira_id'])) {
        throw new Exception('Esta costureira já possui um serviço em andamento.');
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
    // $where = "";
    // if ($filtro === 'ativos') {
    //     $where = "WHERE s.status = 'Em andamento'";
    // } elseif ($filtro === 'finalizados') {
    //     $where = "WHERE s.status = 'Finalizado'";
    // } elseif ($filtro === 'inativos') {
    //     $where = "WHERE s.status = 'Inativo'";
    // }
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

    public function atualizarProgresso($servicoId, $pecasConcluidas)
{
    // Buscar o serviço atual
    $servico = $this->getServicoPorId($servicoId);
    if (!$servico) {
        throw new Exception('Serviço não encontrado');
    }

    $quantidadeTotal = $servico['quantidade_pecas'];

    // Validar se não excede o total
    if ($pecasConcluidas > $quantidadeTotal) {
        throw new Exception('Não é possível concluir mais peças do que o total do serviço');
    }

    if ($pecasConcluidas < 0) {
        throw new Exception('Valor inválido para peças concluídas');
    }

    // Iniciar transação
    $this->pdo->beginTransaction();
    
    try {
        // Atualizar as peças concluídas
        $sql = "UPDATE servicos SET pecas_concluidas = :pecas_concluidas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':pecas_concluidas' => $pecasConcluidas,
            ':id' => $servicoId
        ]);
        
        // Se todas as peças foram concluídas e ainda não está finalizado
        if ($pecasConcluidas >= $quantidadeTotal && $servico['status'] != 'Finalizado') {
            $sqlStatus = "UPDATE servicos SET status = 'Finalizado', data_finalizacao = NOW() WHERE id = :id";
            $stmtStatus = $this->pdo->prepare($sqlStatus);
            $stmtStatus->execute([':id' => $servicoId]);
        } 
        // Se NÃO atingiu o total mas está como Finalizado (caso de correção)
        else if ($pecasConcluidas < $quantidadeTotal && $servico['status'] == 'Finalizado') {
            $sqlStatus = "UPDATE servicos SET status = 'Em andamento', data_finalizacao = NULL WHERE id = :id";
            $stmtStatus = $this->pdo->prepare($sqlStatus);
            $stmtStatus->execute([':id' => $servicoId]);
        }
        
        $this->pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}

    public function getServicoPorId($id)
{
    $sql = "SELECT s.*, 
                   l.nome as lote_nome,
                   l.colecao,
                   l.data_entrega,
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
    
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Garantir que pecas_concluidas tenha um valor padrão
    if ($servico && !isset($servico['pecas_concluidas'])) {
        $servico['pecas_concluidas'] = 0;
    }
    
    return $servico;
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
    // Iniciar transação para garantir consistência
    $this->pdo->beginTransaction();
    
    try {
        // Buscar informações do serviço antes de finalizar
        $servico = $this->getServicoPorId($servicoId);
        if (!$servico) {
            throw new Exception('Serviço não encontrado');
        }
        
        // Verificar se já não está finalizado
        if ($servico['status'] === 'Finalizado') {
            throw new Exception('Serviço já está finalizado');
        }
        
        // Atualizar status para Finalizado
        $sql = "UPDATE servicos SET status = 'Finalizado', data_finalizacao = :data_finalizacao 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':data_finalizacao' => $dataFinalizacao,
            ':id' => $servicoId
        ]);
        
        if (!$result) {
            throw new Exception('Erro ao finalizar serviço');
        }
        
        // Criar pagamento automaticamente se houver costureira vinculada
        if ($servico['costureira_id']) {
            $this->criarPagamentoAutomatico(
                $servicoId,
                $servico['costureira_id'],
                $servico['pecas_concluidas'] ?: $servico['quantidade_pecas'], // Usa peças concluídas ou total
                $servico['valor_operacao']
            );
        }
        
        $this->pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $this->pdo->rollBack();
        error_log("Erro ao finalizar serviço: " . $e->getMessage());
        throw $e;
    }
}

public function criarPagamentoAutomatico($servicoId, $costureiraId, $quantidadePecas, $valorOperacao)
{
    error_log("=== INICIANDO CRIAÇÃO DE PAGAMENTO AUTOMÁTICO ===");
    error_log("Serviço ID: $servicoId, Costureira ID: $costureiraId, Qtd: $quantidadePecas, Valor Unit: $valorOperacao");
    
    try {
        // Verificar se já existe pagamento pendente para este serviço
        $sql = "SELECT pi.id, p.id as pagamento_id, p.status
                FROM pagamento_itens pi 
                INNER JOIN pagamentos p ON pi.pagamento_id = p.id 
                WHERE pi.servico_id = :servico_id AND p.status IN ('Pendente', 'Pago')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':servico_id' => $servicoId]);
        
        $existente = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existente) {
            error_log("Já existe pagamento para este serviço. Pagamento ID: " . $existente['pagamento_id']);
            return $existente['pagamento_id'];
        }
        
        $valorTotal = $quantidadePecas * $valorOperacao;
        $periodoReferencia = date('Y-m-01'); // Primeiro dia do mês atual
        
        error_log("Valor Total: $valorTotal, Período: $periodoReferencia");
        
        // Verificar se já existe pagamento pendente para esta costureira neste mês
        $sql = "SELECT id FROM pagamentos 
                WHERE costureira_id = :costureira_id 
                AND periodo_referencia = :periodo 
                AND status = 'Pendente'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':costureira_id' => $costureiraId,
            ':periodo' => $periodoReferencia
        ]);
        $pagamentoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pagamentoExistente) {
            $pagamentoId = $pagamentoExistente['id'];
            error_log("Pagamento existente encontrado. ID: $pagamentoId");
            
            // Atualizar valor bruto do pagamento
            $sql = "UPDATE pagamentos 
                    SET valor_bruto = valor_bruto + :valor,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':valor' => $valorTotal,
                ':id' => $pagamentoId
            ]);
            error_log("Valor do pagamento atualizado");
        } else {
            error_log("Criando novo pagamento");
            // Criar novo pagamento
            $sql = "INSERT INTO pagamentos (costureira_id, periodo_referencia, valor_bruto, status) 
                    VALUES (:costureira_id, :periodo, :valor, 'Pendente')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':costureira_id' => $costureiraId,
                ':periodo' => $periodoReferencia,
                ':valor' => $valorTotal
            ]);
            $pagamentoId = $this->pdo->lastInsertId();
            error_log("Novo pagamento criado. ID: $pagamentoId");
        }
        
        // Adicionar item ao pagamento
        $sql = "INSERT INTO pagamento_itens (pagamento_id, servico_id, valor_calculado) 
                VALUES (:pagamento_id, :servico_id, :valor)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':pagamento_id' => $pagamentoId,
            ':servico_id' => $servicoId,
            ':valor' => $valorTotal
        ]);
        
        error_log("Item adicionado ao pagamento");
        error_log("=== PAGAMENTO CRIADO COM SUCESSO ===");
        
        return $pagamentoId;
        
    } catch (Exception $e) {
        error_log("ERRO ao criar pagamento automático: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        throw $e;
    }
}

    // Atualizar serviço
    public function atualizarServico($id, $dados)
    {
        // Verificar se já existe outro serviço do mesmo tipo no lote
        if ($this->servicoDoMesmoTipoExiste($dados['lote_id'], $dados['operacao_id'], $id)) {
            throw new Exception('Já existe um serviço deste tipo no lote selecionado.');
        }

        // Verificar se a costureira já tem serviço em andamento
    if ($this->costureiraTemServicoEmAndamento($dados['costureira_id'])) {
        throw new Exception('Esta costureira já possui um serviço em andamento.');
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
                AND u.id NOT IN (
                SELECT DISTINCT costureira_id 
                FROM servicos 
                WHERE status = 'Em andamento' 
                AND costureira_id IS NOT NULL
            )
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

    public function costureiraTemServicoEmAndamento($costureiraId, $servicoId = null)
{
    $sql = "SELECT COUNT(*) as total FROM servicos 
            WHERE costureira_id = :costureira_id 
            AND status = 'Em andamento'";
    
    if ($servicoId) {
        $sql .= " AND id != :servico_id";
    }
    
    $stmt = $this->pdo->prepare($sql);
    $params = [':costureira_id' => $costureiraId];
    
    if ($servicoId) {
        $params[':servico_id'] = $servicoId;
    }
    
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['total'] > 0;
}

public function getServicosRecentesPorCostureira($costureiraId, $limite = 10)
{
    $sql = "SELECT s.*, 
                   l.nome as lote_nome,
                   l.colecao,
                   l.data_entrega,
                   o.nome as operacao_nome,
                   o.valor as valor_base_operacao,
                   s.status as status  
            FROM servicos s
            INNER JOIN lotes l ON s.lote_id = l.id
            INNER JOIN operacoes o ON s.operacao_id = o.id
            WHERE s.costureira_id = :costureira_id 
            ORDER BY s.data_envio DESC, s.id DESC
            LIMIT :limite";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':costureira_id', $costureiraId, PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getServicosAtivosPorCostureira($costureiraId)
{
    $sql = "SELECT s.*, 
                   l.nome as lote_nome,
                   l.colecao,
                   l.data_entrega,  
                   o.nome as operacao_nome,
                   o.valor as valor_base_operacao
            FROM servicos s
            INNER JOIN lotes l ON s.lote_id = l.id
            INNER JOIN operacoes o ON s.operacao_id = o.id
            WHERE s.costureira_id = :costureira_id 
            AND s.status = 'Em andamento'  
            ORDER BY s.data_envio DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':costureira_id' => $costureiraId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getServicosFinalizadosPorCostureira($costureiraId)
{
    $sql = "SELECT s.*, 
                   l.nome as lote_nome,
                   l.colecao,
                   o.nome as operacao_nome,
                   o.valor as valor_base_operacao
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


public function getTodosServicosPorCostureira($costureiraId)
{
    $sql = "SELECT s.*, 
                   l.nome as lote_nome,
                   l.colecao,
                   o.nome as operacao_nome,
                   o.valor as valor_base_operacao
            FROM servicos s
            INNER JOIN lotes l ON s.lote_id = l.id
            INNER JOIN operacoes o ON s.operacao_id = o.id
            WHERE s.costureira_id = :costureira_id 
            ORDER BY 
                CASE s.status
                    WHEN 'Em andamento' THEN 1
                    WHEN 'Finalizado' THEN 2
                    ELSE 3
                END,
                s.data_envio DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':costureira_id' => $costureiraId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
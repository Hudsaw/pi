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
    
    // VERIFICAÇÃO DA QUANTIDADE MÁXIMA DE PEÇAS
    $loteModel = new LoteModel($this->pdo);
    $validacao = $loteModel->validarQuantidadeServico(
        $dados['lote_id'], 
        $dados['quantidade_pecas'], 
        $dados['operacao_id']
    );
    
    if (isset($validacao['error'])) {
        throw new Exception($validacao['error']);
    }
    
    // Se tem warning, podemos permitir mas logar
    if (isset($validacao['warning'])) {
        error_log("AVISO: " . $validacao['warning']);
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
    public function finalizarServico($servicoId, $dataFinalizacao, $quantidadeConcluida = null, $observacaoPerdas = null)
{
    error_log("Finalizando SERVIÇO ===");
    
    try {
        // Primeiro, buscar o serviço
        $servico = $this->getServicoPorId($servicoId);
                
        if (!$servico) {
            throw new Exception('Serviço não encontrado');
        }
        
        // Verificar se tem costureira
        if (empty($servico['costureira_id'])) {
        } else {
            error_log("Costureira encontrada - ID: " . $servico['costureira_id'] . 
                     ", Nome: " . ($servico['costureira_nome'] ?? 'N/A'));
        }
        
        // Se não foi informada quantidade concluída, usar o total
        if ($quantidadeConcluida === null || $quantidadeConcluida === '') {
            $quantidadeConcluida = $servico['quantidade_pecas'];
        }
        
        $quantidadeConcluida = (int)$quantidadeConcluida;
        $this->pdo->beginTransaction();
        $sql = "UPDATE servicos SET 
                    status = 'Finalizado', 
                    data_finalizacao = :data_finalizacao,
                    quantidade_concluida = :quantidade_concluida
                WHERE id = :id";
                
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':data_finalizacao' => $dataFinalizacao,
            ':quantidade_concluida' => $quantidadeConcluida,
            ':id' => $servicoId
        ]);
          
        $pagamentoId = null;
        if ($servico['costureira_id']) {            
            $valorTotal = $quantidadeConcluida * $servico['valor_operacao'];            
            $pagamentoId = $this->criarPagamento(
                $servicoId,
                $servico['costureira_id'],
                $quantidadeConcluida,
                $servico['valor_operacao']
            );
            
        }
        
        $this->pdo->commit();
        
        $resultado = [
            'success' => true,
            'pagamento_id' => $pagamentoId,
            'valor_total' => ($quantidadeConcluida * $servico['valor_operacao']) ?? 0
        ];
        
        return $resultado;
        
    } catch (Exception $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        throw $e;
    }
}

private function criarPagamento($servicoId, $costureiraId, $quantidadeConcluida, $valorOperacao)
{
    error_log("--- Iniciando criarPagamento ---");
    
    // Converter para tipos corretos
    $servicoId = (int)$servicoId;
    $costureiraId = (int)$costureiraId;
    $quantidadeConcluida = (int)$quantidadeConcluida;
    $valorOperacao = (float)$valorOperacao;
    
    $valorTotal = $quantidadeConcluida * $valorOperacao;
    $periodoReferencia = date('Y-m-01');
    
    try {
        // Verificar se existe pagamento pendente para este período
        $sql = "SELECT id, valor_bruto, status FROM pagamentos 
                WHERE costureira_id = :costureira_id 
                AND periodo_referencia = :periodo 
                AND status = 'Pendente'
                LIMIT 1";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':costureira_id' => $costureiraId,
            ':periodo' => $periodoReferencia
        ]);
        
        $pagamentoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pagamentoExistente) {
            // Atualizar existente
            $pagamentoId = $pagamentoExistente['id'];
            $novoValor = $pagamentoExistente['valor_bruto'] + $valorTotal;            
            $sqlUpdate = "UPDATE pagamentos SET 
                            valor_bruto = :valor_bruto,
                            valor_liquido = :valor_liquido,
                            updated_at = NOW()
                          WHERE id = :id";
            
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $resultUpdate = $stmtUpdate->execute([
                ':valor_bruto' => $novoValor,
                ':valor_liquido' => $novoValor,
                ':id' => $pagamentoId
            ]);
                        
        } else {
            // Criar novo pagamento            
            $sqlInsert = "INSERT INTO pagamentos (
                            costureira_id,
                            periodo_referencia,
                            valor_bruto,
                            valor_desconto,
                            valor_liquido,
                            status,
                            created_at
                          ) VALUES (
                            :costureira_id,
                            :periodo,
                            :valor_bruto,
                            0,
                            :valor_liquido,
                            'Pendente',
                            NOW()
                          )";
                        
            $params = [
                ':costureira_id' => $costureiraId,
                ':periodo' => $periodoReferencia,
                ':valor_bruto' => $valorTotal,
                ':valor_liquido' => $valorTotal
            ];            
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $resultInsert = $stmtInsert->execute($params);
                        
            if ($resultInsert) {
                $pagamentoId = $this->pdo->lastInsertId();
            } else {
                $errorInfo = $stmtInsert->errorInfo();
                throw new Exception("Falha ao inserir pagamento: " . ($errorInfo[2] ?? 'Erro desconhecido'));
            }
        }
        
        // Inserir item do pagamento
        if ($pagamentoId) {
            // Verificar se item já existe
            $sqlCheckItem = "SELECT id FROM pagamento_itens 
                           WHERE pagamento_id = :pagamento_id 
                           AND servico_id = :servico_id";
            $stmtCheckItem = $this->pdo->prepare($sqlCheckItem);
            $stmtCheckItem->execute([
                ':pagamento_id' => $pagamentoId,
                ':servico_id' => $servicoId
            ]);
            
            $itemExistente = $stmtCheckItem->fetch(PDO::FETCH_ASSOC);
            
            if (!$itemExistente) {
                $sqlInsertItem = "INSERT INTO pagamento_itens (
                                    pagamento_id,
                                    servico_id,
                                    valor_calculado
                                  ) VALUES (
                                    :pagamento_id,
                                    :servico_id,
                                    :valor
                                  )";
                
                $stmtInsertItem = $this->pdo->prepare($sqlInsertItem);
                $resultInsertItem = $stmtInsertItem->execute([
                    ':pagamento_id' => $pagamentoId,
                    ':servico_id' => $servicoId,
                    ':valor' => $valorTotal
                ]);
                
                if (!$resultInsertItem) {
                    $errorInfo = $stmtInsertItem->errorInfo();
                }
            } else {
                error_log("Item já existe, ignorando inserção");
            }
        }
        
        return $pagamentoId;
        
    } catch (Exception $e) {
        error_log("ERRO em criarPagamentoDebug: " . $e->getMessage());
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
    
    // VERIFICAÇÃO DA QUANTIDADE MÁXIMA DE PEÇAS (para edição)
    $loteModel = new LoteModel($this->pdo);
    $validacao = $loteModel->validarQuantidadeServico(
        $dados['lote_id'], 
        $dados['quantidade_pecas'], 
        $dados['operacao_id'],
        $id  // Passa o ID do serviço atual para exclusão na validação
    );
    
    if (isset($validacao['error'])) {
        throw new Exception($validacao['error']);
    }

    // Verificar se a costureira já tem serviço em andamento (excluindo este)
    if ($this->costureiraTemServicoEmAndamento($dados['costureira_id'], $id)) {
        throw new Exception('Esta costureira já possui um serviço em andamento.');
    }
    
    $sql = "UPDATE servicos SET 
                lote_id = :lote_id,
                operacao_id = :operacao_id,
                quantidade_pecas = :quantidade_pecas,
                valor_operacao = :valor_operacao,
                data_envio = :data_envio,
                observacao = :observacao,
                costureira_id = :costureira_id,
                updated_at = NOW()
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

// Criar pagamento pendente automaticamente ao finalizar serviço
private function criarPagamentoPendenteAutomatico($servicoId, $quantidadeConcluida, $valorTotalAjustado)
{
    // Buscar serviço completo com informações da costureira
    $sql = "SELECT s.*, u.id as costureira_id, u.nome as costureira_nome 
            FROM servicos s
            LEFT JOIN usuarios u ON s.costureira_id = u.id
            WHERE s.id = :servico_id";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':servico_id' => $servicoId]);
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$servico || !$servico['costureira_id']) {
        error_log("Serviço {$servicoId} não possui costureira vinculada");
        throw new Exception('Serviço não possui costureira vinculada');
    }
    
    // Determinar período de referência (mês da finalização)
    $periodoReferencia = date('Y-m-01', strtotime($servico['data_finalizacao']));
    
    // Verificar se já existe pagamento pendente para este período
    $sqlCheck = "SELECT id FROM pagamentos 
                 WHERE costureira_id = :costureira_id 
                   AND periodo_referencia = :periodo 
                   AND status = 'Pendente'";
    
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute([
        ':costureira_id' => $servico['costureira_id'],
        ':periodo' => $periodoReferencia
    ]);
    $pagamentoExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    
    if ($pagamentoExistente) {
        // Adicionar ao pagamento existente
        $this->adicionarServicoAoPagamento($pagamentoExistente['id'], $servicoId, $valorTotalAjustado);
        return $pagamentoExistente['id'];
    } else {
        // Criar novo pagamento
        $sql = "INSERT INTO pagamentos (costureira_id, periodo_referencia, valor_bruto, 
                                        valor_desconto, valor_liquido, status, created_at) 
                VALUES (:costureira_id, :periodo_referencia, :valor_bruto, 
                        0, :valor_liquido, 'Pendente', NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':costureira_id' => $servico['costureira_id'],
            ':periodo_referencia' => $periodoReferencia,
            ':valor_bruto' => $valorTotalAjustado,
            ':valor_liquido' => $valorTotalAjustado
        ]);
        
        if (!$result) {
            throw new Exception('Erro ao criar pagamento');
        }
        
        $pagamentoId = $this->pdo->lastInsertId();
        
        // Adicionar item ao pagamento
        $this->adicionarServicoAoPagamento($pagamentoId, $servicoId, $valorTotalAjustado);
        
        return $pagamentoId;
    }
}

// Adicionar serviço a um pagamento existente
private function adicionarServicoAoPagamento($pagamentoId, $servicoId, $valorCalculado)
{
    // Verificar se o serviço já está no pagamento
    $sqlCheck = "SELECT id FROM pagamento_itens 
                 WHERE pagamento_id = :pagamento_id AND servico_id = :servico_id";
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute([
        ':pagamento_id' => $pagamentoId,
        ':servico_id' => $servicoId
    ]);
    
    if ($stmtCheck->fetch()) {
        return; // Já existe
    }
    
    // Adicionar item
    $sql = "INSERT INTO pagamento_itens (pagamento_id, servico_id, valor_calculado) 
            VALUES (:pagamento_id, :servico_id, :valor_calculado)";
    
    $stmt = $this->pdo->prepare($sql);
    $result = $stmt->execute([
        ':pagamento_id' => $pagamentoId,
        ':servico_id' => $servicoId,
        ':valor_calculado' => $valorCalculado
    ]);
    
    if (!$result) {
        throw new Exception('Erro ao adicionar item ao pagamento');
    }
    
    // Atualizar valor bruto e líquido do pagamento
    $sqlUpdate = "UPDATE pagamentos 
                  SET valor_bruto = (SELECT COALESCE(SUM(valor_calculado), 0) FROM pagamento_itens WHERE pagamento_id = :pagamento_id),
                      valor_liquido = (SELECT COALESCE(SUM(valor_calculado), 0) FROM pagamento_itens WHERE pagamento_id = :pagamento_id) - valor_desconto
                  WHERE id = :pagamento_id";
    
    $stmtUpdate = $this->pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([':pagamento_id' => $pagamentoId]);
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

// Buscar serviços finalizados que ainda não foram pagos
public function getServicosFinalizadosNaoPagos()
{
    $sql = "SELECT s.id, s.quantidade_pecas, s.valor_operacao, s.data_finalizacao,
                   (s.quantidade_pecas * s.valor_operacao) as valor_total,
                   s.costureira_id,
                   DATE_FORMAT(s.data_finalizacao, '%Y-%m') as periodo_referencia,
                   u.nome as costureira_nome,
                   o.nome as operacao_nome,
                   l.nome as lote_nome
            FROM servicos s
            INNER JOIN usuarios u ON s.costureira_id = u.id
            INNER JOIN operacoes o ON s.operacao_id = o.id
            INNER JOIN lotes l ON s.lote_id = l.id
            LEFT JOIN pagamento_itens pi ON s.id = pi.servico_id
            WHERE s.status = 'Finalizado'
              AND pi.id IS NULL
            ORDER BY s.data_finalizacao DESC";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Agrupar serviços por costureira e período para criar pagamentos
public function agruparServicosPorPeriodo($servicos)
{
    $grupos = [];
    
    foreach ($servicos as $servico) {
        $chave = $servico['costureira_id'] . '_' . $servico['periodo_referencia'];
        
        if (!isset($grupos[$chave])) {
            $grupos[$chave] = [
                'costureira_id' => $servico['costureira_id'],
                'costureira_nome' => $servico['costureira_nome'],
                'periodo_referencia' => $servico['periodo_referencia'],
                'servicos' => [],
                'valor_bruto' => 0,
                'quantidade_servicos' => 0
            ];
        }
        
        $grupos[$chave]['servicos'][] = $servico;
        $grupos[$chave]['valor_bruto'] += $servico['valor_total'];
        $grupos[$chave]['quantidade_servicos']++;
    }
    
    return $grupos;
}

}
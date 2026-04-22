<?php
namespace App\Models;

use PDO;
use Exception;

class PagamentoModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Criar pagamento automaticamente ao finalizar serviço
    

    // Obter todos os pagamentos com filtros
    public function getPagamentos($filtro = 'todos', $costureiraId = null)
{
    // Primeiro, verificar se a tabela pagamentos existe e tem dados
    $sqlCheck = "SELECT COUNT(*) as total FROM pagamentos";
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute();
    $total = $stmtCheck->fetch(PDO::FETCH_ASSOC)['total'];
    error_log("Total de pagamentos na tabela: " . $total);
    
    $sql = "SELECT p.*, 
                   COALESCE(u.nome, 'Costureira não encontrada') as costureira_nome,
                   u.email as costureira_email,
                   (SELECT COUNT(*) FROM pagamento_itens pi WHERE pi.pagamento_id = p.id) as total_servicos,
                   (SELECT COALESCE(SUM(pi.valor_calculado), 0) FROM pagamento_itens pi WHERE pi.pagamento_id = p.id) as total_calculado
            FROM pagamentos p
            LEFT JOIN usuarios u ON p.costureira_id = u.id";
    
    $where = [];
    $params = [];
    
    if ($filtro === 'pendentes') {
        $where[] = "p.status = 'Pendente'";
    } elseif ($filtro === 'pagos') {
        $where[] = "p.status = 'Pago'";
    } elseif ($filtro === 'cancelados') {
        $where[] = "p.status = 'Cancelado'";
    }
    
    if ($costureiraId) {
        $where[] = "p.costureira_id = :costureira_id";
        $params[':costureira_id'] = $costureiraId;
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " GROUP BY p.id ORDER BY p.periodo_referencia DESC, p.created_at DESC";
    
    error_log("SQL getPagamentos: " . $sql);
    error_log("Params: " . json_encode($params));
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Resultados encontrados: " . count($resultados));
    
    return $resultados;
}

    // Obter pagamentos pendentes (para admin)
    public function getPagamentosPendentes()
    {
        return $this->getPagamentos('pendentes');
    }

    // Obter detalhes de um pagamento específico
    public function getPagamentoPorId($id)
{
    $sql = "SELECT p.*,
                   COALESCE(u.nome, 'Costureira não encontrada') as costureira_nome,
                   u.email as costureira_email,
                   u.chave_pix,
                   u.tipo_chave_pix,
                   u.telefone,
                   e.nome as especialidade_nome
            FROM pagamentos p
            LEFT JOIN usuarios u ON p.costureira_id = u.id
            LEFT JOIN especialidade e ON u.especialidade_id = e.id
            WHERE p.id = :id";
    
    error_log("SQL getPagamentoPorId para ID $id: " . $sql);
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    error_log("Resultado getPagamentoPorId: " . ($resultado ? 'Encontrado' : 'Não encontrado'));
    
    return $resultado;
}

    // Obter itens de um pagamento (serviços)
    public function getItensPagamento($pagamentoId)
    {
        $sql = "SELECT pi.*,
                       s.quantidade_pecas,
                       s.pecas_concluidas,
                       s.valor_operacao,
                       s.data_finalizacao,
                       o.nome as operacao_nome,
                       l.nome as lote_nome,
                       l.colecao
                FROM pagamento_itens pi
                INNER JOIN servicos s ON pi.servico_id = s.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                INNER JOIN lotes l ON s.lote_id = l.id
                WHERE pi.pagamento_id = :pagamento_id
                ORDER BY s.data_finalizacao DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pagamento_id' => $pagamentoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar pagamento efetuado
public function registrarPagamento($id, $dataPagamento, $comprovante = null, $observacao = null)
{
    $sql = "UPDATE pagamentos SET 
                data_pagamento = :data_pagamento,
                status = 'Pago',
                comprovante = :comprovante,
                observacao = CONCAT(IFNULL(observacao, ''), IF(:observacao IS NOT NULL, CONCAT('\n', :observacao), '')),
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id AND status = 'Pendente'";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':id' => $id,
        ':data_pagamento' => $dataPagamento,
        ':comprovante' => $comprovante,
        ':observacao' => $observacao
    ]);
}

    // Cancelar pagamento
public function cancelarPagamento($id, $motivo = null)
{
    // Iniciar transação
    $this->pdo->beginTransaction();
    
    try {
        // Buscar os serviços vinculados a este pagamento
        $sqlServicos = "SELECT servico_id FROM pagamento_itens WHERE pagamento_id = :pagamento_id";
        $stmtServicos = $this->pdo->prepare($sqlServicos);
        $stmtServicos->execute([':pagamento_id' => $id]);
        $servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
        
        // Cancelar o pagamento
        $sql = "UPDATE pagamentos SET 
                    status = 'Cancelado',
                    observacao = CONCAT(IFNULL(observacao, ''), ' | Cancelado: ', :motivo),
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id AND status = 'Pendente'";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':id' => $id,
            ':motivo' => $motivo ?? 'Cancelado pelo administrador'
        ]);
        
        if (!$result) {
            throw new Exception('Erro ao cancelar o pagamento');
        }
        
        // Para cada serviço, reativar (voltar para "Em andamento")
        foreach ($servicos as $servico) {
            $sqlUpdateServico = "UPDATE servicos 
                                 SET status = 'Em andamento', 
                                     data_finalizacao = NULL 
                                 WHERE id = :servico_id";
            $stmtUpdate = $this->pdo->prepare($sqlUpdateServico);
            $stmtUpdate->execute([':servico_id' => $servico['servico_id']]);
        }
        
        // Commit da transação
        $this->pdo->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback em caso de erro
        $this->pdo->rollBack();
        error_log("Erro ao cancelar pagamento: " . $e->getMessage());
        throw $e;
    }
}

    public function reativarServico($id, $dados)
    {        
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
    
    // Obter pagamentos com paginação
public function getPagamentosPaginados($filtro = 'todos', $termo = '', $pagina = 1, $itensPorPagina = 10)
{
    $offset = ($pagina - 1) * $itensPorPagina;
    
    $sql = "SELECT p.*, 
                   COALESCE(u.nome, 'Costureira não encontrada') as costureira_nome,
                   u.email as costureira_email,
                   (SELECT COUNT(*) FROM pagamento_itens pi WHERE pi.pagamento_id = p.id) as total_servicos,
                   (SELECT COALESCE(SUM(pi.valor_calculado), 0) FROM pagamento_itens pi WHERE pi.pagamento_id = p.id) as total_calculado
            FROM pagamentos p
            LEFT JOIN usuarios u ON p.costureira_id = u.id";
    
    $where = [];
    $params = [];
    
    if ($filtro === 'pendentes') {
        $where[] = "p.status = 'Pendente'";
    } elseif ($filtro === 'pagos') {
        $where[] = "p.status = 'Pago'";
    } elseif ($filtro === 'cancelados') {
        $where[] = "p.status = 'Cancelado'";
    }
    
    if (!empty($termo)) {
        $where[] = "(u.nome LIKE :termo OR p.periodo_referencia LIKE :termo OR p.status LIKE :termo)";
        $params[':termo'] = "%{$termo}%";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " GROUP BY p.id ORDER BY p.periodo_referencia DESC, p.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $this->pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Contar total de pagamentos para paginação
public function getTotalPagamentos($filtro = 'todos', $termo = '')
{
    $sql = "SELECT COUNT(DISTINCT p.id) as total
            FROM pagamentos p
            LEFT JOIN usuarios u ON p.costureira_id = u.id";
    
    $where = [];
    $params = [];
    
    if ($filtro === 'pendentes') {
        $where[] = "p.status = 'Pendente'";
    } elseif ($filtro === 'pagos') {
        $where[] = "p.status = 'Pago'";
    } elseif ($filtro === 'cancelados') {
        $where[] = "p.status = 'Cancelado'";
    }
    
    if (!empty($termo)) {
        $where[] = "(u.nome LIKE :termo OR p.periodo_referencia LIKE :termo OR p.status LIKE :termo)";
        $params[':termo'] = "%{$termo}%";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['total'] ?? 0;
}

// Estornar pagamento (voltar de Pago para Pendente)
public function estornarPagamento($id)
{
    // Iniciar transação
    $this->pdo->beginTransaction();
    
    try {
        // Buscar o pagamento para verificar status atual
        $sqlCheck = "SELECT status FROM pagamentos WHERE id = :id";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $id]);
        $pagamento = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if (!$pagamento) {
            throw new Exception('Pagamento não encontrado');
        }
        
        if ($pagamento['status'] !== 'Pago') {
            throw new Exception('Apenas pagamentos com status "Pago" podem ser estornados');
        }
        
        // Atualizar o pagamento para Pendente
        $sql = "UPDATE pagamentos SET 
                    status = 'Pendente',
                    data_pagamento = NULL,
                    comprovante = NULL,
                    observacao = CONCAT(IFNULL(observacao, ''), ' | Estornado em: ', NOW()),
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([':id' => $id]);
        
        if (!$result) {
            throw new Exception('Erro ao estornar o pagamento');
        }
        
        // Commit da transação
        $this->pdo->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback em caso de erro
        $this->pdo->rollBack();
        error_log("Erro ao estornar pagamento: " . $e->getMessage());
        throw $e;
    }
}

// Exportar todos os pagamentos (para CSV/Excel)
public function exportarPagamentos($filtro = 'todos', $termo = '')
{
    $sql = "SELECT p.*, 
                   COALESCE(u.nome, 'Costureira não encontrada') as costureira_nome,
                   u.email as costureira_email,
                   u.telefone,
                   (SELECT COUNT(*) FROM pagamento_itens pi WHERE pi.pagamento_id = p.id) as total_servicos,
                   (SELECT COALESCE(SUM(pi.valor_calculado), 0) FROM pagamento_itens pi WHERE pi.pagamento_id = p.id) as total_calculado
            FROM pagamentos p
            LEFT JOIN usuarios u ON p.costureira_id = u.id";
    
    $where = [];
    $params = [];
    
    if ($filtro === 'pendentes') {
        $where[] = "p.status = 'Pendente'";
    } elseif ($filtro === 'pagos') {
        $where[] = "p.status = 'Pago'";
    } elseif ($filtro === 'cancelados') {
        $where[] = "p.status = 'Cancelado'";
    }
    
    if (!empty($termo)) {
        $where[] = "(u.nome LIKE :termo OR p.periodo_referencia LIKE :termo)";
        $params[':termo'] = "%{$termo}%";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " GROUP BY p.id ORDER BY p.periodo_referencia DESC, p.created_at DESC";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter resumo financeiro
    public function getResumoFinanceiro()
    {
        $sql = "SELECT 
                    SUM(CASE WHEN status = 'Pendente' THEN valor_bruto ELSE 0 END) as total_pendente,
                    SUM(CASE WHEN status = 'Pago' THEN valor_bruto ELSE 0 END) as total_pago,
                    SUM(CASE WHEN status = 'Cancelado' THEN valor_bruto ELSE 0 END) as total_cancelado,
                    COUNT(CASE WHEN status = 'Pendente' THEN 1 END) as qtd_pendente,
                    COUNT(CASE WHEN status = 'Pago' THEN 1 END) as qtd_pago,
                    COUNT(CASE WHEN status = 'Cancelado' THEN 1 END) as qtd_cancelado
                FROM pagamentos";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar pagamentos por termo
    public function buscarPagamentos($termo)
    {
        $sql = "SELECT p.*,
                       u.nome as costureira_nome
                FROM pagamentos p
                INNER JOIN usuarios u ON p.costureira_id = u.id
                WHERE u.nome LIKE :termo 
                   OR p.periodo_referencia LIKE :termo
                   OR p.status LIKE :termo
                ORDER BY p.periodo_referencia DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $termoBusca = "%{$termo}%";
        $stmt->execute([':termo' => $termoBusca]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =============================================
    // MÓDULO DE LUCRO E FINANCEIRO
    // =============================================

    // Obter lotes entregues (faturamento)
    public function getLotesEntregues($ano = null, $mes = null)
    {
        $sql = "SELECT l.*, e.nome as empresa_nome,
                       (SELECT SUM(pecas.quantidade * pecas.valor_unitario) 
                        FROM pecas 
                        WHERE pecas.lote_id = l.id) as valor_total_calculado
                FROM lotes l
                INNER JOIN empresas e ON l.empresa_id = e.id
                WHERE l.status = 'Entregue'";
        
        $params = [];
        
        if ($ano && $mes) {
            $sql .= " AND MONTH(l.data_entrega) = :mes AND YEAR(l.data_entrega) = :ano";
            $params[':mes'] = $mes;
            $params[':ano'] = $ano;
        } elseif ($ano) {
            $sql .= " AND YEAR(l.data_entrega) = :ano";
            $params[':ano'] = $ano;
        }
        
        $sql .= " ORDER BY l.data_entrega DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obter total de receita (lotes entregues)
    public function getReceitaTotal($ano = null, $mes = null)
{
    $sql = "SELECT SUM(valor_total) as total FROM lotes WHERE status = 'Entregue'";
    
    $params = [];
    
    if ($ano && $mes) {
        $sql .= " AND MONTH(data_entrega) = :mes AND YEAR(data_entrega) = :ano";
        $params[':mes'] = $mes;
        $params[':ano'] = $ano;
    } elseif ($ano) {
        $sql .= " AND YEAR(data_entrega) = :ano";
        $params[':ano'] = $ano;
    }
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    error_log("Receita total para ano=$ano, mes=$mes: " . ($result['total'] ?? 0));
    
    return $result['total'] ?? 0;
}

    // Obter total de despesas (pagamentos realizados)
    public function getDespesaTotal($ano = null, $mes = null)
    {
        $sql = "SELECT SUM(valor_bruto) as total 
                FROM pagamentos 
                WHERE status = 'Pago'";
        
        $params = [];
        
        if ($ano && $mes) {
            $sql .= " AND MONTH(periodo_referencia) = :mes AND YEAR(periodo_referencia) = :ano";
            $params[':mes'] = $mes;
            $params[':ano'] = $ano;
        } elseif ($ano) {
            $sql .= " AND YEAR(periodo_referencia) = :ano";
            $params[':ano'] = $ano;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Calcular lucro líquido
    public function calcularLucro($ano = null, $mes = null)
    {
        $receita = $this->getReceitaTotal($ano, $mes);
        $despesa = $this->getDespesaTotal($ano, $mes);
        
        return [
            'receita' => $receita,
            'despesa' => $despesa,
            'lucro' => $receita - $despesa,
            'margem' => $receita > 0 ? (($receita - $despesa) / $receita) * 100 : 0
        ];
    }

    // Rentabilidade por lote
    public function getRentabilidadePorLote()
    {
        $sql = "SELECT l.id, l.nome, l.colecao, l.valor_total as valor_lote,
                       e.nome as empresa_nome,
                       (SELECT COALESCE(SUM(pi.valor_calculado), 0) 
                        FROM pagamento_itens pi
                        INNER JOIN servicos s ON pi.servico_id = s.id
                        WHERE s.lote_id = l.id
                        AND pi.pagamento_id IN (SELECT id FROM pagamentos WHERE status = 'Pago')) as custo_total,
                       l.data_entrega
                FROM lotes l
                INNER JOIN empresas e ON l.empresa_id = e.id
                WHERE l.status = 'Entregue'
                ORDER BY l.data_entrega DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($lotes as &$lote) {
            $lote['lucro'] = $lote['valor_lote'] - $lote['custo_total'];
            $lote['margem'] = $lote['valor_lote'] > 0 ? ($lote['lucro'] / $lote['valor_lote']) * 100 : 0;
        }
        
        return $lotes;
    }

    // Obter estatísticas do dashboard financeiro
    public function getEstatisticasFinanceiras()
    {
        // Total de lotes entregues
        $sql = "SELECT COUNT(*) as total FROM lotes WHERE status = 'Entregue'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $totalLotes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de pagamentos realizados
        $sql = "SELECT COUNT(*) as total FROM pagamentos WHERE status = 'Pago'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $totalPagamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Lucro total acumulado
        $lucroTotal = $this->calcularLucro();
        
        // Melhor mês
        $lucroMensal = $this->getLucroPorMes();
        $melhorMes = !empty($lucroMensal) ? max($lucroMensal, function($a, $b) {
            return $a['lucro'] <=> $b['lucro'];
        }) : null;
        
        return [
            'total_lotes_entregues' => $totalLotes,
            'total_pagamentos' => $totalPagamentos,
            'lucro_total' => $lucroTotal['lucro'],
            'receita_total' => $lucroTotal['receita'],
            'margem_media' => $lucroTotal['margem'],
            'melhor_mes' => $melhorMes
        ];
    }

    // Obter anos com dados para filtro
public function getAnosComDados()
{
    $sql = "SELECT DISTINCT YEAR(periodo_referencia) as ano 
            FROM pagamentos 
            UNION 
            SELECT DISTINCT YEAR(data_entrega) as ano 
            FROM lotes 
            WHERE status = 'Entregue'
            ORDER BY ano DESC";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $anos = [];
    foreach ($resultados as $r) {
        if ($r['ano']) {
            $anos[] = $r['ano'];
        }
    }
    
    // Garantir que o ano atual está na lista
    $anoAtual = date('Y');
    if (!in_array($anoAtual, $anos)) {
        $anos[] = $anoAtual;
        rsort($anos);
    }
    
    return $anos;
}

// Obter lucro por mês até uma data específica
public function getLucroPorMesAte($ano, $mes)
{
    $lucroMensal = [];
    
    // Criar data alvo
    $dataAlvo = new \DateTime("$ano-$mes-01");
    
    // Pegar últimos 11 meses + o mês selecionado
    for ($i = 11; $i >= 0; $i--) {
        $data = clone $dataAlvo;
        $data->modify("-$i months");
        $anoMes = $data->format('Y');
        $mesNum = $data->format('m');
        $mesNome = $data->format('M/Y');
        
        $receita = $this->getReceitaTotal($anoMes, $mesNum);
        $despesa = $this->getDespesaTotal($anoMes, $mesNum);
        
        $lucroMensal[] = [
            'mes' => $mesNome,
            'ano' => $anoMes,
            'mes_num' => $mesNum,
            'receita' => $receita,
            'despesa' => $despesa,
            'lucro' => $receita - $despesa
        ];
    }
    
    return $lucroMensal;
}

// Sobrescrever getLucroPorMes para aceitar ano específico
public function getLucroPorMes($ano = null)
{
    $lucroMensal = [];
    $ano = $ano ?? date('Y');
    
    for ($mes = 1; $mes <= 12; $mes++) {
        $receita = $this->getReceitaTotal($ano, $mes);
        $despesa = $this->getDespesaTotal($ano, $mes);
        $mesNome = date('M/Y', strtotime("$ano-$mes-01"));
        
        $lucroMensal[] = [
            'mes' => $mesNome,
            'ano' => $ano,
            'mes_num' => $mes,
            'receita' => $receita,
            'despesa' => $despesa,
            'lucro' => $receita - $despesa
        ];
    }
    
    return $lucroMensal;
}

// Obter totais por mês para o gráfico de barras
public function getTotaisPorMes($ano, $mes = null)
{
    if ($mes) {
        // Para um mês específico, pegar últimos 12 meses
        return $this->getLucroPorMesAte($ano, $mes);
    } else {
        // Para ano completo, pegar todos os meses do ano
        return $this->getLucroPorMes($ano);
    }
}

// Obter pagamentos de uma costureira
    public function getPagamentosPorCostureira($costureiraId)
    {
        $sql = "SELECT p.*,
                       COUNT(pi.id) as quantidade_servicos,
                       GROUP_CONCAT(DISTINCT s.id SEPARATOR ', ') as servicos_ids
                FROM pagamentos p
                LEFT JOIN pagamento_itens pi ON p.id = pi.pagamento_id
                LEFT JOIN servicos s ON pi.servico_id = s.id
                WHERE p.costureira_id = :costureira_id
                GROUP BY p.id
                ORDER BY p.periodo_referencia DESC, p.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':costureira_id' => $costureiraId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Calcular pagamento do mês para uma costureira
    public function calcularPagamentoMes($costureiraId)
    {
        $primeiroDia = date('Y-m-01');
        $ultimoDia = date('Y-m-t');

        $sql = "SELECT COALESCE(SUM(s.quantidade_pecas * s.valor_operacao), 0) as total
                FROM servicos s
                WHERE s.costureira_id = :costureira_id 
                AND s.status = 'Finalizado'
                AND s.data_finalizacao BETWEEN :data_inicio AND :data_fim";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':costureira_id' => $costureiraId,
            ':data_inicio' => $primeiroDia,
            ':data_fim' => $ultimoDia
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Contar serviços com entrega próxima (3 dias)
    public function contarProximasEntregas($costureiraId)
    {
        $hoje = date('Y-m-d');
        $limite = date('Y-m-d', strtotime('+3 days'));

        $sql = "SELECT COUNT(*) as total
                FROM servicos s
                INNER JOIN lotes l ON s.lote_id = l.id
                WHERE s.costureira_id = :costureira_id 
                AND s.status = 'Em andamento'
                AND l.data_entrega BETWEEN :hoje AND :limite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':costureira_id' => $costureiraId,
            ':hoje' => $hoje,
            ':limite' => $limite
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

}
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

    // Obter detalhes de um pagamento específico
    public function getPagamentoPorId($id)
    {
        $sql = "SELECT p.*,
                       u.nome as costureira_nome,
                       u.email as costureira_email,
                       u.chave_pix,
                       u.tipo_chave_pix
                FROM pagamentos p
                INNER JOIN usuarios u ON p.costureira_id = u.id
                WHERE p.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obter itens de um pagamento
    public function getItensPagamento($pagamentoId)
    {
        $sql = "SELECT pi.*,
                       s.quantidade_pecas,
                       s.valor_operacao,
                       o.nome as operacao_nome,
                       l.nome as lote_nome
                FROM pagamento_itens pi
                INNER JOIN servicos s ON pi.servico_id = s.id
                INNER JOIN operacoes o ON s.operacao_id = o.id
                INNER JOIN lotes l ON s.lote_id = l.id
                WHERE pi.pagamento_id = :pagamento_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pagamento_id' => $pagamentoId]);
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
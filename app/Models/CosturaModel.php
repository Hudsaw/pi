<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class CosturaModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function calcularPagamentoMes($costureiraId)
    {
        // Calcular pagamento estimado do mês atual
        $sql = "SELECT SUM(s.valor_operacao * s.quantidade_pecas) as total
                FROM servicos s
                INNER JOIN servico_costureiras sc ON s.id = sc.servico_id
                WHERE sc.costureira_id = :costureira_id 
                AND s.status = 'Em andamento'
                AND MONTH(sc.data_inicio) = MONTH(CURRENT_DATE())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':costureira_id' => $costureiraId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }

    public function contarProximasEntregas($costureiraId)
    {
        // Contar serviços com entrega nos próximos 7 dias
        $sql = "SELECT COUNT(*) as total
                FROM servico_costureiras sc
                INNER JOIN servicos s ON sc.servico_id = s.id
                WHERE sc.costureira_id = :costureira_id 
                AND s.status = 'Em andamento'
                AND sc.data_entrega BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':costureira_id' => $costureiraId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }

}
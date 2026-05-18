<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Pagamentos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 20px;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .info {
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .info .linha {
            margin-bottom: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background: #ddd;
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .status-pendente { background: #ffeaa7; }
        .status-pago { background: #d5f4e6; }
        .status-cancelado { background: #fadbd8; }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        .totals {
            margin-top: 15px;
            text-align: right;
            font-weight: bold;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Pagamentos</h1>
        <p>Sistema PontoCerto</p>
    </div>
    
    <div class="info">
        <div class="linha"><strong>Data de emissão:</strong> <?= date('d/m/Y H:i:s') ?></div>
        <div class="linha"><strong>Filtro:</strong> <?= ucfirst($filtro) ?></div>
        <?php if (!empty($termoBusca)): ?>
        <div class="linha"><strong>Busca:</strong> <?= htmlspecialchars($termoBusca) ?></div>
        <?php endif; ?>
        <div class="linha"><strong>Total de registros:</strong> <?= count($pagamentos) ?></div>
    </div>
    
    <table cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Costureira</th>
                <th>Período</th>
                <th>Serviços</th>
                <th class="text-right">Valor Bruto</th>
                <th class="text-right">Valor Líquido</th>
                <th>Status</th>
                <th>Data Pagamento</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pagamentos)): ?>
            <tr>
                <td colspan="8" class="text-center">Nenhum pagamento encontrado</td>
            </tr>
            <?php else: ?>
                <?php 
                $totalBruto = 0;
                $totalLiquido = 0;
                foreach ($pagamentos as $p): 
                    $totalBruto += $p['valor_bruto'];
                    $totalLiquido += ($p['valor_liquido'] ?? $p['valor_bruto']);
                ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['costureira_nome'] ?? '-') ?></td>
                    <td><?= date('m/Y', strtotime($p['periodo_referencia'])) ?></td>
                    <td><?= $p['total_servicos'] ?? 0 ?></td>
                    <td class="text-right">R$ <?= number_format($p['valor_bruto'] ?? 0, 2, ',', '.') ?></td>
                    <td class="text-right">R$ <?= number_format($p['valor_liquido'] ?? $p['valor_bruto'] ?? 0, 2, ',', '.') ?></td>
                    <td class="text-center">
                        <span class="status status-<?= strtolower($p['status']) ?>">
                            <?= $p['status'] ?>
                        </span>
                    </td>
                    <td class="text-center"><?= $p['data_pagamento'] ? date('d/m/Y', strtotime($p['data_pagamento'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background: #f0f0f0; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAIS:</td>
                <td class="text-right">R$ <?= number_format($totalBruto ?? 0, 2, ',', '.') ?></td>
                <td class="text-right">R$ <?= number_format($totalLiquido ?? 0, 2, ',', '.') ?></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema PontoCerto</p>
        <button class="no-print" onclick="window.print()" style="margin-top: 10px; padding: 5px 15px; cursor: pointer;">
            🖨️ Imprimir / Salvar PDF
        </button>
    </div>
    
    <script>
        // Opcional: imprimir automaticamente ao carregar
        // window.print();
    </script>
</body>
</html>
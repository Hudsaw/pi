<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="formulario-cadastro">
        <div class="flex" style="justify-content: space-between; align-items: center;">
            <h2>Detalhes do Pagamento - <?= date('m/Y', strtotime($pagamento['periodo_referencia'] ?? 'now')) ?></h2>
            <a href="<?= BASE_URL ?>costura/pagamentos" class="botao">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        <hr class="shadow">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Cabeçalho com status -->
        <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Período: <?= date('F/Y', strtotime($pagamento['periodo_referencia'] ?? 'now')) ?></h3>
            <?php
            $statusClass = '';
            $statusText = $pagamento['status'] ?? 'Pendente';
            
            if ($statusText == 'Pago') {
                $statusClass = 'completed';
            } elseif ($statusText == 'Pendente') {
                $statusClass = 'in-progress';
            } elseif ($statusText == 'Cancelado') {
                $statusClass = 'cancelled';
            }
            ?>
            <span class="status-badge <?= $statusClass ?>" style="font-size: 1em; padding: 8px 15px;">
                <?= $statusText ?>
            </span>
        </div>

        <!-- Informações principais em duas colunas -->
        <span class="lista-informacoes flex center" style="margin-bottom: 30px;">
            <span class="lista-informacoes-coluna bold flex vertical" style="width: 200px;">
                <span class="flex v-center">Data de criação:</span>
                <span class="flex v-center">Data de pagamento:</span>
                <span class="flex v-center">Valor bruto:</span>
                <?php if (($pagamento['valor_desconto'] ?? 0) > 0): ?>
                <span class="flex v-center">Descontos:</span>
                <?php endif; ?>
                <span class="flex v-center">Valor líquido:</span>
                <span class="flex v-center">Serviços incluídos:</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($pagamento['created_at'] ?? 'now')) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= !empty($pagamento['data_pagamento']) ? date('d/m/Y', strtotime($pagamento['data_pagamento'])) : '-' ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($pagamento['valor_bruto'] ?? 0, 2, ',', '.') ?></span>
                <?php if (($pagamento['valor_desconto'] ?? 0) > 0): ?>
                <span class="flex v-center" style="min-height:20px; color: #dc3545;">- R$ <?= number_format($pagamento['valor_desconto'], 2, ',', '.') ?></span>
                <?php endif; ?>
                <span class="flex v-center" style="min-height:20px; font-weight: bold;">R$ <?= number_format($pagamento['valor_liquido'] ?? 0, 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= count($itens ?? []) ?> serviço(s)</span>
            </span>
        </span>

        <!-- Motivo do desconto (se houver) -->
        <?php if (!empty($pagamento['motivo_desconto'])): ?>
        <div style="margin-bottom: 30px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #ffc107; border-radius: 4px;">
            <h4 style="margin-bottom: 5px;">Motivo do desconto:</h4>
            <p style="margin: 0;"><?= htmlspecialchars($pagamento['motivo_desconto']) ?></p>
        </div>
        <?php endif; ?>

        <!-- Informações PIX -->
        <div style="margin-bottom: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
            <h4 style="margin-bottom: 15px;">Informações para pagamento (PIX)</h4>
            <div class="flex" style="gap: 30px;">
                <div>
                    <strong>Chave PIX:</strong><br>
                    <span><?= htmlspecialchars($pagamento['chave_pix'] ?? 'Não informada') ?></span>
                </div>
                <div>
                    <strong>Tipo de chave:</strong><br>
                    <span><?= htmlspecialchars($pagamento['tipo_chave_pix'] ?? 'Não informado') ?></span>
                </div>
            </div>
        </div>

        <!-- Tabela de serviços incluídos -->
        <h4 style="margin-bottom: 15px;">Serviços incluídos neste pagamento</h4>

        <?php if (empty($itens)): ?>
            <p class="no-data">Nenhum serviço encontrado para este pagamento.</p>
        <?php else: ?>
            <div class="tabela" style="margin-bottom: 30px;">
                <table cellspacing='0' class="redondinho shadow">
                    <thead>
                        <tr>
                            <th class="ae">Lote</th>
                            <th class="ae">Operação</th>
                            <th class="ae">Qtd. Peças</th>
                            <th class="ae">Valor Unit.</th>
                            <th class="ae">Valor Total</th>
                            <th class="ae">Data Envio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                        <tr>
                            <td class="ae"><?= htmlspecialchars($item['lote_nome'] ?? 'N/A') ?></td>
                            <td class="ae"><?= htmlspecialchars($item['operacao_nome'] ?? 'N/A') ?></td>
                            <td class="ae"><?= number_format($item['quantidade_pecas'] ?? 0, 0, ',', '.') ?></td>
                            <td class="ae">R$ <?= number_format($item['valor_operacao'] ?? 0, 2, ',', '.') ?></td>
                            <td class="ae">R$ <?= number_format(($item['quantidade_pecas'] ?? 0) * ($item['valor_operacao'] ?? 0), 2, ',', '.') ?></td>
                            <td class="ae"><?= isset($item['data_envio']) ? date('d/m/Y', strtotime($item['data_envio'])) : 'N/A' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot style="background-color: #f8f9fa; font-weight: bold;">
                        <tr>
                            <td colspan="4" class="ar" style="padding-right: 20px;">Total:</td>
                            <td class="ae">R$ <?= number_format($pagamento['valor_bruto'] ?? 0, 2, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>

        <!-- Comprovante (se disponível) -->
        <?php if (($pagamento['status'] ?? '') == 'Pago' && !empty($pagamento['comprovante'])): ?>
        <div class="flex h-center" style="margin-top: 20px;">
            <a href="<?= BASE_URL ?>uploads/comprovantes/<?= $pagamento['comprovante'] ?>" target="_blank" class="botao-azul">
                <i class="fas fa-file-pdf"></i> Visualizar comprovante
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
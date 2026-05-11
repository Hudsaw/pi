<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="formulario-cadastro">
        <div class="titulo">Pagamento #<?= $pagamento['id'] ?></div>
        <hr class="shadow">
        
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">Costureira</span>
                <span class="flex v-center">Período de Referência</span>
                <span class="flex v-center">Valor Bruto</span>
                <span class="flex v-center">Desconto</span>
                <span class="flex v-center">Valor Líquido</span>
                <span class="flex v-center">Status</span>
                <?php if ($pagamento['motivo_desconto']): ?>
                <span class="flex v-center">Motivo do Desconto</span>
                <?php endif; ?>
                <?php if ($pagamento['data_pagamento']): ?>
                <span class="flex v-center">Data do Pagamento</span>
                <?php endif; ?>
                <?php if ($pagamento['comprovante']): ?>
                <span class="flex v-center">Comprovante</span>
                <?php endif; ?>
                <?php if ($pagamento['observacao']): ?>
                <span class="flex v-center">Observação</span>
                <?php endif; ?>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($pagamento['costureira_nome']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= date('m/Y', strtotime($pagamento['periodo_referencia'])) ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($pagamento['valor_bruto'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($pagamento['valor_desconto'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($pagamento['valor_liquido'], 2, ',', '.') ?></span>
                <span class="flex v-center status-<?= strtolower($pagamento['status']) ?>" style="min-height:20px"><?= $pagamento['status'] ?></span>
                <?php if ($pagamento['motivo_desconto']): ?>
                <span class="flex v-center" style="min-height:20px"><?= nl2br(htmlspecialchars($pagamento['motivo_desconto'])) ?></span>
                <?php endif; ?>
                <?php if ($pagamento['data_pagamento']): ?>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($pagamento['data_pagamento'])) ?></span>
                <?php endif; ?>
                <?php if ($pagamento['comprovante']): ?>
                <span class="flex v-center" style="min-height:20px">
                    <a href="<?= UPLOADS_URL ?>comprovantes/<?= $pagamento['comprovante'] ?>" 
                       target="_blank" class="btn-visualizar">Visualizar Comprovante</a>
                </span>
                <?php endif; ?>
                <?php if ($pagamento['observacao']): ?>
                <span class="flex v-center" style="min-height:20px"><?= nl2br(htmlspecialchars($pagamento['observacao'])) ?></span>
                <?php endif; ?>
            </span>
        </span>
        
        <div class="tabela mt-20">
            <h3>Serviços Incluídos neste Pagamento</h3>
            <table cellspacing='0' class="redondinho shadow">
                <thead>
                    <tr>
                        <th class="ae">Lote</th>
                        <th class="ae">Operação</th>
                        <th class="ae">Qtd. Peças (Total)</th>
                        <th class="ae">Qtd. Concluída</th>
                        <th class="ae">Valor Unitário</th>
                        <th class="ae">Valor Total</th>
                        <th class="ae">Data Finalização</th>
                        <th class="ae">Perdas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagamento['itens'])): ?>
                        <tr>
                            <td colspan="8" class="ac">Nenhum serviço encontrado</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $totalPecasConcluidas = 0;
                        $totalPecasPerdidas = 0;
                        ?>
                        <?php foreach ($pagamento['itens'] as $item): ?>
                            <?php 
                            $pecasConcluidas = $item['quantidade_concluida'] ?? $item['quantidade_pecas'];
                            $pecasPerdidas = $item['quantidade_pecas'] - $pecasConcluidas;
                            $totalPecasConcluidas += $pecasConcluidas;
                            $totalPecasPerdidas += $pecasPerdidas;
                            ?>
                            <tr>
                                <td class="ae"><?= htmlspecialchars($item['lote_nome']) ?></td>
                                <td class="ae"><?= htmlspecialchars($item['operacao_nome']) ?></td>
                                <td class="ae"><?= number_format($item['quantidade_pecas'], 0, ',', '.') ?></td>
                                <td class="ae">
                                    <strong><?= number_format($pecasConcluidas, 0, ',', '.') ?></strong>
                                    <?php if ($pecasPerdidas > 0): ?>
                                        <span class="texto-vermelho">(<?= number_format($pecasPerdidas, 0, ',', '.') ?> perdidas)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="ae">R$ <?= number_format($item['valor_operacao'], 2, ',', '.') ?></td>
                                <td class="ae">R$ <?= number_format($item['valor_calculado'], 2, ',', '.') ?></td>
                                <td class="ae"><?= date('d/m/Y', strtotime($item['data_finalizacao'])) ?></td>
                                <td class="ae">
                                    <?php if ($pecasPerdidas > 0): ?>
                                        <span class="texto-vermelho"><?= number_format($pecasPerdidas, 0, ',', '.') ?> peças</span>
                                    <?php else: ?>
                                        <span class="texto-verde">Nenhuma</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="3" class="ae bold">Totais</td>
                            <td class="ae bold"><?= number_format($totalPecasConcluidas, 0, ',', '.') ?> peças</td>
                            <td colspan="2"></td>
                            <td colspan="2" class="ae bold">Perdas: <?= number_format($totalPecasPerdidas, 0, ',', '.') ?> peças</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/pagamentos" class="botao">Voltar</a>
            <?php if ($pagamento['status'] === 'Pendente'): ?>
                <a href="<?= BASE_URL ?>admin/editar-pagamento?id=<?= $pagamento['id'] ?>" class="botao">Editar</a>
                <a href="<?= BASE_URL ?>admin/finalizar-pagamento?id=<?= $pagamento['id'] ?>" 
           onclick="return confirm('Confirmar pagamento de R$ <?= number_format($pagamento['valor_liquido'], 2, ',', '.') ?>?')"
           class="btn-pagar" title="Pagar">
            <img class="icone" src="<?= ASSETS_URL ?>icones/pagar.svg" alt="pagar">
        </a>
                <a href="<?= BASE_URL ?>admin/cancelar-pagamento?id=<?= $pagamento['id'] ?>" 
                   onclick="return confirm('Tem certeza que deseja cancelar este pagamento?')"
                   class="botao-remover">Cancelar Pagamento</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para pagar -->
<div id="modalPagar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; width: 90%; max-width: 500px;">
        <div style="padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Confirmar Pagamento</h3>
            <span onclick="fecharModalPagar()" style="font-size: 28px; cursor: pointer;">&times;</span>
        </div>
        <form method="POST" action="<?= BASE_URL ?>admin/finalizar-pagamento?id=<?= $pagamento['id'] ?>" enctype="multipart/form-data">
            <div style="padding: 20px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Costureira:</label>
                    <strong><?= htmlspecialchars($pagamento['costureira_nome']) ?></strong>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Valor a Pagar:</label>
                    <strong style="font-size: 18px; color: #28a745;">R$ <?= number_format($pagamento['valor_liquido'], 2, ',', '.') ?></strong>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="data_pagamento" style="display: block; margin-bottom: 5px; font-weight: bold;">Data do Pagamento *</label>
                    <input type="date" name="data_pagamento" id="data_pagamento" 
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                           value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="comprovante" style="display: block; margin-bottom: 5px; font-weight: bold;">Comprovante</label>
                    <input type="file" name="comprovante" id="comprovante" 
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small style="color: #666;">Formatos permitidos: PDF, JPG, PNG (máx. 5MB)</small>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="observacao_pagamento" style="display: block; margin-bottom: 5px; font-weight: bold;">Observação</label>
                    <textarea name="observacao_pagamento" id="observacao_pagamento" 
                              style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 80px;"
                              placeholder="Observações sobre o pagamento..."></textarea>
                </div>
            </div>
            <div style="padding: 15px 20px; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="fecharModalPagar()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="submit" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Confirmar Pagamento</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalPagar() {
    document.getElementById('modalPagar').style.display = 'block';
}

function fecharModalPagar() {
    document.getElementById('modalPagar').style.display = 'none';
}
</script>
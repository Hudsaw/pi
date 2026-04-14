<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
<div class="conteudo-formulario">
    <div class="cabecalho-formulario">
        <h2>Registrar Pagamento</h2>
        <div class="acoes">
            <a href="<?= BASE_URL ?>admin/pagamentos" class="botao-cinza">Voltar</a>
        </div>
    </div>
    
    <?php if (isset($pagamento) && $pagamento): ?>
        <div class="info-pagamento-resumo">
            <div class="info-card">
                <div class="info-titulo">Costureira</div>
                <div class="info-conteudo"><?= htmlspecialchars($pagamento['costureira_nome']) ?></div>
                <div class="info-sub">Período: <?= date('m/Y', strtotime($pagamento['periodo_referencia'])) ?></div>
            </div>
            
            <div class="info-card destaque">
                <div class="info-titulo">Valor a Pagar</div>
                <div class="info-conteudo texto-verde">
                    R$ <?= number_format($pagamento['valor_liquido'] ?? $pagamento['valor_bruto'], 2, ',', '.') ?>
                </div>
                <div class="info-sub">Valor Bruto: R$ <?= number_format($pagamento['valor_bruto'], 2, ',', '.') ?></div>
                <?php if (($pagamento['valor_desconto'] ?? 0) > 0): ?>
                    <div class="info-sub texto-vermelho">Desconto: - R$ <?= number_format($pagamento['valor_desconto'], 2, ',', '.') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="info-card">
                <div class="info-titulo">Dados para Pagamento</div>
                <div class="info-conteudo">
                    <?php if ($pagamento['tipo_chave_pix'] && $pagamento['chave_pix']): ?>
                        <p><strong>PIX:</strong> <?= ucfirst($pagamento['tipo_chave_pix']) ?> - <?= htmlspecialchars($pagamento['chave_pix']) ?></p>
                    <?php endif; ?>
                    <?php if ($pagamento['telefone']): ?>
                        <p><strong>Telefone:</strong> <?= htmlspecialchars($pagamento['telefone']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="form-container">
            <form method="POST" action="<?= BASE_URL ?>admin/registrar-pagamento" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $pagamento['id'] ?>">
                
                <div class="form-group">
                    <label for="data_pagamento">Data do Pagamento *</label>
                    <input type="date" name="data_pagamento" id="data_pagamento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="comprovante">Comprovante de Pagamento</label>
                    <input type="file" name="comprovante" id="comprovante" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Arquivos permitidos: PDF, JPG, PNG (máx. 5MB)</small>
                </div>
                
                <div class="form-group">
                    <label for="observacao">Observação (opcional)</label>
                    <textarea name="observacao" id="observacao" class="form-control" rows="3" placeholder="Informações adicionais sobre o pagamento..."></textarea>
                </div>
                
                <div class="servicos-pagamento">
                    <h3>Serviços Incluídos neste Pagamento</h3>
                    <div class="tabela">
                        <table cellspacing='0' class="redondinho shadow">
                            <thead>
                                <tr>
                                    <th class="ae">Lote</th>
                                    <th class="ae">Coleção</th>
                                    <th class="ae">Operação</th>
                                    <th class="ae">Quantidade</th>
                                    <th class="ae">Valor Unitário</th>
                                    <th class="ae">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $itens = $pagamento['itens'] ?? [];
                                foreach ($itens as $item): 
                                ?>
                                    <tr>
                                        <td class="ae"><?= htmlspecialchars($item['lote_nome'] ?? 'N/A') ?></td>
                                        <td class="ae"><?= htmlspecialchars($item['colecao'] ?? 'N/A') ?></td>
                                        <td class="ae"><?= htmlspecialchars($item['operacao_nome'] ?? 'N/A') ?></td>
                                        <td class="ae"><?= $item['quantidade_pecas'] ?? 0 ?> peças</td>
                                        <td class="ae">R$ <?= number_format($item['valor_operacao'] ?? 0, 2, ',', '.') ?></td>
                                        <td class="ae">R$ <?= number_format($item['valor_calculado'] ?? 0, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="5" class="ae"><strong>Total</strong></td>
                                    <td class="ae"><strong>R$ <?= number_format($pagamento['valor_bruto'], 2, ',', '.') ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="botao-verde">Confirmar Pagamento</button>
                    <a href="<?= BASE_URL ?>admin/pagamentos" class="botao-cinza">Cancelar</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-error">
            <p>Pagamento não encontrado.</p>
            <a href="<?= BASE_URL ?>admin/pagamentos" class="botao-cinza">Voltar para lista</a>
        </div>
    <?php endif; ?>
</div>
</div>
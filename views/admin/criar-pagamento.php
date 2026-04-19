<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>

    <form class="formulario-cadastro" method="post" action="<?= BASE_URL ?>admin/registrar-pagamento" enctype="multipart/form-data">
        <div class="titulo">Pagamento Pendente</div>
        
        <hr class="shadow">
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">Costureira</span>
                <span class="flex v-center">Período</span>
                <span class="flex v-center">Valor a Pagar</span>
                <span class="flex v-center">Chave Pix</span>
                <span class="flex v-center">Data do pagamento</span>
                <span class="flex v-center">Comprovante </span>
                <span class="flex v-center">Observação (Opcional)</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($pagamento['costureira_nome']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= date('m/Y', strtotime($pagamento['periodo_referencia'])) ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($pagamento['valor_liquido'] ?? $pagamento['valor_bruto'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= ucfirst($pagamento['tipo_chave_pix']) ?> - <?= htmlspecialchars($pagamento['chave_pix']) ?></span>

                <span class="flex v-center" style="height:20px"><input type="date" name="data_pagamento" id="data_pagamento" class="form-control" value="<?= date('Y-m-d') ?>" required></span>
                <span class="flex v-center" style="height:20px">
                    <label class="form-label v-center flex" for="comprovante">
                        <img class="icone" src="<?php echo ASSETS_URL?>icones/anexo.svg" alt="Anexo">
                        <small>Formatos aceitos: PDF, JPG, PNG, DOC (Max: 5MB)</small>
                    </label>
                    <input type="file" name="comprovante" id="comprovante" class="form-input escondido" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </span>
                
                <span class="flex v-center" style="height:20px"> <input size="50" name="observacao" id="observacao" class="form-control" placeholder="Informações adicionais sobre o pagamento..."></input></span>
            </span>
         </span>
        <input type="hidden" name="id" value="<?= $pagamento['id'] ?>">

    
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <button type="submit" class="botao-azul">Confirmar Pagamento</button>
            <a href="<?= BASE_URL ?>admin/pagamentos" class="botao">Voltar</a>
        </div>

        
        <!-- Seção de Peças -->
        <div style="margin-top: 2rem;">
            <h3>Serviços Incluídos neste Pagamento</h3>
            
            <!-- Filtros e busca -->
            <div class="filtro flex v-center s-gap" style="margin-bottom: 20px;">
                <form method="GET" class="flex v-center s-gap">
                    <input type="hidden" name="id" value="<?= $lote['id'] ?>">
                    <input type="text" name="search" placeholder="Buscar serviço..." value="<?= htmlspecialchars($search ?? '') ?>" class="form-input" style="width: 300px;">
                    <button type="submit" class="botao-azul pequeno">Buscar</button>
                    <?php if (!empty($search)): ?>
                        <a href="?id=<?= $lote['id'] ?>" class="botao pequeno">Limpar</a>
                    <?php endif; ?>
                </form>
            </div>
            
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
    </form>
</div>
</div>
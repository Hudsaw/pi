<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <form class="formulario-cadastro">
        <div class="titulo">Lote #<?= htmlspecialchars($lote['id']) ?> - <?= htmlspecialchars($lote['nome']) ?></div>
        
        <hr class="shadow">
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">ID da Empresa</span>
                <span class="flex v-center">Coleção</span>
                <span class="flex v-center">Valor Total</span>
                <span class="flex v-center">Data de Entrada</span>
                <span class="flex v-center">Data de Entrega</span>
                <span class="flex v-center">Status</span>
                <span class="flex v-center">Anexo</span>
                <span class="flex v-center">Observação</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($lote['empresa_id']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($lote['colecao']) ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($lote['valor_total'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($lote['data_entrada'])) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $lote['data_entrega'] ? date('d/m/Y', strtotime($lote['data_entrega'])) : 'Não definida' ?></span>
                <span class="flex v-center status-<?= strtolower($lote['status']) ?>" style="min-height:20px"><?= htmlspecialchars($lote['status']) ?></span>
                <span class="flex v-center" style="min-height:20px">
                    <?php if (!empty($lote['anexos'])): ?>
                        <a href="<?= BASE_URL ?>uploads/lotes/<?= htmlspecialchars($lote['anexos']) ?>" 
                           target="_blank" class="botao-link">
                          Ver Anexo
                        </a>
                    <?php else: ?>
                        Nenhum anexo
                    <?php endif; ?>
                </span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($lote['observacao'] ?? 'Nenhuma') ?></span>
            </span>
        </span>
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/lotes" class="botao">Voltar</a>
            
            <?php if ($lote['status'] === 'Aberto'): ?>
                <a href="<?= BASE_URL ?>admin/editar-lote?id=<?= $lote['id'] ?>" class="botao">Editar Lote</a>
                <button type="button" class="botao-verde" onclick="abrirModalFinalizar()">
                    Finalizar Lote
                </button>
            <?php elseif ($lote['status'] === 'Entregue'): ?>
                <a href="<?= BASE_URL ?>admin/editar-lote?id=<?= $lote['id'] ?>" class="botao">Editar Lote</a>
            <?php endif; ?>
        </div>
        
        <!-- Seção de Peças -->
        <div style="margin-top: 2rem;">
            <h3>Peças do Lote (<?= $totalPecas ?> peças)</h3>
            
            <!-- Filtros e busca -->
            <div class="filtro flex v-center s-gap" style="margin-bottom: 20px;">
                <form method="GET" class="flex v-center s-gap">
                    <input type="hidden" name="id" value="<?= $lote['id'] ?>">
                    <input type="text" name="search" placeholder="Buscar peça..." value="<?= htmlspecialchars($search ?? '') ?>" class="form-input" style="width: 300px;">
                    <button type="submit" class="botao-azul pequeno">Buscar</button>
                    <?php if (!empty($search)): ?>
                        <a href="?id=<?= $lote['id'] ?>" class="botao pequeno">Limpar</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="tabela">
                <table cellspacing='0' class="redondinho shadow" id="tabelaPecas">
                    <thead>
                        <tr>
                            <th class="ae">ID</th>
                            <th class="ae">Tipo Peça</th>
                            <th class="ae">Cor</th>
                            <th class="ae">Tamanho</th>
                            <th class="ae">Operação</th>
                            <th class="ae">Quantidade</th>
                            <th class="ae">Valor Unitário</th>
                            <th class="ae">Valor Total</th>
                            <th class="ae">Observação</th>
                            <th class="ac">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pecas)): ?>
                            <tr>
                                <td colspan="10" class="ac">Nenhuma peça encontrada</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pecas as $peca): ?>
                                <tr class="linha-peca">
                                    <td class="ae"><?= htmlspecialchars($peca['id']) ?></td>
                                    <td class="ae"><?= htmlspecialchars($peca['tipo_peca_nome']) ?></td>
                                    <td class="ae">
                                        <span class="cor-indicator" style="background-color: <?= htmlspecialchars($peca['codigo_hex'] ?? '#CCCCCC') ?>"></span>
                                        <?= htmlspecialchars($peca['cor_nome']) ?>
                                    </td>
                                    <td class="ae"><?= htmlspecialchars($peca['tamanho_nome']) ?></td>
                                    <td class="ae"><?= htmlspecialchars($peca['operacao_nome']) ?></td>
                                    <td class="ae"><?= htmlspecialchars($peca['quantidade']) ?></td>
                                    <td class="ae">R$ <?= number_format($peca['valor_unitario'], 2, ',', '.') ?></td>
                                    <td class="ae">R$ <?= number_format($peca['valor_total'], 2, ',', '.') ?></td>
                                    <td class="ae"><?= htmlspecialchars($peca['observacao'] ?? 'Nenhuma') ?></td>
                                    <td class="ac">
                                        <a href="<?= BASE_URL ?>admin/remover-peca?id=<?= $peca['id'] ?>&lote_id=<?= $lote['id'] ?>" 
                                           onclick="return confirm('Tem certeza que deseja remover esta peça?')"
                                           class="btn-remover" title="Remover peça">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover">
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <?php if ($totalPaginas > 1): ?>
            <div class="paginacao flex center v-center s-gap" style="margin-top: 20px;">
                <?php if ($paginaAtual > 1): ?>
                    <a href="?id=<?= $lote['id'] ?>&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link">« Primeira</a>
                <?php endif; ?>
                
                <?php if ($paginaAtual > 1): ?>
                    <a href="?id=<?= $lote['id'] ?>&page=<?= $paginaAtual - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link">‹ Anterior</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $paginaAtual - 2); $i <= min($totalPaginas, $paginaAtual + 2); $i++): ?>
                    <?php if ($i == $paginaAtual): ?>
                        <span class="pagina-atual"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?id=<?= $lote['id'] ?>&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a href="?id=<?= $lote['id'] ?>&page=<?= $paginaAtual + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link">Próxima ›</a>
                <?php endif; ?>
                
                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a href="?id=<?= $lote['id'] ?>&page=<?= $totalPaginas ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link">Última »</a>
                <?php endif; ?>
            </div>
            
            <div class="info-paginacao flex center" style="margin-top: 10px;">
                <span class="texto-pequeno">
                    Mostrando <?= $inicio ?> a <?= $fim ?> de <?= $totalPecas ?> peças
                    <?php if (!empty($search)): ?>
                        (filtrado por: "<?= htmlspecialchars($search) ?>")
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if ($lote['status'] === 'Aberto'): ?>
<!-- Modal para finalizar lote -->
<div id="modalFinalizar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; width: 90%; max-width: 500px;">
        <div style="padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Finalizar Lote #<?= htmlspecialchars($lote['id']) ?></h3>
            <span onclick="fecharModalFinalizar()" style="font-size: 28px; cursor: pointer;">&times;</span>
        </div>
        <form method="POST" action="<?= BASE_URL ?>admin/finalizar-lote" onsubmit="return confirm('Confirma a finalização deste lote? Um registro de pagamento recebido será criado automaticamente.')">
            <input type="hidden" name="id" value="<?= $lote['id'] ?>">
            
            <div style="padding: 20px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Lote:</label>
                    <strong><?= htmlspecialchars($lote['nome']) ?></strong>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Coleção:</label>
                    <strong><?= htmlspecialchars($lote['colecao']) ?></strong>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Empresa:</label>
                    <strong><?= htmlspecialchars($lote['empresa_nome'] ?? 'Não informada') ?></strong>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Valor Total do Lote:</label>
                    <strong style="color: #28a745; font-size: 1.2em;">R$ <?= number_format($lote['valor_total'], 2, ',', '.') ?></strong>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="data_entrega" style="display: block; margin-bottom: 5px; font-weight: bold;">Data de Entrega *</label>
                    <input type="date" name="data_entrega" id="data_entrega" 
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                           value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="valor_recebido" style="display: block; margin-bottom: 5px; font-weight: bold;">Valor Recebido (R$) *</label>
                    <input type="number" name="valor_recebido" id="valor_recebido" 
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                           step="0.01" min="0" 
                           value="<?= number_format($lote['valor_total'], 2, '.', '') ?>" required>
                    <small style="color: #666;">Informe o valor efetivamente recebido</small>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="observacao_pagamento" style="display: block; margin-bottom: 5px; font-weight: bold;">Observação</label>
                    <textarea name="observacao_pagamento" id="observacao_pagamento" 
                              style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 80px;"
                              placeholder="Observações sobre o pagamento recebido..."></textarea>
                </div>
                
                <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; padding: 10px; margin-top: 10px;">
                    <strong>ℹ️ Informação</strong>
                    <p style="margin: 5px 0 0 0;">Ao finalizar, o lote será marcado como "Entregue" e um registro de pagamento recebido será criado automaticamente.</p>
                </div>
            </div>
            
            <div style="padding: 15px 20px; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="fecharModalFinalizar()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="submit" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Confirmar Finalização</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalFinalizar() {
    document.getElementById('modalFinalizar').style.display = 'block';
}

function fecharModalFinalizar() {
    document.getElementById('modalFinalizar').style.display = 'none';
}

// Fechar modal clicando fora dele
document.getElementById('modalFinalizar').addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModalFinalizar();
    }
});

// Fechar modal com tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalFinalizar();
    }
});
</script>
<?php endif; ?>
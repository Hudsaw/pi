<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar-admin.php'; ?>
    
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap v-center">
            <h2>Lote #<?= htmlspecialchars($lote['id']) ?> - <?= htmlspecialchars($lote['nome']) ?></h2>
            <a href="<?= BASE_URL ?>admin/adicionar-peca?lote_id=<?= $lote['id'] ?>" class="botao-azul">Adicionar Peça</a>
        </div>
        
        <div class="detalhes-lote">
            <div class="info-lote">
                <div class="info-item">
                    <span class="info-label">ID da Empresa:</span>
                    <span class="info-value"><?= htmlspecialchars($lote['empresa_id']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Coleção:</span>
                    <span class="info-value"><?= htmlspecialchars($lote['colecao']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Valor Total:</span>
                    <span class="info-value">R$ <?= number_format($lote['valor_total'], 2, ',', '.') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Entrada:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($lote['data_entrada'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Entrega:</span>
                    <span class="info-value"><?= $lote['data_entrega'] ? date('d/m/Y', strtotime($lote['data_entrega'])) : 'Não definida' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value status-<?= strtolower($lote['status']) ?>">
                        <?= htmlspecialchars($lote['status']) ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Observação:</span>
                    <span class="info-value"><?= htmlspecialchars($lote['observacao'] ?? 'Nenhuma') ?></span>
                </div>
            </div>
        </div>
        
        <h3>Peças do Lote (<?= $totalPecas ?> peças)</h3>
        
        <!-- Filtros e busca -->
        <div class="filtro flex v-center s-gap" style="margin-bottom: 20px;">
            <form method="GET" class="flex v-center s-gap">
                <input type="hidden" name="id" value="<?= $lote['id'] ?>">
                <input type="text" name="search" placeholder="Buscar peça..." value="<?= htmlspecialchars($search ?? '') ?>" class="campo-busca">
                <button type="submit" class="botao-azul pequeno">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="?id=<?= $lote['id'] ?>" class="botao-cinza pequeno">Limpar</a>
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
            <!-- Link primeira página -->
            <?php if ($paginaAtual > 1): ?>
                <a href="?id=<?= $lote['id'] ?>&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link">« Primeira</a>
            <?php endif; ?>
            
            <!-- Link página anterior -->
            <?php if ($paginaAtual > 1): ?>
                <a href="?id=<?= $lote['id'] ?>&page=<?= $paginaAtual - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link">‹ Anterior</a>
            <?php endif; ?>
            
            <!-- Números das páginas -->
            <?php for ($i = max(1, $paginaAtual - 2); $i <= min($totalPaginas, $paginaAtual + 2); $i++): ?>
                <?php if ($i == $paginaAtual): ?>
                    <span class="pagina-atual"><?= $i ?></span>
                <?php else: ?>
                    <a href="?id=<?= $lote['id'] ?>&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <!-- Link próxima página -->
            <?php if ($paginaAtual < $totalPaginas): ?>
                <a href="?id=<?= $lote['id'] ?>&page=<?= $paginaAtual + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagina-link">Próxima ›</a>
            <?php endif; ?>
            
            <!-- Link última página -->
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
</div>

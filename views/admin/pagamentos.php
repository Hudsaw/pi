<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
<div class="conteudo-tabela">
    <div class="cabecalho-pagina flex-between">
        <h2>Pagamentos</h2>
        <div class="acoes-cabecalho">
            <a href="<?= BASE_URL ?>admin/pagamentos?export=csv<?= !empty($filtro) ? '&filtro=' . $filtro : '' ?><?= !empty($termoBusca) ? '&search=' . urlencode($termoBusca) : '' ?>" class="btn-exportar">
                <img class="icone" src="<?= ASSETS_URL ?>icones/download.svg" alt="exportar">
                Exportar CSV
            </a>
        </div>
    </div>
    
    <div class="filtro flex s-gap">
        <div class="busca-wrapper">
            <input type="text" id="filtro" placeholder="Buscar por costureira ou período..." value="<?= htmlspecialchars($termoBusca) ?>" onkeypress="if(event.key === 'Enter') filtrarBusca()">
            <button onclick="filtrarBusca()" class="btn-buscar">Buscar</button>
        </div>
        
        <select id="filtroStatus" onchange="filtrarPorStatus()">
            <option value="todos" <?= $filtro === 'todos' ? 'selected' : '' ?>>Todos</option>
            <option value="pendentes" <?= $filtro === 'pendentes' ? 'selected' : '' ?>>Pendentes</option>
            <option value="pagos" <?= $filtro === 'pagos' ? 'selected' : '' ?>>Pagos</option>
            <option value="cancelados" <?= $filtro === 'cancelados' ? 'selected' : '' ?>>Cancelados</option>
        </select>
    </div>
    
    <div class="info-paginacao">
        <span>Mostrando <?= count($listaPagamentos) ?> de <?= $totalRegistros ?> registros</span>
    </div>
    
    <div class="tabela">
        <table cellspacing='0' class="redondinho shadow">
            <thead>
                <tr>
                    <th class="ae">ID</th>
                    <th class="ae">Costureira</th>
                    <th class="ae">Período</th>
                    <th class="ae">Serviços</th>
                    <th class="ae">Valor Bruto</th>
                    <th class="ae">Valor Líquido</th>
                    <th class="ae">Status</th>
                    <th class="ae">Data Pagamento</th>
                    <th class="ac">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaPagamentos)): ?>
                    <tr>
                        <td colspan="9" class="ac">Nenhum pagamento encontrado</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaPagamentos as $index => $pagamento): ?>
                        <tr>
                            <td class="ae">#<?= $pagamento['id'] ?></td>
                            <td class="ae"><?= htmlspecialchars($pagamento['costureira_nome']) ?></td>
                            <td class="ae"><?= date('m/Y', strtotime($pagamento['periodo_referencia'])) ?></td>
                            <td class="ae"><?= $pagamento['total_servicos'] ?? 0 ?> serviço(s)</td>
                            <td class="ae">R$ <?= number_format($pagamento['valor_bruto'], 2, ',', '.') ?></td>
                            <td class="ae texto-verde">R$ <?= number_format($pagamento['valor_liquido'] ?? $pagamento['valor_bruto'], 2, ',', '.') ?></td>
                            <td class="ae">
                                <span class="status-badge <?= match($pagamento['status']) {
                                    'Pendente' => 'warning',
                                    'Pago' => 'completed',
                                    'Cancelado' => 'inactive',
                                    default => ''
                                } ?>">
                                    <?= $pagamento['status'] ?>
                                </span>
                            </td>
                            <td class="ae"><?= $pagamento['data_pagamento'] ? date('d/m/Y', strtotime($pagamento['data_pagamento'])) : '-' ?></td>
                            <td class="ac">
                                <?php if ($pagamento['status'] === 'Pendente'): ?>
                                    <a href="<?= BASE_URL ?>admin/registrar-pagamento?id=<?= $pagamento['id'] ?>" class="btn-acao" title="Registrar Pagamento">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="pagar">
                                    </a>
                                    <a href="<?= BASE_URL ?>admin/cancelar-pagamento?id=<?= $pagamento['id'] ?>" 
                                       onclick="return confirm('Cancelar este pagamento? O serviço voltará a ficar pendente.')"
                                       class="btn-acao btn-remover" title="Cancelar">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="cancelar">
                                    </a>
                                <?php endif; ?>
                                <?php if ($pagamento['status'] === 'Pago' && !empty($pagamento['comprovante'])): ?>
                                    <a href="<?= BASE_URL ?>uploads/comprovantes/<?= $pagamento['comprovante'] ?>" target="_blank" class="btn-acao" title="Ver Comprovante">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/anexo.svg" alt="comprovante">
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Paginação -->
    <?php if ($totalPaginas > 1): ?>
    <div class="paginacao">
        <ul class="paginacao-lista">
            <?php if ($paginaAtual > 1): ?>
                <li><a href="<?= BASE_URL ?>admin/pagamentos?pagina=1&filtro=<?= $filtro ?>&search=<?= urlencode($termoBusca) ?>">&laquo; Primeira</a></li>
                <li><a href="<?= BASE_URL ?>admin/pagamentos?pagina=<?= $paginaAtual - 1 ?>&filtro=<?= $filtro ?>&search=<?= urlencode($termoBusca) ?>">&lsaquo; Anterior</a></li>
            <?php endif; ?>
            
            <?php
            $inicio = max(1, $paginaAtual - 2);
            $fim = min($totalPaginas, $paginaAtual + 2);
            
            if ($inicio > 1): ?>
                <li><span>...</span></li>
            <?php endif;
            
            for ($i = $inicio; $i <= $fim; $i++): ?>
                <li class="<?= $i == $paginaAtual ? 'ativo' : '' ?>">
                    <a href="<?= BASE_URL ?>admin/pagamentos?pagina=<?= $i ?>&filtro=<?= $filtro ?>&search=<?= urlencode($termoBusca) ?>"><?= $i ?></a>
                </li>
            <?php endfor;
            
            if ($fim < $totalPaginas): ?>
                <li><span>...</span></li>
            <?php endif; ?>
            
            <?php if ($paginaAtual < $totalPaginas): ?>
                <li><a href="<?= BASE_URL ?>admin/pagamentos?pagina=<?= $paginaAtual + 1 ?>&filtro=<?= $filtro ?>&search=<?= urlencode($termoBusca) ?>">Próxima &rsaquo;</a></li>
                <li><a href="<?= BASE_URL ?>admin/pagamentos?pagina=<?= $totalPaginas ?>&filtro=<?= $filtro ?>&search=<?= urlencode($termoBusca) ?>">Última &raquo;</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
</div>

<script>
function filtrarBusca() {
    const termo = document.getElementById('filtro').value;
    const status = document.getElementById('filtroStatus').value;
    const url = new URL(window.location.href);
    url.searchParams.set('search', termo);
    url.searchParams.set('filtro', status);
    url.searchParams.delete('pagina');
    window.location.href = url.toString();
}

function filtrarPorStatus() {
    const status = document.getElementById('filtroStatus').value;
    const termo = document.getElementById('filtro').value;
    const url = new URL(window.location.href);
    url.searchParams.set('filtro', status);
    if (termo) {
        url.searchParams.set('search', termo);
    }
    url.searchParams.delete('pagina');
    window.location.href = url.toString();
}
</script>
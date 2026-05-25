<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
<div class="conteudo-tabela">
    <h2>Pagamentos</h2>
    
    <!-- Cards de estatísticas -->
    <div class="cards-dashboard grid-4 mb-20">
        <div class="card card-pendente">
            <div class="card-titulo">Pendentes</div>
            <div class="card-valor">R$ <?= number_format($estatisticas['total_pendente'], 2, ',', '.') ?></div>
            <div class="card-detalhe"><?= $estatisticas['pendentes'] ?> pagamentos</div>
        </div>
        <div class="card card-pago">
            <div class="card-titulo">Pagos</div>
            <div class="card-valor">R$ <?= number_format($estatisticas['total_pago'], 2, ',', '.') ?></div>
            <div class="card-detalhe"><?= $estatisticas['pagos'] ?> pagamentos</div>
        </div>
        <div class="card card-cancelado">
            <div class="card-titulo">Cancelados</div>
            <div class="card-valor">R$ <?= number_format($estatisticas['total_cancelado'], 2, ',', '.') ?></div>
            <div class="card-detalhe"><?= $estatisticas['cancelados'] ?> pagamentos</div>
        </div>
        <div class="card card-total">
            <div class="card-titulo">Total Geral</div>
            <div class="card-valor">R$ <?= number_format($estatisticas['total_pendente'] + $estatisticas['total_pago'] + $estatisticas['total_cancelado'], 2, ',', '.') ?></div>
            <div class="card-detalhe"><?= $estatisticas['total'] ?> pagamentos</div>
        </div>
    </div>
    
    <div class="filtro flex s-gap">
        <input type="text" id="filtro" placeholder="Digite sua busca" onkeyup="filtrarPagamentos()">
        <span class="flex v-center">
            <select id="filtroStatus" onchange="filtrarPorStatus(this)">
                <option value="todos" <?= $filtro === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="pendentes" <?= $filtro === 'pendentes' ? 'selected' : '' ?>>Pendentes</option>
                <option value="pagos" <?= $filtro === 'pagos' ? 'selected' : '' ?>>Pagos</option>
                <option value="cancelados" <?= $filtro === 'cancelados' ? 'selected' : '' ?>>Cancelados</option>
            </select>
        </span>
    </div>

    <div class="tabela">
        <table cellspacing='0' class="redondinho shadow">
            <thead>
                <tr>
                    <th class="ae">Período</th>
                    <th class="ae">Valor Bruto</th>
                    <th class="ae">Desconto</th>
                    <th class="ae">Valor Líquido</th>
                    <th class="ae">Status</th>
                    <th class="ae">Data Pagamento</th>
                    <th class="ac">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaPagamentos)): ?>
                    <tr>
                        <td colspan="8" class="ac">Nenhum pagamento encontrado</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaPagamentos as $pagamento): ?>
                        <tr data-status="<?= strtolower($pagamento['status']) ?>">
                            <td class="ae"><?= date('m/Y', strtotime($pagamento['periodo_referencia'])) ?></td>
                            <td class="ae">R$ <?= number_format($pagamento['valor_bruto'], 2, ',', '.') ?></td>
                            <td class="ae">R$ <?= number_format($pagamento['valor_desconto'], 2, ',', '.') ?></td>
                            <td class="ae">R$ <?= number_format($pagamento['valor_liquido'], 2, ',', '.') ?></td>
                            <td class="ae status-<?= strtolower($pagamento['status']) ?>">
                                <?= htmlspecialchars($pagamento['status']) ?>
                                <?php if ($pagamento['quantidade_servicos']): ?>
                                    <small>(<?= $pagamento['quantidade_servicos'] ?> serv.)</small>
                                <?php endif; ?>
                            </td>
                            <td class="ae">
                                <?= $pagamento['data_pagamento'] ? date('d/m/Y', strtotime($pagamento['data_pagamento'])) : '-' ?>
                            </td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>costura/visualizar-pagamento?id=<?= $pagamento['id'] ?>" 
                                   class="btn-visualizar" title="Visualizar">
                                    <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filtrarPagamentos() {
    const filtro = document.getElementById('filtro').value.toLowerCase();
    const linhas = document.querySelectorAll('.tabela tbody tr');
    
    linhas.forEach(linha => {
        if (linha.cells.length <= 1) return;
        const textoLinha = linha.textContent.toLowerCase();
        if (textoLinha.includes(filtro)) {
            linha.style.display = '';
        } else {
            linha.style.display = 'none';
        }
    });
}

function filtrarPorStatus(select) {
    const status = select.value;
    window.location.href = '<?= BASE_URL ?>costura/pagamentos?filtro=' + status;
}
</script>
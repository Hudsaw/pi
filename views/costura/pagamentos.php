<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="conteudo-tabela">
        <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Meus Pagamentos</h2>
            <a href="<?= BASE_URL ?>costura/painel" class="botao">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
        
        <!-- Cards de Resumo Financeiro (mantido pois é útil) -->
        <div class="cards-dashboard" style="margin-bottom: 25px;">
            <!-- Total Recebido -->
            <div class="card">
                <div class="card-header">
                    <h3>Total Recebido</h3>
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="card-content">
                    <div class="numero">R$ <?= number_format(array_sum(array_column($pagamentos ?? [], 'valor_bruto')), 2, ',', '.') ?></div>
                    <p>Valor bruto total</p>
                </div>
            </div>
            
            <!-- Pagamentos Pendentes -->
            <div class="card">
                <div class="card-header">
                    <h3>Pagamentos Pendentes</h3>
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <div class="numero"><?= count(array_filter($pagamentos ?? [], fn($p) => ($p['status'] ?? '') == 'Pendente')) ?></div>
                    <p>Aguardando pagamento</p>
                </div>
            </div>
            
            <!-- Pagamentos Recebidos -->
            <div class="card">
                <div class="card-header">
                    <h3>Pagamentos Recebidos</h3>
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-content">
                    <div class="numero"><?= count(array_filter($pagamentos ?? [], fn($p) => ($p['status'] ?? '') == 'Pago')) ?></div>
                    <p>Já processados</p>
                </div>
            </div>
        </div>

        <!-- Filtro por status -->
        <div class="filtro flex s-gap" style="margin-bottom: 20px;">
            <div class="flex v-center" style="gap: 10px;">
                <label for="statusFilter">Filtrar por status:</label>
                <select id="statusFilter" class="form-control" style="width: 200px;" onchange="filtrarPorStatus(this.value)">
                    <option value="all">Todos os status</option>
                    <option value="Pendente">Pendentes</option>
                    <option value="Pago">Pagos</option>
                    <option value="Cancelado">Cancelados</option>
                </select>
            </div>
        </div>

        <!-- Contador de registros -->
        <div id="resultado-info" style="margin-bottom: 10px;">
            <span id="contador-registros">Mostrando <?= count($pagamentos ?? []) ?> pagamentos</span>
        </div>

        <!-- Tabela de pagamentos -->
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow" id="tabela-pagamentos">
                <thead>
                    <tr>
                        <th class="ae">Período</th>
                        <th class="ae">Valor Bruto</th>
                        <th class="ae">Descontos</th>
                        <th class="ae">Valor Líquido</th>
                        <th class="ae">Serviços</th>
                        <th class="ae">Data Pagamento</th>
                        <th class="ae">Status</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagamentos)): ?>
                        <tr>
                            <td colspan="8" class="ac">Nenhum pagamento encontrado</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pagamentos as $pagamento): ?>
                            <tr data-status="<?= $pagamento['status'] ?? '' ?>">
                                <td class="ae">
                                    <strong><?= date('m/Y', strtotime($pagamento['periodo_referencia'] ?? 'now')) ?></strong>
                                </td>
                                <td class="ae">R$ <?= number_format($pagamento['valor_bruto'] ?? 0, 2, ',', '.') ?></td>
                                <td class="ae <?= ($pagamento['valor_desconto'] ?? 0) > 0 ? 'texto-vermelho' : '' ?>">
                                    <?php if (($pagamento['valor_desconto'] ?? 0) > 0): ?>
                                        - R$ <?= number_format($pagamento['valor_desconto'], 2, ',', '.') ?>
                                        <?php if (!empty($pagamento['motivo_desconto'])): ?>
                                            <i class="fas fa-info-circle" title="<?= htmlspecialchars($pagamento['motivo_desconto']) ?>"></i>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        R$ 0,00
                                    <?php endif; ?>
                                </td>
                                <td class="ae"><strong>R$ <?= number_format($pagamento['valor_liquido'] ?? 0, 2, ',', '.') ?></strong></td>
                                <td class="ae"><?= $pagamento['quantidade_servicos'] ?? 0 ?></td>
                                <td class="ae">
                                    <?= !empty($pagamento['data_pagamento']) ? date('d/m/Y', strtotime($pagamento['data_pagamento'])) : '-' ?>
                                </td>
                                <td class="ae">
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
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="ac">
                                    <form method="POST" action="<?= BASE_URL ?>costura/visualizar-pagamento" style="display: inline;">
                                        <input type="hidden" name="pagamento_id" value="<?= $pagamento['id'] ?>">
                                        <button type="submit" class="btn-visualizar" title="Visualizar">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                        </button>
                                    </form>
                                    
                                    <?php if (($pagamento['status'] ?? '') == 'Pago' && !empty($pagamento['comprovante'])): ?>
                                        <a href="<?= BASE_URL ?>uploads/comprovantes/<?= $pagamento['comprovante'] ?>" target="_blank" class="btn-download" title="Comprovante">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/download.svg" alt="download">
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filtrarPorStatus(status) {
    const linhas = document.querySelectorAll('#tabela-pagamentos tbody tr');
    let contadorVisiveis = 0;
    
    linhas.forEach(linha => {
        // Pular linha de "Nenhum pagamento encontrado"
        if (linha.cells.length <= 1 || linha.querySelector('td[colspan]')) {
            return;
        }
        
        const statusLinha = linha.getAttribute('data-status');
        
        if (status === 'all' || statusLinha === status) {
            linha.style.display = '';
            contadorVisiveis++;
        } else {
            linha.style.display = 'none';
        }
    });
    
    // Atualiza contador
    const contadorElement = document.getElementById('contador-registros');
    if (contadorElement) {
        contadorElement.textContent = `Mostrando ${contadorVisiveis} pagamentos`;
    }
}
</script>
<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar-admin.php'; ?>
    
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap v-center">
            <h2>Serviços</h2>
            <a href="<?= BASE_URL ?>admin/criar-servico" class="botao-azul">Novo Serviço</a>
            
            <!-- Filtros -->
            <select id="filtro-status" onchange="filtrarServicos()" class="campo">
                <option value="ativos" <?= ($filtro === 'ativos') ? 'selected' : '' ?>>Ativos</option>
                <option value="finalizados" <?= ($filtro === 'finalizados') ? 'selected' : '' ?>>Finalizados</option>
                <option value="inativos" <?= ($filtro === 'inativos') ? 'selected' : '' ?>>Inativos</option>
            </select>
            
            <!-- Busca -->
            <form method="GET" class="flex v-center s-gap">
                <input type="hidden" name="filtro" value="<?= $filtro ?>">
                <input type="text" name="search" placeholder="Buscar serviços..." value="<?= htmlspecialchars($termoBusca) ?>" class="campo-busca">
                <button type="submit" class="botao-azul pequeno">Buscar</button>
                <?php if (!empty($termoBusca)): ?>
                    <a href="?filtro=<?= $filtro ?>" class="botao-cinza pequeno">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow">
                <thead>
                    <tr>
                        <th class="ae">ID</th>
                        <th class="ae">Lote</th>
                        <th class="ae">Operação</th>
                        <th class="ae">Costureira</th>
                        <th class="ae">Qtd. Peças</th>
                        <th class="ae">Valor</th>
                        <th class="ae">Data Envio</th>
                        <th class="ae">Status</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaServicos)): ?>
                        <tr>
                            <td colspan="9" class="ac">Nenhum serviço encontrado</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaServicos as $servico): ?>
                            <tr>
                                <td class="ae"><?= htmlspecialchars($servico['id']) ?></td>
                                <td class="ae"><?= htmlspecialchars($servico['lote_nome']) ?></td>
                                <td class="ae"><?= htmlspecialchars($servico['operacao_nome']) ?></td>
                                <td class="ae"><?= htmlspecialchars($servico['costureira_nome'] ?? 'Não vinculada') ?></td>
                                <td class="ae"><?= htmlspecialchars($servico['quantidade_pecas']) ?></td>
                                <td class="ae">R$ <?= number_format($servico['valor_operacao'], 2, ',', '.') ?></td>
                                <td class="ae"><?= date('d/m/Y', strtotime($servico['data_envio'])) ?></td>
                                <td class="ae">
                                    <span class="status-<?= strtolower($servico['status']) ?>">
                                        <?= htmlspecialchars($servico['status']) ?>
                                    </span>
                                </td>
                                <td class="ac">
                                    <a href="<?= BASE_URL ?>admin/visualizar-servico?id=<?= $servico['id'] ?>" class="btn-visualizar" title="Visualizar">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                    </a>
                                    <?php if ($servico['status'] === 'Em andamento'): ?>
                                        <a href="<?= BASE_URL ?>admin/remover-servico?id=<?= $servico['id'] ?>" 
                                           onclick="return confirm('Tem certeza que deseja remover este serviço?')"
                                           class="btn-remover" title="Remover">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover">
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
function filtrarServicos() {
    const filtro = document.getElementById('filtro-status').value;
    window.location.href = '?filtro=' + filtro;
}
</script>
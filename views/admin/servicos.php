<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap">
            <input type="text" id="filtro" placeholder="Digite sua busca" onkeyup="filtrar()">
            <span class="flex v-center">
                <input type="checkbox" id="inativos" onchange="filtrarInativos(this)">
                <label class="flex v-center" for="inativos">Mostrar Inativos</label>
            </span>
            <a href="<?= BASE_URL ?>admin/criar-servico" class="botao-azul">Criar Serviço</a>
        </div>

        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow">
                <thead>
                    <tr>
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
                                <td class="ae"><?= htmlspecialchars($servico['lote_nome']) ?></td>
                                <td class="ae"><?= htmlspecialchars($servico['operacao_nome']) ?></td>
<td class="ae"><?= htmlspecialchars($servico['costureiras_vinculadas'] ?? 'Não vinculada') ?></td>                                <td class="ae"><?= htmlspecialchars($servico['quantidade_pecas']) ?></td>
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
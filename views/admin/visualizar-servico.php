<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <form class="formulario-cadastro">
        <div class="titulo">Serviço #<?= htmlspecialchars($servico['id']) ?></div>
        <hr class="shadow">
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">Lote</span>
                <span class="flex v-center">Operação</span>
                <span class="flex v-center">Quantidade de peças</span>
                <span class="flex v-center">Valor da operação</span>
                <span class="flex v-center">Valor total</span>
                <span class="flex v-center">Data de envio</span>
                <span class="flex v-center">Status</span>
                <span class="flex v-center">Observação</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['lote_nome']) ?> - <?= htmlspecialchars($servico['colecao']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['operacao_nome']) ?> (R$ <?= number_format($servico['valor_base_operacao'], 2, ',', '.') ?>)</span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['quantidade_pecas']) ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($servico['valor_operacao'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($servico['valor_operacao'] * $servico['quantidade_pecas'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($servico['data_envio'])) ?></span>
                <span class="flex v-center status-<?= strtolower($servico['status']) ?>" style="min-height:20px"><?= htmlspecialchars($servico['status']) ?></span>
                <?php if ($servico['data_finalizacao']): ?>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($servico['data_finalizacao'])) ?></span>
                <?php endif; ?>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['observacao'] ?? 'Nenhuma') ?></span>
            </span>
        </span>
        
        <!-- Costureiras vinculadas -->
        <div>
        <h3>Costureiras Vinculadas</h3>
        <?php if (empty($servico['costureiras'])): ?>
            <p>Nenhuma costureira vinculada a este serviço.</p>
        <?php else: ?>
            <div class="tabela">
                <table cellspacing='0' class="redondinho shadow">
                    <thead>
                        <tr>
                            <th class="ae">Nome</th>
                            <th class="ae">Especialidade</th>
                            <th class="ae">Data Início</th>
                            <th class="ae">Data Entrega</th>
                            <th class="ac">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servico['costureiras'] as $costureira): ?>
                            <tr>
                                <td class="ae"><?= htmlspecialchars($costureira['nome']) ?></td>
                                <td class="ae"><?= htmlspecialchars($costureira['especialidade'] ?? 'Não informada') ?></td>
                                <td class="ae"><?= date('d/m/Y', strtotime($costureira['data_inicio'])) ?></td>
                                <td class="ae"><?= date('d/m/Y', strtotime($costureira['data_entrega'])) ?></td>
                                <td class="ac">
                                    <?php if ($servico['status'] === 'Em andamento'): ?>
                                    <a href="<?= BASE_URL ?>admin/desvincular-costureira?servico_id=<?= $servico['id'] ?>&costureira_id=<?= $costureira['id'] ?>" 
                                       onclick="return confirm('Tem certeza que deseja desvincular esta costureira?')"
                                       class="btn-remover" title="Desvincular">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="desvincular">
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <!-- Ações -->
        <?php if ($servico['status'] === 'Em andamento'): ?>
        <div class="acoes-servico flex s-gap" style="margin-top: 20px;">
            <!-- Vincular costureira -->
            <form method="POST" action="<?= BASE_URL ?>admin/vincular-costureira" class="flex v-center s-gap">
                <input type="hidden" name="servico_id" value="<?= $servico['id'] ?>">
                <select name="costureira_id" required class="campo">
                    <option value="">Selecione uma costureira</option>
                    <?php foreach ($costureiras as $costureira): ?>
                        <option value="<?= $costureira['id'] ?>"><?= htmlspecialchars($costureira['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="data_inicio" required class="campo" value="<?= date('Y-m-d') ?>">
                <input type="date" name="data_entrega" required class="campo">
                <button type="submit" class="botao-azul">Vincular Costureira</button>
            </form>
            
            <!-- Finalizar serviço -->
            <form method="POST" action="<?= BASE_URL ?>admin/finalizar-servico?id=<?= $servico['id'] ?>">
                <input type="hidden" name="data_finalizacao" value="<?= date('Y-m-d') ?>">
                <button type="submit" class="botao-verde" onclick="return confirm('Tem certeza que deseja finalizar este serviço?')">
                    Finalizar Serviço
                </button>
            </form>
        </div>
        <?php endif; ?>
        </div>
    </div>
</div>
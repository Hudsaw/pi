2<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap v-center">
            <h2>Serviço #<?= htmlspecialchars($servico['id']) ?></h2>
            <a href="<?= BASE_URL ?>admin/servicos" class="botao-cinza">Voltar</a>
        </div>
        
        <div class="detalhes-servico">
            <div class="info-servico">
                <div class="info-item">
                    <span class="info-label">Lote:</span>
                    <span class="info-value"><?= htmlspecialchars($servico['lote_nome']) ?> - <?= htmlspecialchars($servico['colecao']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Operação:</span>
                    <span class="info-value"><?= htmlspecialchars($servico['operacao_nome']) ?> (R$ <?= number_format($servico['valor_base_operacao'], 2, ',', '.') ?>)</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Quantidade de Peças:</span>
                    <span class="info-value"><?= htmlspecialchars($servico['quantidade_pecas']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Valor da Operação:</span>
                    <span class="info-value">R$ <?= number_format($servico['valor_operacao'], 2, ',', '.') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Valor Total:</span>
                    <span class="info-value">R$ <?= number_format($servico['valor_operacao'] * $servico['quantidade_pecas'], 2, ',', '.') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Envio:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($servico['data_envio'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value status-<?= strtolower($servico['status']) ?>">
                        <?= htmlspecialchars($servico['status']) ?>
                    </span>
                </div>
                <?php if ($servico['data_finalizacao']): ?>
                <div class="info-item">
                    <span class="info-label">Data de Finalização:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($servico['data_finalizacao'])) ?></span>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <span class="info-label">Observação:</span>
                    <span class="info-value"><?= htmlspecialchars($servico['observacao'] ?? 'Nenhuma') ?></span>
                </div>
            </div>
        </div>
        
        <!-- Costureiras vinculadas -->
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
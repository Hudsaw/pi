<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="dashboard-costureira">
        <h2>Meus Serviços - <?= htmlspecialchars($nomeUsuario ?? 'Usuário') ?></h2>
        
        <!-- Serviços Ativos -->
        <div class="servicos-section">
            <h3>Serviços em Andamento</h3>
            <?php if (empty($servicosAtivos)): ?>
                <div class="sem-servicos">
                    <p>Nenhum serviço em andamento no momento.</p>
                </div>
            <?php else: ?>
                <div class="lista-servicos">
                    <?php foreach ($servicosAtivos as $servico): ?>
                        <div class="servico-card servico-ativo">
                            <div class="servico-header">
                                <h4><?= htmlspecialchars($servico['operacao_nome'] ?? 'N/A') ?></h4>
                                <span class="status status-ativo">Em Andamento</span>
                            </div>
                            <div class="servico-info">
                                <div class="info-item">
                                    <strong>Lote:</strong> <?= htmlspecialchars($servico['lote_nome'] ?? 'N/A') ?>
                                </div>
                                <div class="info-item">
                                    <strong>Coleção:</strong> <?= htmlspecialchars($servico['colecao'] ?? 'N/A') ?>
                                </div>
                                <div class="info-item">
                                    <strong>Peças:</strong> <?= htmlspecialchars($servico['quantidade_pecas'] ?? 0) ?>
                                </div>
                                <div class="info-item">
                                    <strong>Valor por peça:</strong> R$ <?= number_format($servico['valor_operacao'] ?? 0, 2, ',', '.') ?>
                                </div>
                                <div class="info-item">
                                    <strong>Valor Total:</strong> R$ <?= number_format(($servico['valor_operacao'] ?? 0) * ($servico['quantidade_pecas'] ?? 0), 2, ',', '.') ?>
                                </div>
                                <div class="info-item">
                                    <strong>Data Início:</strong> <?= isset($servico['data_inicio']) ? date('d/m/Y', strtotime($servico['data_inicio'])) : 'N/A' ?>
                                </div>
                                <div class="info-item">
                                    <strong>Data Entrega:</strong> <?= isset($servico['data_entrega']) ? date('d/m/Y', strtotime($servico['data_entrega'])) : 'N/A' ?>
                                </div>
                                <?php if (isset($servico['data_entrega']) && strtotime($servico['data_entrega']) < strtotime('+3 days')): ?>
                                    <div class="alerta-prazo">
                                        ⚠️ Prazo próximo!
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Serviços Finalizados -->
        <div class="servicos-section">
            <h3>Serviços Finalizados</h3>
            <?php if (empty($servicosFinalizados)): ?>
                <div class="sem-servicos">
                    <p>Nenhum serviço finalizado.</p>
                </div>
            <?php else: ?>
                <div class="lista-servicos">
                    <?php foreach ($servicosFinalizados as $servico): ?>
                        <div class="servico-card servico-finalizado">
                            <div class="servico-header">
                                <h4><?= htmlspecialchars($servico['operacao_nome'] ?? 'N/A') ?></h4>
                                <span class="status status-finalizado">Finalizado</span>
                            </div>
                            <div class="servico-info">
                                <div class="info-item">
                                    <strong>Lote:</strong> <?= htmlspecialchars($servico['lote_nome'] ?? 'N/A') ?>
                                </div>
                                <div class="info-item">
                                    <strong>Coleção:</strong> <?= htmlspecialchars($servico['colecao'] ?? 'N/A') ?>
                                </div>
                                <div class="info-item">
                                    <strong>Peças:</strong> <?= htmlspecialchars($servico['quantidade_pecas'] ?? 0) ?>
                                </div>
                                <div class="info-item">
                                    <strong>Valor Total:</strong> R$ <?= number_format(($servico['valor_operacao'] ?? 0) * ($servico['quantidade_pecas'] ?? 0), 2, ',', '.') ?>
                                </div>
                                <div class="info-item">
                                    <strong>Período:</strong> 
                                    <?= isset($servico['data_inicio']) ? date('d/m/Y', strtotime($servico['data_inicio'])) : 'N/A' ?> 
                                    a 
                                    <?= isset($servico['data_entrega']) ? date('d/m/Y', strtotime($servico['data_entrega'])) : 'N/A' ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

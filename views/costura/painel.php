<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar-costura.php'; ?>
    
    <div class="dashboard-costureira">
        <h2>Meu Painel - <?= htmlspecialchars($nomeUsuario ?? 'Usuário') ?></h2>
        
        <div class="cards-dashboard">
            <!-- Serviços em Andamento -->
            <div class="card">
                <h3>Serviços Ativos</h3>
                <div class="numero"><?= $servicosAtivos ?? 0 ?></div>
                <p>Serviços em produção</p>
            </div>
            
            <!-- Pagamento do Mês -->
            <div class="card">
                <h3>Pagamento Este Mês</h3>
                <div class="numero">R$ <?= number_format($pagamentoMes ?? 0, 2, ',', '.') ?></div>
                <p>Valor estimado</p>
            </div>
            
            <!-- Próximos Prazos -->
            <div class="card">
                <h3>Próximas Entregas</h3>
                <div class="numero"><?= $proximasEntregas ?? 0 ?></div>
                <p>Serviços com prazo próximo</p>
            </div>
        </div>

        <!-- Serviços Ativos -->
        <div class="servicos-ativos">
            <h3>Meus Serviços em Andamento</h3>
            <?php if (empty($servicos)): ?>
                <p>Nenhum serviço em andamento no momento.</p>
            <?php else: ?>
                <div class="lista-servicos">
                    <?php foreach ($servicos as $servico): ?>
                        <div class="servico-item">
                            <h4><?= htmlspecialchars($servico['operacao_nome'] ?? 'N/A') ?></h4>
                            <p><strong>Lote:</strong> <?= htmlspecialchars($servico['lote_nome'] ?? 'N/A') ?></p>
                            <p><strong>Peças:</strong> <?= htmlspecialchars($servico['quantidade_pecas'] ?? 0) ?></p>
                            <p><strong>Valor por peça:</strong> R$ <?= number_format($servico['valor_operacao'] ?? 0, 2, ',', '.') ?></p>
                            <p><strong>Entrega:</strong> 
                                <?= isset($servico['data_entrega']) ? date('d/m/Y', strtotime($servico['data_entrega'])) : 'N/A' ?>
                            </p>
                            <p><strong>Valor Total:</strong> R$ 
                                <?= number_format(($servico['valor_operacao'] ?? 0) * ($servico['quantidade_pecas'] ?? 0), 2, ',', '.') ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
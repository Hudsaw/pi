<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="dashboard-costureira">
        <h2>Meu Painel - <?= htmlspecialchars($nomeUsuario ?? 'Usuário') ?></h2>
        
        <!-- Cards de Resumo (Mantido, pois é um resumo visual importante) -->
        <div class="cards-dashboard">
            <!-- Serviços em Andamento -->
            <div class="card">
                <div class="card-header">
                    <h3>Serviços Ativos</h3>
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <div class="numero"><?= $servicosAtivos ?? 0 ?></div>
                    <p>Serviços em produção</p>
                </div>
            </div>
            
            <!-- Pagamento do Mês -->
            <div class="card">
                <div class="card-header">
                    <h3>Pagamento Este Mês</h3>
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="card-content">
                    <div class="numero">R$ <?= number_format($pagamentoMes ?? 0, 2, ',', '.') ?></div>
                    <p>Valor pago</p>
                </div>
            </div>
            
            <!-- Próximos Prazos -->
            <div class="card">
                <div class="card-header">
                    <h3>Próximas Entregas</h3>
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="card-content">
                    <div class="numero"><?= $proximasEntregas ?? 0 ?></div>
                    <p>Serviços com prazo próximo</p>
                </div>
            </div>
        </div>

        <!-- Seção de Serviços em Andamento (Agora em formato de tabela, como no painel ADM) -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3>Meus Serviços em Andamento</h3>
                <a href="<?= BASE_URL ?>costura/servicos" class="btn-link">Ver todos</a>
            </div>

            <?php if (empty($servicos)): ?>
                <p class="no-data">Nenhum serviço em andamento no momento.</p>
            <?php else: ?>
                <div class="tabela">
                    <table cellspacing='0' class="redondinho shadow filter">
                        <thead>
                            <tr>
                                <th class="ae">Operação</th>
                                <th class="ae">Lote</th>
                                <th class="ad">Qtd. Peças</th>
                                <th class="ad">Valor por Peça</th>
                                <th class="ae">Data Entrega</th>
                                <th class="ae">Status</th>
                                <th class="ac">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicos as $servico): ?>
                                <tr>
                                    <td class="ae"><?= htmlspecialchars($servico['operacao_nome'] ?? 'N/A') ?></td>
                                    <td class="ae"><?= htmlspecialchars($servico['lote_nome'] ?? 'N/A') ?></td>
                                    <td class="ad"><?= number_format($servico['quantidade_pecas'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="ad">R$ <?= number_format($servico['valor_operacao'] ?? 0, 2, ',', '.') ?></td>
                                    <td class="ae">
                                        <?= isset($servico['data_entrega']) ? date('d/m/Y', strtotime($servico['data_entrega'])) : 'N/A' ?>
                                        <?php
                                        // Lógica para destacar prazos próximos (opcional)
                                        if (isset($servico['data_entrega'])) {
                                            $diasRestantes = (strtotime($servico['data_entrega']) - time()) / (60 * 60 * 24);
                                            if ($diasRestantes <= 3 && $diasRestantes > 0) {
                                                echo ' <i class="fas fa-exclamation-triangle" style="color: #f39c12;" title="Prazo próximo!"></i>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="ae">
                                        <span class="status-badge in-progress">
                                            Em Andamento
                                        </span>
                                    </td>
                                    <td class="ac">
                                        <form method="POST" action="<?= BASE_URL ?>costura/visualizar-servico" style="display: inline;">
                                            <input type="hidden" name="servico_id" value="<?= $servico['id'] ?>">
                                            <a href="#" onclick="this.closest('form').submit(); return false;" class="btn-visualizar" title="Visualizar">
                                                <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                            </a>
                                        </form>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
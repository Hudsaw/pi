<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="dashboard-admin">
        <h2>Painel Administrativo</h2>
        <p class="welcome-message">Bem-vindo, <?= htmlspecialchars($nomeUsuario ?? 'Administrador') ?>!</p>
        
        <!-- Cards de Resumo -->
        <div class="cards-dashboard">
            <!-- Usuários -->
            <div class="card">
                <div class="card-header">
                    <h3>Usuários</h3>
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-content">
                    <div class="numero"><?= $totalUsuarios ?? 0 ?></div>
                    <p>Total de usuários cadastrados</p>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>admin/usuarios" class="btn-link">Ver todos</a>
                </div>
            </div>
            
            <!-- Empresas -->
            <div class="card">
                <div class="card-header">
                    <h3>Empresas</h3>
                    <i class="fas fa-building"></i>
                </div>
                <div class="card-content">
                    <div class="numero"><?= $totalEmpresas ?? 0 ?></div>
                    <p>Empresas parceiras</p>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>admin/empresas" class="btn-link">Ver todas</a>
                </div>
            </div>
            
            <!-- Lotes -->
            <div class="card">
                <div class="card-header">
                    <h3>Lotes</h3>
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="card-content">
                    <div class="numero"><?= $totalLotes ?? 0 ?></div>
                    <p>Lotes cadastrados</p>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>admin/lotes" class="btn-link">Ver todos</a>
                </div>
            </div>
            
            <!-- Serviços -->
            <div class="card">
                <div class="card-header">
                    <h3>Serviços</h3>
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <div class="card-content">
                    <div class="numero"><?= $totalServicos ?? 0 ?></div>
                    <p>Total de serviços</p>
                    <small><?= $servicosAtivos ?? 0 ?> ativos</small>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>admin/servicos" class="btn-link">Ver todos</a>
                </div>
            </div>
        </div>

        <!-- Seção de Lotes Recentes -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3>Lotes Recentes</h3>
                <a href="<?= BASE_URL ?>admin/lotes" class="btn-link">Ver todos</a>
            </div>
            <?php if (empty($lotesRecentes)): ?>
                <p class="no-data">Nenhum lote cadastrado recentemente.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Empresa</th>
                                <th>Coleção</th>
                                <th>Data Entrada</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lotesRecentes as $lote): ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL ?>admin/visualizar-lote?id=<?= $lote['id'] ?>">
                                            <?= htmlspecialchars($lote['nome'] ?? 'N/A') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($lote['empresa_nome'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($lote['colecao'] ?? 'N/A') ?></td>
                                    <td><?= isset($lote['data_entrada']) ? date('d/m/Y', strtotime($lote['data_entrada'])) : 'N/A' ?></td>
                                    <td>
                                        <span class="status-badge <?= ($lote['ativo'] ?? false) ? 'active' : 'inactive' ?>">
                                            <?= ($lote['ativo'] ?? false) ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Seção de Serviços Recentes -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3>Serviços Recentes</h3>
                <a href="<?= BASE_URL ?>admin/servicos" class="btn-link">Ver todos</a>
            </div>
            <?php if (empty($servicosRecentes)): ?>
                <p class="no-data">Nenhum serviço cadastrado recentemente.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Operação</th>
                                <th>Lote</th>
                                <th>Quantidade</th>
                                <th>Data Envio</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicosRecentes as $servico): ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL ?>admin/visualizar-servico?id=<?= $servico['id'] ?>">
                                            <?= htmlspecialchars($servico['operacao_nome'] ?? 'N/A') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($servico['lote_nome'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($servico['quantidade_pecas'] ?? 0) ?> peças</td>
                                    <td><?= isset($servico['data_envio']) ? date('d/m/Y', strtotime($servico['data_envio'])) : 'N/A' ?></td>
                                    <td>
                                        <span class="status-badge <?= ($servico['finalizado'] ?? false) ? 'completed' : 'in-progress' ?>">
                                            <?= ($servico['finalizado'] ?? false) ? 'Finalizado' : 'Em Andamento' ?>
                                        </span>
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
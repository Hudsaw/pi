<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="dashboard-admin">
        <h2>Painel Financeiro</h2>
                
        <!-- Cards de Resumo -->
        <div class="cards-dashboard">
            <!-- Receita -->
            <div class="card">
                <div class="card-header">
                    <h3>Receita Total</h3>
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-content">
                    <div class="numero texto-verde">R$ <?= number_format($resumoFinanceiro['receita'] ?? 0, 2, ',', '.') ?></div>
                    <p>Valor total dos serviços</p>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>admin/servicos" class="btn-link">Ver serviços</a>
                </div>
            </div>
            
            <!-- Despesas (Pagamentos realizados) -->
            <div class="card">
                <div class="card-header">
                    <h3>Despesas</h3>
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="card-content">
                    <div class="numero texto-vermelho">R$ <?= number_format($resumoFinanceiro['despesas'] ?? 0, 2, ',', '.') ?></div>
                    <p>Total pago às costureiras</p>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>admin/pagamentos?filtro=pagos" class="btn-link">Ver pagamentos</a>
                </div>
            </div>
            
            <!-- Pagamentos Pendentes -->
            <div class="card">
                <div class="card-header">
                    <h3>Pagamentos Pendentes</h3>
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <div class="numero texto-laranja">R$ <?= number_format($resumoFinanceiro['pendentes'] ?? 0, 2, ',', '.') ?></div>
                    <p><?= $resumoFinanceiro['qtd_pendentes'] ?? 0 ?> pagamentos a realizar</p>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>admin/pagamentos?filtro=pendentes" class="btn-link">Ver pendentes</a>
                </div>
            </div>
            
            <!-- Lucro Líquido -->
            <div class="card">
                <div class="card-header">
                    <h3>Lucro Líquido</h3>
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="card-content">
                    <div class="numero <?= ($resumoFinanceiro['lucro'] ?? 0) >= 0 ? 'texto-verde' : 'texto-vermelho' ?>">
                        R$ <?= number_format($resumoFinanceiro['lucro'] ?? 0, 2, ',', '.') ?>
                    </div>
                    <p>Receita - Despesas</p>
                    <small>Margem: <?= number_format($resumoFinanceiro['margem'] ?? 0, 1) ?>%</small>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>admin/relatorio-pagamentos" class="btn-link">Relatório detalhado</a>
                </div>
            </div>
        </div>

        <!-- Pagamentos Pendentes -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3>Pagamentos Pendentes</h3>
                <a href="<?= BASE_URL ?>admin/pagamentos?filtro=pendentes" class="btn-link">Ver todos</a>
            </div>
            <?php if (empty($pagamentosPendentes)): ?>
                <p class="no-data">Nenhum pagamento pendente no período.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Costureira</th>
                                <th>Período</th>
                                <th>Serviços</th>
                                <th>Valor Bruto</th>
                                <th>Valor Líquido</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagamentosPendentes as $pagamento): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pagamento['costureira_nome'] ?? 'N/A') ?></td>
                                    <td><?= date('m/Y', strtotime($pagamento['periodo_referencia'])) ?></td>
                                    <td><?= $pagamento['total_servicos'] ?? 0 ?> serviço(s)</td>
                                    <td>R$ <?= number_format($pagamento['valor_bruto'] ?? 0, 2, ',', '.') ?></td>
                                    <td class="texto-verde">R$ <?= number_format($pagamento['valor_liquido'] ?? 0, 2, ',', '.') ?></td>
                                    <td class="acoes">
                                        <a href="<?= BASE_URL ?>admin/registrar-pagamento?id=<?= $pagamento['id'] ?>" class="btn-visualizar" title="Visualizar">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                        </a>
                                        <a href="<?= BASE_URL ?>admin/cancelar-pagamento?id=<?= $pagamento['id'] ?>" 
                                           onclick="return confirm('Cancelar este pagamento? O serviço voltará a ficar pendente.')"
                                           class="btn-remover" title="Cancelar">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="cancelar">
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4" class="ae"><strong>Total Pendente</strong></td>
                                <td class="ae"><strong>R$ <?= number_format($totalPendente, 2, ',', '.') ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Gráfico Chart.js -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3>Evolução Financeira</h3><!-- Filtros de Ano e Mês -->
        <div class="filtro-periodo">
            <form method="GET" action="<?= BASE_URL ?>admin/financeiro" class="form-filtros">
                <div class="filtro-group">
                    <label for="ano">Ano:</label>
                    <select name="ano" id="ano" onchange="this.form.submit()">
                        <?php foreach ($anosDisponiveis as $anoOption): ?>
                            <option value="<?= $anoOption ?>" <?= $anoSelecionado == $anoOption ? 'selected' : '' ?>>
                                <?= $anoOption ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filtro-group">
                    <label for="mes">Mês:</label>
                    <select name="mes" id="mes" onchange="this.form.submit()">
                        <option value="">Ano completo</option>
                        <?php 
                        $meses = [
                            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                        ];
                        foreach ($meses as $num => $nome): ?>
                            <option value="<?= $num ?>" <?= $mesSelecionado == $num ? 'selected' : '' ?>>
                                <?= $nome ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
                
            </div>
            
            <?php if (empty($lucroMensal)): ?>
                <p class="no-data">Nenhum dado disponível para o período selecionado.</p>
            <?php else: ?>
                <div class="grafico-container">
                    <canvas id="financeiroChart" style="width: 100%; height: 400px;"></canvas>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Dados para o gráfico
const lucroMensal = <?= json_encode($lucroMensal) ?>;

// Extrair labels e dados
const labels = lucroMensal.map(item => item.mes);
const receitaData = lucroMensal.map(item => item.receita);
const despesaData = lucroMensal.map(item => item.despesa);
const lucroData = lucroMensal.map(item => item.lucro);

// Configurar o gráfico
let financeiroChart = null;

function criarGrafico() {
    const ctx = document.getElementById('financeiroChart').getContext('2d');
    
    if (financeiroChart) {
        financeiroChart.destroy();
    }
    
    financeiroChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Receita (Faturamento)',
                    data: receitaData,
                    backgroundColor: 'rgba(46, 204, 113, 0.7)',
                    borderColor: '#2ecc71',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Despesa (Pagamentos)',
                    data: despesaData,
                    backgroundColor: 'rgba(231, 76, 60, 0.7)',
                    borderColor: '#e74c3c',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Lucro Líquido',
                    data: lucroData,
                    type: 'line',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderColor: '#3498db',
                    borderWidth: 3,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3,
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            return `${label}: R$ ${value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                        }
                    },
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                },
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 10
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    },
                    title: {
                        display: true,
                        text: 'Valor (R$)',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Período',
                        font: {
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

// Inicializar gráfico quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    if (lucroMensal.length > 0) {
        criarGrafico();
    }
});

</script>

<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="conteudo-tabela">
        <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Meus Serviços</h2>
            <a href="<?= BASE_URL ?>costura/painel" class="botao">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
        
        <!-- Filtro apenas por data (opcional, como você mencionou) -->
        <div class="filtro flex s-gap" style="margin-bottom: 20px;">
            <div class="flex v-center" style="gap: 10px;">
                <label for="data_filtro">Filtrar por data de envio:</label>
                <input type="date" id="data_filtro" onchange="filtrarPorData(this.value)">
                <button class="botao-azul" onclick="limparFiltro()">Limpar Filtro</button>
            </div>
        </div>

        <!-- Contador de registros -->
        <div id="resultado-info" style="margin-bottom: 10px;">
            <span id="contador-registros">Mostrando <?= count($servicos ?? []) ?> serviços</span>
        </div>

        <!-- Tabela de serviços -->
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow" id="tabela-servicos">
                <thead>
                    <tr>
                        <th class="ae">Lote</th>
                        <th class="ae">Coleção</th>
                        <th class="ae">Operação</th>
                        <th class="ae">Qtd. Peças</th>
                        <th class="ae">Data Envio</th>
                        <th class="ae">Status</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($servicos)): ?>
                        <tr>
                            <td colspan="7" class="ac">Nenhum serviço encontrado</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($servicos as $servico): ?>
                            <tr data-data-envio="<?= $servico['data_envio'] ?? '' ?>">
                                <td class="ae">
                                    <strong><?= htmlspecialchars($servico['lote_nome'] ?? 'N/A') ?></strong>
                                    
                                </td>
                                <td class="ae"><?= htmlspecialchars($servico['colecao'] ?? '') ?></td>
                                <td class="ae"><?= htmlspecialchars($servico['operacao_nome'] ?? 'N/A') ?></td>
                                <td class="ae"><?= number_format($servico['quantidade_pecas'] ?? 0, 0, ',', '.') ?></td>
                                <td class="ae">
                                    <?= isset($servico['data_envio']) ? date('d/m/Y', strtotime($servico['data_envio'])) : 'N/A' ?>
                                </td>
                                <td class="ae">
                                    <?php if (($servico['status'] ?? '') == 'Finalizado'): ?>
                                        <span class="status-badge completed">Finalizado</span>
                                    <?php else: ?>
                                        <span class="status-badge in-progress">Em andamento</span>
                                    <?php endif; ?>
                                </td>
                                <td class="ac">
                                    <form method="POST" action="<?= BASE_URL ?>costura/visualizar-servico" style="display: inline;">
                                        <input type="hidden" name="servico_id" value="<?= $servico['id'] ?>">
                                        <button type="submit" class="btn-visualizar" title="Visualizar">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                        </button>
                                    </form>
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
function filtrarPorData(dataSelecionada) {
    const linhas = document.querySelectorAll('#tabela-servicos tbody tr');
    let contadorVisiveis = 0;
    
    linhas.forEach(linha => {
        // Pular linha de "Nenhum serviço encontrado"
        if (linha.cells.length <= 1 || linha.querySelector('td[colspan]')) {
            return;
        }
        
        const dataEnvio = linha.getAttribute('data-data-envio');
        
        if (!dataSelecionada) {
            // Se não há data selecionada, mostra todas
            linha.style.display = '';
            contadorVisiveis++;
        } else {
            // Compara a data (ignorando o horário)
            if (dataEnvio) {
                const dataLinha = new Date(dataEnvio + 'T00:00:00');
                const dataFiltro = new Date(dataSelecionada + 'T00:00:00');
                
                if (dataLinha.getTime() === dataFiltro.getTime()) {
                    linha.style.display = '';
                    contadorVisiveis++;
                } else {
                    linha.style.display = 'none';
                }
            } else {
                linha.style.display = 'none';
            }
        }
    });
    
    // Atualiza contador
    const contadorElement = document.getElementById('contador-registros');
    if (contadorElement) {
        contadorElement.textContent = `Mostrando ${contadorVisiveis} serviços`;
    }
}

function limparFiltro() {
    document.getElementById('data_filtro').value = '';
    filtrarPorData('');
}
</script>
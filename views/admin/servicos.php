<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
<div class="conteudo-tabela">
    <h2>Serviços</h2>
    <div class="filtro flex s-gap">
            <input type="text" id="filtro" placeholder="Digite sua busca" onkeyup="filtrarServico()">
            <span class="flex v-center">
                <input type="checkbox" id="inativos" onchange="filtrarServicoInativos(this)">
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
                    <th class="ae">Data Envio</th>
                    <th class="ae">Status</th>
                    <th class="ac">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaServicos)): ?>
                    <tr>
                        <td colspan="7" class="ac">Nenhum serviço encontrado</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaServicos as $servico): ?>
                        <tr>
                            <td class="ae"><?= htmlspecialchars($servico['lote_nome']) ?></td>
                            <td class="ae"><?= htmlspecialchars($servico['operacao_nome']) ?></td>
                            <td class="ae">
                                <?= $servico['costureira_nome'] ? htmlspecialchars($servico['costureira_nome']) : '<span class="texto-vermelho">Não vinculada</span>' ?>
                            </td>
                            <td class="ae"><?= htmlspecialchars($servico['quantidade_pecas']) ?></td>
                            <td class="ae"><?= date('d/m/Y', strtotime($servico['data_envio'])) ?></td>
                            <td class="ae status-<?= strtolower($servico['status']) ?>"><?= htmlspecialchars($servico['status']) ?></td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>admin/visualizar-servico?id=<?= $servico['id'] ?>" class="btn-visualizar" title="Visualizar">
                                    <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                </a>
                                <a href="<?= BASE_URL ?>admin/editar-servico?id=<?= $servico['id'] ?>"> 
                                    <img class="icone" src="<?php echo ASSETS_URL?>icones/editar.svg" alt="editar">
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

<script>
function filtrarServico() {
    const filtro = document.getElementById('filtro').value.toLowerCase();
    const checkboxInativos = document.getElementById('inativos');
    const mostrarInativos = checkboxInativos.checked;
    const linhas = document.querySelectorAll('.tabela tbody tr');
    
    linhas.forEach(linha => {
        if (linha.cells.length <= 1) return; // Pular linha de "Nenhum serviço encontrada"
        
        const textoLinha = linha.textContent.toLowerCase();
        const statusCell = linha.querySelector('td:nth-child(6)'); // Coluna do status
        const status = statusCell ? statusCell.textContent.toLowerCase().trim() : '';
        
        // Verificar se a linha corresponde ao filtro de texto
        const correspondeFiltro = textoLinha.includes(filtro);
        
        // Verificar se deve mostrar baseado no status
        const mostrarPorStatus = mostrarInativos || status !== 'inativo';
        
        // Mostrar/ocultar linha
        if (correspondeFiltro && mostrarPorStatus) {
            linha.style.display = '';
        } else {
            linha.style.display = 'none';
        }
    });
}

function filtrarServicoInativos(checkbox) {
    filtrarServico(); // Reaplica o filtro quando o checkbox muda
}
</script>
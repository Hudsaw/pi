<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap">
            <input type="text" id="filtro" placeholder="Digite sua busca (nome, coleção ou empresa)" onkeyup="filtrarLotes()">
            <span class="flex v-center">
                <input type="checkbox" id="inativos" onchange="filtrarLotesInativos(this)">
                <label class="flex v-center" for="inativos">Mostrar Inativos</label>
            </span>
            <a href="<?= BASE_URL ?>admin/criar-lote" class="botao-azul">Criar Lote</a>
        </div>
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow" id="tabelaLotes">
                <thead>
                    <tr>
                        <th class="ae">Empresa</th>
                        <th class="ae">Coleção</th>
                        <th class="ae">Nome</th>
                        <th class="ae">Data Entrada</th>
                        <th class="ae">Valor Total</th>
                        <th class="ae">Status</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaLotes)): ?>
                        <tr>
                            <td colspan="8" class="ac">Nenhum lote encontrado</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaLotes as $lote): ?>
                            <tr class="linha-lote" data-ativo="<?= $lote['ativo'] ? '1' : '0' ?>">
                                <td class="ae"><?= htmlspecialchars($lote['empresa_id']) ?></td>
                                <td class="ae"><?= htmlspecialchars($lote['colecao']) ?></td>
                                <td class="ae"><?= htmlspecialchars($lote['nome']) ?></td>
                                <td class="ae"><?= date('d/m/Y', strtotime($lote['data_entrada'])) ?></td>
                                <td class="ae">R$ <?= number_format($lote['valor_total'], 2, ',', '.') ?></td>
                                <td class="ae">
                                    <span class="status-<?= strtolower($lote['status']) ?>">
                                        <?= htmlspecialchars($lote['status']) ?>
                                    </span>
                                </td>
                                <td class="ac">
                                    <a href="<?= BASE_URL ?>admin/visualizar-lote?id=<?= $lote['id'] ?>" title="Visualizar">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                    </a>
                                    <a href="<?= BASE_URL ?>admin/editar-lote?lote_id=<?= $lote['id'] ?>" title="Editar lote">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/editar.svg" alt="Editar lote">
                                    </a>
                                    <?php if ($lote['status'] === 'Aberto'): ?>
                                        <a href="<?= BASE_URL ?>admin/remover-lote?id=<?= $lote['id'] ?>" 
                                           onclick="return confirm('Tem certeza que deseja remover este lote?')"
                                           title="Remover Lote">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover">
                                        </a>
                                    <?php else: ?>
                                        <span class="icone-disabled" title="Lote não pode ser removido">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover" style="opacity: 0.3;">
                                        </span>
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
document.addEventListener('DOMContentLoaded', function() {
    esconderLotesInativos();
});

function filtrarLotes() {
    const input = document.getElementById('filtro');
    const filter = input.value.trim().toUpperCase();
    const table = document.getElementById('tabelaLotes');
    const rows = table.querySelectorAll('.linha-lote');
    
    rows.forEach(row => {
        const empresa = row.cells[1].textContent.toUpperCase();
        const colecao = row.cells[2].textContent.toUpperCase();
        const nome = row.cells[3].textContent.toUpperCase();
        
        const match = empresa.includes(filter) || colecao.includes(filter) || nome.includes(filter);
        row.style.display = match ? '' : 'none';
    });
}

function filtrarLotesInativos(elemento) {
    if (elemento.checked) {
        listarLotesInativos(); 
    } else {
        esconderLotesInativos();
    } 
}

function esconderLotesInativos() {
    const table = document.getElementById('tabelaLotes');
    const rows = table.querySelectorAll('.linha-lote');
    
    rows.forEach(row => {
        const ativo = row.getAttribute('data-ativo');
        row.style.display = ativo === '1' ? '' : 'none';
    });
}

function listarLotesInativos() {
    const table = document.getElementById('tabelaLotes');
    const rows = table.querySelectorAll('.linha-lote');
    
    rows.forEach(row => {
        row.style.display = "";
    });
}
</script>
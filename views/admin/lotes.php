<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a href="<?= BASE_URL ?>admin/painel" class="item">Painel</a>
        <a href="<?= BASE_URL ?>admin/usuarios" class="item">Usuários</a>
        <a href="<?= BASE_URL ?>admin/lotes" class="item bold">Lotes</a>
        <a href="<?= BASE_URL ?>admin/operacoes" class="item">Operações</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap">
            <div class="filtro flex s-gap">
                <input type="text" id="filtro" placeholder="Digite sua busca (nome ou coleção)" onkeyup="filtrarLotes()">
                <span class="flex v-center">
                    <input type="checkbox" id="inativos" onchange="filtrarLotesInativos(this)">
                    <label for="inativos">Mostrar Inativos</label>
                </span>
                <a href="<?= BASE_URL ?>admin/criar-lote" class="botao-azul">Criar Lote</a>
            </div>
        </div>
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow" id="tabelaLotes">
                <thead>
                    <tr>
                        <th class="ae">ID</th>
                        <th class="ae">Descrição</th>
                        <th class="ae">Quantidade</th>
                        <th class="ae">Valor</th>
                        <th class="ae">Data Início</th>
                        <th class="ae">Data Prazo</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaLotes as $lote): ?>
                        <tr class="linha-lote" data-ativo="<?= $lote['ativo'] ? '1' : '0' ?>">
                            <td class="ae"><?= htmlspecialchars($lote['id']) ?></td>
                            <td class="ae"><?= htmlspecialchars($lote['descricao']) ?></td>
                            <td class="ae"><?= htmlspecialchars($lote['quantidade']) ?></td>
                            <td class="ae">R$ <?= number_format($lote['valor'], 2, ',', '.') ?></td>
                            <td class="ae"><?= date('d/m/Y', strtotime($lote['data_inicio'])) ?></td>
                            <td class="ae"><?= date('d/m/Y', strtotime($lote['data_prazo'])) ?></td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>admin/visualizar-lote?id=<?= $lote['id'] ?>">
                                    <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                </a>
                                <a href="<?= BASE_URL ?>admin/adicionar-peca?lote_id=<?= $lote['id'] ?>">
                                    <img class="icone" src="<?= ASSETS_URL ?>icones/editar.svg" alt="adicionar peça">
                                </a>
                                <a href="<?= BASE_URL ?>admin/remover-lote?id=<?= $lote['id'] ?>" onclick="return confirm('Tem certeza que deseja remover este lote?')">
                                    <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    esconderLotesInativas();
});

function filtrarLotes() {
    const input = document.getElementById('filtro');
    const filter = input.value.trim().toUpperCase();
    const table = document.getElementById('tabelaLotes');
    const rows = table.querySelectorAll('.linha-lote');
    
    rows.forEach(row => {
        const nome = row.cells[1].textContent.toUpperCase();
        
        const match = nome.includes(filter);
        row.style.display = match ? '' : 'none';
    });
}


function filtrarLotesInativas(elemento) {
    if (elemento.checked) {
        listarLotesInativas(); 
    } else {
        esconderLotesInativas();
    } 
}

function esconderLotesInativas() {
    const table = document.getElementById('tabelaLotes');
    const rows = table.querySelectorAll('.linha-lote');
    
    rows.forEach(row => {
        const ativo = row.getAttribute('data-ativo');
        row.style.display = ativo === '1' ? '' : 'none';
    });
}

function listarLotesInativas() {
    const table = document.getElementById('tabelaLotes');
    const rows = table.querySelectorAll('.linha-lote');
    
    rows.forEach(row => {
        row.style.display = "";
    });
}
</script>
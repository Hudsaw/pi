<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a href="<?= BASE_URL ?>admin/painel" class="item">Painel</a>
        <a href="<?= BASE_URL ?>admin/usuarios" class="item">Usuários</a>
        <a href="<?= BASE_URL ?>admin/lotes" class="item">Lotes</a>
        <a href="<?= BASE_URL ?>admin/operacoes" class="item bold">Operações</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap">
            <input type="text" id="filtro" placeholder="Digite sua busca" onkeyup="filtrarOperacoes()">
            <span class="flex v-center">
                <input type="checkbox" id="inativos" onchange="filtrarOperacoesInativas(this)">
                <label for="inativos">Mostrar Inativos</label>
            </span>
            <a href="<?= BASE_URL ?>admin/criar-operacao" class="botao-azul">Criar Operação</a>
        </div>
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow" id="tabelaOperacoes">
                <thead>
                    <tr>
                        <th class="ae">Operação</th>
                        <th class="ae">Valor (R$)</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaOperacoes as $operacao): ?>
                        <tr class="linha-operacao" data-ativo="<?= $operacao['ativo'] ? '1' : '0' ?>">
                            <td class="ae"><?= htmlspecialchars($operacao['nome']) ?></td>
                            <td class="ae">R$ <?= number_format($operacao['valor'], 2, ',', '.') ?></td>
                            <td class="ac">
                                <?php if ($operacao['ativo']): ?>
                                    <a href="<?= BASE_URL ?>admin/remover-operacao?id=<?= $operacao['id'] ?>" onclick="return confirm('Tem certeza que deseja remover esta operação?')">
                                        <img class="icone" src="<?php echo ASSETS_URL?>icones/remover.svg" alt="remover">
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>admin/reativar-operacao?id=<?= $operacao['id'] ?>">
                                        <img class="icone" src="<?php echo ASSETS_URL?>icones/reativar.svg" alt="reativar"> 
                                    </a>
                                <?php endif; ?>
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
    esconderOperacoesInativas();
});

function filtrarOperacoes() {
    const input = document.getElementById('filtro');
    const filter = input.value.trim().toUpperCase();
    const table = document.getElementById('tabelaOperacoes');
    const rows = table.querySelectorAll('.linha-operacao');
    
    rows.forEach(row => {
        const nome = row.cells[0].textContent.toUpperCase();
        
        const match = nome.includes(filter);
        row.style.display = match ? '' : 'none';
    });
}


function filtrarOperacoesInativas(elemento) {
    if (elemento.checked) {
        listarOperacoesInativas(); 
    } else {
        esconderOperacoesInativas();
    } 
}

function esconderOperacoesInativas() {
    const table = document.getElementById('tabelaOperacoes');
    const rows = table.querySelectorAll('.linha-operacao');
    
    rows.forEach(row => {
        const ativo = row.getAttribute('data-ativo');
        row.style.display = ativo === '1' ? '' : 'none';
    });
}

function listarOperacoesInativas() {
    const table = document.getElementById('tabelaOperacoes');
    const rows = table.querySelectorAll('.linha-operacao');
    
    rows.forEach(row => {
        row.style.display = "";
    });
}


</script>
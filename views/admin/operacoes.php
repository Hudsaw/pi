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
            <div class="filtros flex">
                <a href="<?= BASE_URL ?>admin/operacoes?filtro=ativos" class="<?= ($filtro === 'ativos') ? 'ativo' : '' ?>">Ativas</a>
                <a href="<?= BASE_URL ?>admin/operacoes?filtro=inativos" class="<?= ($filtro === 'inativos') ? 'ativo' : '' ?>">Inativas</a>
                <a href="<?= BASE_URL ?>admin/operacoes?filtro=todos" class="<?= ($filtro === 'todos') ? 'ativo' : '' ?>">Todas</a>
            </div>
            <a href="<?= BASE_URL ?>admin/criar-operacao" class="botao-azul">Criar Operação</a>
        </div>
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow" id="tabelaOperacoes">
                <thead>
                    <tr>
                        <th class="ae">ID</th>
                        <th class="ae">Nome</th>
                        <th class="ae">Descrição</th>
                        <th class="ae">Valor (R$)</th>
                        <th class="ae">Tempo Estimado</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaOperacoes as $operacao): ?>
                        <tr class="linha-operacao">
                            <td class="ae"><?= htmlspecialchars($operacao['id']) ?></td>
                            <td class="ae"><?= htmlspecialchars($operacao['nome']) ?></td>
                            <td class="ae"><?= htmlspecialchars($operacao['descricao']) ?></td>
                            <td class="ae">R$ <?= number_format($operacao['valor'], 2, ',', '.') ?></td>
                            <td class="ae"><?= htmlspecialchars($operacao['tempo_estimado']) ?> min</td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>admin/remover-operacao?id=<?= $operacao['id'] ?>" onclick="return confirm('Tem certeza que deseja remover esta operação?')">
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
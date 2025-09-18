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
            <div class="filtros flex">
                <a href="<?= BASE_URL ?>admin/lotes?filtro=ativos" class="<?= ($filtro === 'ativos') ? 'ativo' : '' ?>">Ativos</a>
                <a href="<?= BASE_URL ?>admin/lotes?filtro=inativos" class="<?= ($filtro === 'inativos') ? 'ativo' : '' ?>">Inativos</a>
                <a href="<?= BASE_URL ?>admin/lotes?filtro=todos" class="<?= ($filtro === 'todos') ? 'ativo' : '' ?>">Todos</a>
            </div>
            <a href="<?= BASE_URL ?>admin/criar-lote" class="botao-azul">Criar Lote</a>
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
                        <tr class="linha-lote">
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
                                    <img class="icone" src="<?= ASSETS_URL ?>icones/adicionar.svg" alt="adicionar peça">
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
<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap">
            <input type="text" id="filtro" placeholder="Digite sua busca" onkeyup="filtrarOperacoes()">
            <span class="flex v-center">
                <input type="checkbox" id="inativos" onchange="filtrarOperacoesInativos(this)">
                <label class="flex v-center" for="inativos">Mostrar Inativos</label>
            </span>
            <a href="<?= BASE_URL ?>admin/criar-operacao" class="botao-azul">Criar operação</a>
        </div>  
        
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow">
                <thead>
                    <tr>
                        <th class="ae">ID</th>
                        <th class="ae">Nome</th>
                        <th class="ae">Valor (R$)</th>
                        <th class="ae">Status</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaOperacoes)): ?>
                        <tr>
                            <td colspan="5" class="ac">Nenhuma operação encontrada</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaOperacoes as $operacao): ?>
                            <tr>
                                <td class="ae"><?= htmlspecialchars($operacao['id']) ?></td>
                                <td class="ae"><?= htmlspecialchars($operacao['nome']) ?></td>
                                <td class="ae">R$ <?= number_format($operacao['valor'], 2, ',', '.') ?></td>
                                <td class="ae">
                                    <span class="status-<?= $operacao['ativo'] ? 'ativo' : 'inativo' ?>">
                                        <?= $operacao['ativo'] ? 'Ativa' : 'Inativa' ?>
                                    </span>
                                </td>
                                <td class="ac">
                                    <a href="<?= BASE_URL ?>admin/editar-operacao?id=<?= $operacao['id'] ?>" title="Editar Operação">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/editar.svg" alt="editar">
                                    </a>
                                    <?php if ($operacao['ativo']): ?>
                                        <a href="<?= BASE_URL ?>admin/remover-operacao?id=<?= $operacao['id'] ?>" 
                                           onclick="return confirm('Tem certeza que deseja desativar esta operação?')"
                                           title="Desativar Operação">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="desativar">
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>admin/reativar-operacao?id=<?= $operacao['id'] ?>" 
                                           onclick="return confirm('Tem certeza que deseja reativar esta operação?')"
                                           title="Reativar Operação">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/reativar.svg" alt="reativar">
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
</div>

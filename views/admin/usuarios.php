<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <div class="conteudo-tabela">
    <h2>Usuários</h2>
        <div class="filtro flex s-gap">
            <input type="text" id="filtro" placeholder="Digite sua busca (nome ou especialidade)" onkeyup="filtrarBusca()">
            <span class="flex v-center">
                <input type="checkbox" id="inativos" onchange="filtrarInativos(this)">
                <label class="flex v-center" for="inativos">Mostrar Inativos</label>
            </span>
            <a href="<?= BASE_URL ?>admin/criar-usuario" class="botao-azul">Criar usuario</a>
        </div>
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow filter">
                <thead>
                    <tr>
                        <th class="ae">Nome</th>
                        <th class="ae">Cidade</th>
                        <th class="ae">Especialidade</th>
                        <th class="ae">Telefone</th>
                        <th class="ae">Status</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaUsuarios as $usuario): ?>
                        <tr class="linha-filter" data-ativo="<?= $usuario['ativo'] ? '1' : '0' ?>">
                            <td class="ae"><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td class="ae"><?= htmlspecialchars($usuario['cidade']) ?></td>
                            <td class="ae"><?= htmlspecialchars($usuario['especialidade'] ?? 'N/A') ?></td>
                            <td class="ae" id="telefone"><?= htmlspecialchars($usuario['telefone']) ?></td>
                            <td class="ae"> <span class="status-badge <?=$usuario['ativo'] ? "active":"inactive" ?> "> <?= htmlspecialchars($usuario['ativo'] ? "Ativo":"Inativo") ?></span></td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>admin/visualizar-usuario?id=<?= $usuario['id'] ?>">
                                    <img class="icone" src="<?php echo ASSETS_URL?>icones/visualizar.svg" alt="visualizar">
                                </a>
                                <a href="<?= BASE_URL ?>admin/editar-usuario?id=<?= $usuario['id'] ?>">
                                    <img class="icone" src="<?php echo ASSETS_URL?>icones/editar.svg" alt="editar">
                                </a>
                                <?php if ($usuario['ativo']): ?>
                                    <a href="<?= BASE_URL ?>admin/remover-usuario?id=<?= $usuario['id'] ?>">
                                        <img class="icone" src="<?php echo ASSETS_URL?>icones/remover.svg" alt="remover">
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>admin/reativar-usuario?id=<?= $usuario['id'] ?>">
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
<script src="<?= ASSETS_URL ?>js/utils.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    formatarDadosExibidos();
});
</script>
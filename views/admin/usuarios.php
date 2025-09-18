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
            <input type="text" id="filtro" placeholder="Digite sua busca (nome ou especialidade)" onkeyup="filtrarUsuarios()">
            <a href="<?= BASE_URL ?>admin/criar-usuario" class="botao-azul">Criar usuario</a>
        </div>
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow" id="tabelaUsuarios">
                <thead>
                    <tr>
                        <th class="ae">Nome</th>
                        <th class="ae">Telefone</th>
                        <th class="ae">CPF</th>
                        <th class="ae">Cidade</th>
                        <th class="ae">Especialidade</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaUsuarios as $usuario): ?>
                        <tr class="linha-usuario">
                            <td class="ae"><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td class="ae" id="telefone"><?= htmlspecialchars($usuario['telefone']) ?></td>
                            <td class="ae" id="cpf"><?= htmlspecialchars($usuario['cpf']) ?></td>
                            <td class="ae"><?= htmlspecialchars($usuario['cidade']) ?></td>
                            <td class="ae"><?= htmlspecialchars($usuario['especialidade'] ?? 'N/A') ?></td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>admin/visualizar-usuario?id=<?= $usuario['id'] ?>">
                                    <img class="icone" src="<?php echo ASSETS_URL?>icones/visualizar.svg" alt="visualizar">
                                </a>
                                <a href="<?= BASE_URL ?>admin/editar-usuario?id=<?= $usuario['id'] ?>">
                                    <img class="icone" src="<?php echo ASSETS_URL?>icones/editar.svg" alt="editar">
                                </a>
                                <a href="<?= BASE_URL ?>admin/remover-usuario?id=<?= $usuario['id'] ?>">
                                    <img class="icone" src="<?php echo ASSETS_URL?>icones/remover.svg" alt="remover">
                                </a>
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

function filtrarUsuarios() {
    const input = document.getElementById('filtro');
    const filter = input.value.trim().toUpperCase();
    const table = document.getElementById('tabelaUsuarios');
    const rows = table.querySelectorAll('.linha-usuario');
    
    rows.forEach(row => {
        const nome = row.cells[0].textContent.toUpperCase();
        const especialidade = row.cells[4].textContent.toUpperCase();
        
        const match = nome.includes(filter) || especialidade.includes(filter);
        row.style.display = match ? '' : 'none';
    });
}
</script>
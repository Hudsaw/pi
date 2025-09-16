<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a class="item bold">Usuários</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap">
            <input type="text" id="filtro" placeholder="Digite sua busca">
            <a href="<?= BASE_URL ?>admin/criar-usuario" class="botao-azul">Criar usuario</a>
        </div>
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow">
                <thead>
                    <tr>
                        <th class="ae">Nome</th>
                        <th class="ae">Telefone</th>
                        <th class="ae">CPF</th>
                        <th class="ae">Cidade</th>
                        <th class="ac">Visualizar</th>
                        <th class="ac">Editar</th>
                        <th class="ac">Remover</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaUsuarios as $usuario): ?>
                        <tr>
                            <td class="ae"><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td class="ae" id="telefone"><?= htmlspecialchars($usuario['telefone']) ?></td>
                            <td class="ae" id="cpf"><?= htmlspecialchars($usuario['cpf']) ?></td>
                            <td class="ae"><?= htmlspecialchars($usuario['cidade']) ?></td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>admin/visualizar-usuario?id=<?= $usuario['id'] ?>">
                                    <img class="icone" src="<?php echo ASSETS_URL?>icones/visualizar.svg" alt="visualizar">
                                </a>
                            </td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>admin/editar-usuario?id=<?= $usuario['id'] ?>">
                                    <img class="icone" src="<?php echo ASSETS_URL?>icones/editar.svg" alt="editar">
                                </a>
                            </td>
                            <td class="ac">
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

    function exportarUsuarios() {
        const form = document.getElementById('searchForm');
        const tipo = document.getElementById('userTypeFilter').value;
        const search = document.getElementById('userSearchInput').value;

        window.open(
            `<?php echo BASE_URL?>admin/exportarUsuarios?tipo=${tipo}&search=${encodeURIComponent(search)}`,
            '_blank'
        );
    }

    function filterByUserType() {
        const tipo = document.getElementById('userTypeFilter').value;
        const searchTerm = document.getElementById('userSearchInput').value;

        let url = `?tipo=${tipo}&pagina=1`;

        if (searchTerm) {
            url += `&search=${encodeURIComponent(searchTerm)}`;
        }

        window.location.href = url;
    }



        // Filtros
        function filterUserTable() {
            const input = document.getElementById('userSearchInput');
            const filter = input.value.trim().toUpperCase();
            const table = document.getElementById('usersTable');
            const rows = table.querySelectorAll('tbody tr:not(.no-results)');
            let anyVisible = false;

            if (filter === '') {
                rows.forEach(row => row.style.display = '');
                document.querySelector('.no-results').style.display = 'none';
                return;
            }

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 4) {
                    const id = cells[0].textContent.toUpperCase();
                    const nome = cells[1].textContent.toUpperCase();
                    const especialidade = cells[2].textContent.toUpperCase();

                    const match = id.includes(filter) ||
                        nome.includes(filter) ||
                        especialidade.includes(filter);

                    row.style.display = match ? '' : 'none';
                    if (match) anyVisible = true;
                }
            });

            const noResultsRow = table.querySelector('.no-results');
            if (noResultsRow) {
                noResultsRow.style.display = anyVisible ? 'none' : '';
            }
        }

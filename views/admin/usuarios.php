<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a class="item bold">Usuários</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <!-- <div class="cards">
        <a href="<?= BASE_URL ?>admin/criar-usuario" class="card novo-usuario">Criar usuário</a>
        <?php foreach ($listaUsuarios as $usuario): ?>
            <a href="<?= BASE_URL ?>admin/visualizar-usuario?id=<?= $usuario['id'] ?>" class="card"><?= htmlspecialchars($usuario['nome']) ?><br><br><?= htmlspecialchars($usuario['especialidade']) ?></a>
        <?php endforeach; ?>
    </div> -->
    <div class="tabela">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>CPF</th>
                    <th>Cidade</th>
                    <th>Visualizar</th>
                    <th>Editar</th>
                    <th>Remover</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaUsuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['telefone']) ?></td>
                        <td><?= htmlspecialchars($usuario['cpf']) ?></td>
                        <td><?= htmlspecialchars($usuario['cidade']) ?></td>
                        <td><a href="<?= BASE_URL ?>admin/visualizar-usuario?id=<?= $usuario['id'] ?>">Visualizar</a></td>
                        <td>Editar</td>
                        <td>Remover</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
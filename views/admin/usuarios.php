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
                            <td class="ae"><?= htmlspecialchars($usuario['telefone']) ?></td>
                            <td class="ae"><?= htmlspecialchars($usuario['cpf']) ?></td>
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
<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a class="item bold">Usuários</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <div class="cards">
        <a href="<?= BASE_URL ?>admin/criar-usuario" class="card novo-usuario">Criar usuário</a><!-- Fazer ícone -->
        <?php foreach ($listaUsuarios as $usuario): ?>
            <a href="<?= BASE_URL ?>admin/visualizar-usuario?id=<?= $usuario['id'] ?>" class="card"><?= htmlspecialchars($usuario['nome']) ?><br><br><?= htmlspecialchars($usuario['especialidade']) ?></a>
        <?php endforeach; ?>
    </div>
</div>
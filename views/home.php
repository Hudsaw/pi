<main class="container">
    <section class="hero card">
        <h1><?= $dados['titulo'] ?? 'Bem-vindo' ?></h1>
        <p><?= $dados['descricao'] ?? 'Sistema de Gestão de Facção' ?></p>
        <?php if (isset($dados['usuario'])): ?>
            <a href="<?= BASE_URL ?>/cadastro" class="btn">Editar cadastro</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/cadastro" class="btn">Cadastre-se</a>
        <?php endif; ?>
    </section>
</main>
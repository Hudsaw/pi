<main class="container">
    <section class="card">
        <h1><?php echo $titulo ?></h1>

        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['mensagem'] ?></div>
            <?php unset($_SESSION['mensagem']); ?>
<?php endif; ?>

        <div class="admin-actions">
            <a href="<?php echo BASE_URL ?>/adicionar" class="btn">Adicionar Nova Pergunta</a>
            <span class="pagination-info">
                Mostrando                          <?php echo count($perguntas) ?> de<?php echo $totalPerguntas ?> perguntas
            </span>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pergunta</th>
                    <th>Área</th>
                    <th>Nível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($perguntas as $pergunta): ?>
                    <tr>
                        <td><?php echo $pergunta['id'] ?></td>
                        <td><?php echo htmlspecialchars($pergunta['pergunta']) ?></td>
                        <td><?php echo htmlspecialchars($pergunta['area_nome']) ?></td>
                        <td><?php echo htmlspecialchars($pergunta['nivel_nome']) ?></td>
                        <td class="actions">
                            <?php if ($pergunta['ativa']): ?>
        <form action="<?php echo BASE_URL?>/toggleStatus" method="POST" >
            <input type="hidden" name="id" value="<?php echo $pergunta['id']?>">
            <input type="hidden" name="page" value="<?php echo $paginaAtual?>">
            <button type="submit" class="btn btn-sm btn-disable">Desativar</button>
        </form>
    <?php else: ?>
        <form action="<?php echo BASE_URL?>/toggleStatus" method="POST" >
            <input type="hidden" name="id" value="<?php echo $pergunta['id']?>">
            <input type="hidden" name="page" value="<?php echo $paginaAtual?>">
            <button type="submit" class="btn btn-sm btn-enable">Ativar</button>
        </form>
                            <?php endif; ?>
                            <form action="<?php echo BASE_URL ?>/excluir" method="POST">
    <input type="hidden" name="id" value="<?php echo $pergunta['id'] ?>">
    <button type="submit" class="btn btn-sm btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta pergunta?')">Excluir</button>
</form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginação -->
       <?php if ($totalPaginas > 1): ?>
    <div class="pagination">
        <?php if ($paginaAtual > 1): ?>
            <a href="<?php echo BASE_URL ?>/admin?page=<?php echo $paginaAtual - 1 ?>" class="btn">&laquo; Anterior</a>
        <?php endif; ?>

        <span class="page-info">Página                                        <?php echo $paginaAtual ?> de<?php echo $totalPaginas ?></span>

        <?php if ($paginaAtual < $totalPaginas): ?>
            <a href="<?php echo BASE_URL ?>/admin?page=<?php echo $paginaAtual + 1 ?>" class="btn">Próxima &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
    </section>
</main>
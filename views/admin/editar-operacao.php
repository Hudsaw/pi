<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <form class="formulario-cadastro auth-form" method="POST" action="<?= BASE_URL ?>admin/atualizar-operacao">
        <div class="titulo">Editar Operação</div>
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <input type="hidden" name="id" value="<?= htmlspecialchars($operacao['id']) ?>">
        
        <hr class="shadow">
        <span class="inputs flex center">
            <span class="label flex vertical">
                <label class="flex v-center" for="nome">Operação</label>
                <label class="flex v-center" for="valor">Valor (R$)</label>
            </span>
            <span class="input flex vertical">
                <input type="text" name="nome" id="nome" placeholder="Nome da operação" value="<?= htmlspecialchars($old['nome'] ?? $operacao['nome']) ?>" required>
                <input type="text" name="valor" id="valor" maxlength="22" value="<?= htmlspecialchars($old['valor'] ?? number_format($operacao['valor'], 2, ',', '')) ?>" required>
            </span>
        </span>
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/operacoes" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Atualizar Operação">
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setupMasks();
    }); 
</script>
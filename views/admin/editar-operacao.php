<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <form class="formulario-cadastro auth-form form-responsive" method="POST" action="<?= BASE_URL ?>admin/atualizar-operacao">
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
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="nome">Operação</label>
                <input type="text" name="nome" id="nome" class="form-input" placeholder="Nome da operação" value="<?= htmlspecialchars($old['nome'] ?? $operacao['nome']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="valor">Valor (R$)</label>
                <input type="text" name="valor" id="valor" class="form-input" maxlength="22" value="<?= htmlspecialchars($old['valor'] ?? number_format($operacao['valor'], 2, ',', '')) ?>" required>
            </div>
        </div>
        
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
<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <form class="formulario-cadastro auth-form form-responsive" method="POST" action="<?= BASE_URL ?>admin/criar-operacao">
        <div class="titulo">Cadastro de operação</div>
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <hr class="shadow">
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="nome">Operação</label>
                <input type="text" name="nome" id="nome" class="form-input" placeholder="Nome da operação" value="<?= htmlspecialchars($old['nome'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="valor">Valor (R$)</label>
                <input type="text" name="valor" id="valor" class="form-input" maxlength="22" value="<?= htmlspecialchars($old['valor'] ?? '') ?>">
            </div>
        </div>
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/operacoes" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Salvar">
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setupMasks();
    }); 
</script>
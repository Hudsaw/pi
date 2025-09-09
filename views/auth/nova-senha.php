<div class="auth-container">
    <h1 class="auth-title">Definir Nova Senha</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>resetar-senha/confirmar">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        
        <div class="form-group">
            <label for="password">Nova Senha</label>
            <input type="password" id="password" name="password" required minlength="8">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirmar Nova Senha</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Atualizar Senha
        </button>
    </form>
</div>
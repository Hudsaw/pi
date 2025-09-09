<div class="auth-container">
    <h1 class="auth-title">Resetar Senha</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>resetar-senha">
        <div class="form-group">
            <label for="email">E-mail cadastrado</label>
            <input type="email" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-key"></i> Enviar Link de Reset
        </button>
    </form>
    
    <div class="auth-links">
        <a href="<?php echo BASE_URL; ?>login">Voltar para o login</a>
    </div>
</div>
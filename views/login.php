<div class="container">
    <div class="card" id="login" style="max-width: 500px;">
        <h1 class="text-center">Acesse sua conta</h1>
        
        <?php if (!empty($erro)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="<?= BASE_URL ?>/login">
            <div class="form-group" id="email-container">
                <label for="email">E-mail</label>
                <input class="input" type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group" id="senha-container">
                <label for="senha">Senha</label>
                <input class="input" type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit" class="btn btn-primary">
                Entrar
            </button>

        </form>

            <div class="auth-links" id="criar_conta">
                <div>
                    <p>É novo aqui?</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/cadastro" class="link-btn">Criar nova conta</a>
                </div>
            </div>  
        
    </div>
</div>
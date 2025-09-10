<div class="principal azul flex center">
    <a href="<?= BASE_URL ?>" class="voltar">Voltar</a>
    <form class="formulario-login flex vertical redondinho shadow" class="auth-form" method="POST" action="<?= BASE_URL ?>/logar">
        <div class="banner flex h-center">
            <img src="<?php echo ASSETS_URL?>img/banner.png" alt="banner">
        </div>
        <h3 class="bem-vindo">Seja bem-vindo!</h3>
        <span class="inputs flex space-between">
            <span class="label flex vertical">
                <label class="flex v-center" for="cpf" value="<?= $cpf ?? '' ?>">CPF</label>
                <label class="flex v-center" for="senha">Senha</label>
            </span>
            <span class="input flex vertical">
                <input type="text" name="cpf" id="cpf" autocomplete="off" placeholder="CPF">
                <input type="password" name="senha" id="senha" placeholder="Senha">
            </span>
        </span>
        <div class="flex center">
            <button type="submit" class="botao-azul" style="width: 50%;">Entrar</button>
        </div>
        <hr>
        <div class="flex center">
            <a class="botao" href="<?= BASE_URL ?>resetar-senha">
            Esqueceu sua senha?</a>
        </div>
    </form>
</div>
<?php if (!empty($erro)): ?>
    <script>setTimeout(() => alert('<?= htmlspecialchars($erro) ?>'), 10)</script>
<?php endif; ?>
<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a href="<?= BASE_URL ?>admin/painel" class="item">Painel</a>
        <a href="<?= BASE_URL ?>admin/usuarios" class="item">Usuários</a>
        <a href="<?= BASE_URL ?>admin/lotes" class="item">Lotes</a>
        <a href="<?= BASE_URL ?>admin/operacoes" class="item bold">Operações</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <form class="formulario-cadastro" class="auth-form" method="POST" action="<?= BASE_URL ?>admin/criar-operacao">
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
        <span class="inputs flex center">   
            <span class="label flex vertical">
                <label class="flex v-center" for="nome">Operação</label>
                <label class="flex v-center" for="valor">Valor (R$)</label>
            </span>
            <span class="input flex vertical">
                <input type="text" name="nome" id="nome" placeholder="Nome da operação" value="<?= htmlspecialchars($old['nome'] ?? '') ?>">
                <input type="text" name="valor" id="valor" maxlength="22" value="<?= htmlspecialchars($old['valor'] ?? '') ?>">
            </span>
        </span>
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
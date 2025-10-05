<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a class="item bold">Usuários</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <form class="formulario-cadastro" class="auth-form" method="POST" action="<?= BASE_URL ?>admin/cadastrar-usuario">
        <div class="titulo">Cadastro de usuário</div>
        <?php if (!empty($_SESSION['registrar_erros'])): ?>
            <div class="error-messages">
                <?php foreach ($_SESSION['registrar_erros'] as $error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <hr class="shadow">
        <span class="inputs flex center">
            <span class="label flex vertical">
                <label class="flex v-center" for="nome">Nome Completo</label>
                <label class="flex v-center" for="telefone">Telefone</label>
                <label class="flex v-center" for="email">Email</label>
                <label class="flex v-center" for="cpf">CPF</label>
                <label class="flex v-center" for="senha">Senha</label>
                <label class="flex v-center" for="csenha">Confirmar Senha</label>
                <label class="flex v-center" for="cep">CEP</label>
                <label class="flex v-center" for="logradouro">Logradouro</label>
                <label class="flex v-center" for="complemento">Complemento</label>
                <label class="flex v-center" for="cidade">Cidade</label>
                <label class="flex v-center" for="especialidade_id">Especialidade</label>
                <label class="flex v-center" for="tipo_chave_pix">Tipo da chave PIX</label>
                <label class="flex v-center" for="chave_pix">PIX</label>
            </span>
            <span class="input flex vertical">
                <input type="text" name="nome" id="nome" placeholder="Nome Completo" value="<?= $_SESSION['registrar_data']['nome'] ?? '' ?>">
                <input type="text" name="telefone" id="telefone" placeholder="Telefone" value="<?= $_SESSION['registrar_data']['telefone'] ?? '' ?>">
                <input type="text" name="email" id="email" placeholder="Email" value="<?= $_SESSION['registrar_data']['email'] ?? '' ?>">
                <input type="text" name="cpf" id="cpf" placeholder="CPF" maxlength='14' value="<?= $_SESSION['registrar_data']['cpf'] ?? '' ?>" onblur="validarCPFInput(this)">
                <input type="password" name="senha" id="senha" placeholder="Senha" value="">
                <input type="password" name="csenha" id="csenha" placeholder="Confirmar Senha" value="">
                <input type="text" name="cep" id="cep" placeholder="CEP" value="<?= $_SESSION['registrar_data']['cep'] ?? '' ?>">
                <input type="text" name="logradouro" id="logradouro" placeholder="Logradouro" value="<?= $_SESSION['registrar_data']['logradouro'] ?? '' ?>">
                <input type="text" name="complemento" id="complemento" placeholder="Complemento" value="<?= $_SESSION['registrar_data']['complemento'] ?? '' ?>">
                <input type="text" name="cidade" id="cidade" placeholder="Cidade" value="<?= $_SESSION['registrar_data']['cidade'] ?? '' ?>">
                <select name="especialidade_id" id="especialidade_id">
                    <option value="">Selecione a especialidade</option>
                    <?php
                    // Buscar especialidades do banco
                    foreach ($especialidades as $especialidade): ?>
                        <option value="<?= $especialidade['id'] ?>" <?= ($_SESSION['registrar_data']['especialidade_id'] ?? '') == $especialidade['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($especialidade['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="tipo_chave_pix" id="tipo_chave_pix">
                    <option value="" <?= empty($_SESSION['registrar_data']['tipo_chave_pix']) ? 'selected' : '' ?>>Selecione o tipo</option>
                    <option value="cpf" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'cpf' ? 'selected' : '' ?>>CPF</option>
                    <option value="cnpj" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'cnpj' ? 'selected' : '' ?>>CNPJ</option>
                    <option value="email" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                    <option value="telefone" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'telefone' ? 'selected' : '' ?>>Telefone</option>
                    <option value="aleatoria" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'aleatoria' ? 'selected' : '' ?>>Aleatória</option>
                </select>
                <input type="text" name="chave_pix" id="chave_pix" placeholder="Chave PIX" value="<?= $_SESSION['registrar_data']['chave_pix'] ?? '' ?>">
            </span>
        </span>
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/usuarios" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Salvar">
        </div>
    </form>
</div>
<script src="<?= ASSETS_URL ?>js/utils.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setupMasks();
    });
    document.querySelector('.formulario-cadastro').addEventListener('submit', function (e) {
        const cpfInput = document.getElementById('cpf');
        if (cpfInput && !validarCPFInput(cpfInput)) {
            e.preventDefault();
        }
    });
</script>
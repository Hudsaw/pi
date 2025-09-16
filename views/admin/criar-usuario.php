<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a class="item bold">Usuários</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <form class="formulario-cadastro" class="auth-form" method="POST" action="<?= BASE_URL ?>admin/cadastrar-usuario">
        <div class="titulo">Cadastro de usuário</div>
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
                <label class="flex v-center" for="tipo_chave_pix">Tipo da chave PIX</label>
                <label class="flex v-center" for="chave_pix">PIX</label>
            </span>
            <span class="input flex vertical">
                <input type="text" name="nome" id="nome" placeholder="Nome Completo" value="<?= $usuario['nome'] ?>">
                <input type="text" name="telefone" id="telefone" placeholder="Telefone" value="<?= $usuario['telefone'] ?>">
                <input type="text" name="email" id="email" placeholder="Email" value="<?= $usuario['email'] ?>">
                <input type="text" name="cpf" id="cpf" placeholder="CPF" maxlength='14' value="<?= $usuario['cpf'] ?>">
                <input type="password" name="senha" id="senha" placeholder="Senha">
                <input type="password" name="csenha" id="csenha" placeholder="Confirmar Senha">
                <input type="text" name="cep" id="cep" placeholder="CEP" value="<?= $usuario['cep'] ?>">
                <input type="text" name="logradouro" id="logradouro" placeholder="Logradouro" value="<?= $usuario['logradouro'] ?>">
                <input type="text" name="complemento" id="complemento" placeholder="Complemento" value="<?= $usuario['complemento'] ?>">
                <input type="text" name="cidade" id="cidade" placeholder="Cidade" value="<?= $usuario['cidade'] ?>">
                <select name="tipo_chave_pix" id="tipo_chave_pix">
                    <option <?= $usuario['tipo_chave_pix'] === 'cpf' ? 'selected' : '' ?> value="cpf">CPF</option>
                    <option <?= $usuario['tipo_chave_pix'] === 'cnpj' ? 'selected' : '' ?> value="cnpj">CNPJ</option>
                    <option <?= $usuario['tipo_chave_pix'] === 'email' ? 'selected' : '' ?> value="email">Email</option>
                    <option <?= $usuario['tipo_chave_pix'] === 'telefone' ? 'selected' : '' ?> value="telefone">Telefone</option>
                    <option <?= $usuario['tipo_chave_pix'] === 'aleatoria' ? 'selected' : '' ?> value="aleatoria">Aleatória</option>
                </select>
                <input type="text" name="chave_pix" id="chave_pix" placeholder="Chave PIX" value="<?= $usuario['chave_pix'] ?>">
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
document.addEventListener('DOMContentLoaded', function() {
    setupMasks();
});
</script>
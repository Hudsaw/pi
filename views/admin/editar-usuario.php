<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <form class="formulario-cadastro auth-form form-responsive" method="POST" action="<?= BASE_URL ?>admin/salvar-usuario">
        <div class="titulo">Edição de usuário</div>
        <hr class="shadow">
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="nome">Nome Completo</label>
                <input type="text" name="nome" id="nome" class="form-input" placeholder="Nome Completo" value="<?= $usuario['nome'] ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="telefone">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-input" placeholder="Telefone" value="<?= $usuario['telefone'] ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="text" name="email" id="email" class="form-input" placeholder="Email" value="<?= $usuario['email'] ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cpf">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-input" placeholder="CPF" maxlength='14' value="<?= $usuario['cpf'] ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="cep">CEP</label>
                <input type="text" name="cep" id="cep" class="form-input" placeholder="CEP" value="<?= $usuario['cep'] ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="logradouro">Logradouro</label>
                <input type="text" name="logradouro" id="logradouro" class="form-input" placeholder="Logradouro" value="<?= $usuario['logradouro'] ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="complemento">Complemento</label>
                <input type="text" name="complemento" id="complemento" class="form-input" placeholder="Complemento" value="<?= $usuario['complemento'] ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cidade">Cidade</label>
                <input type="text" name="cidade" id="cidade" class="form-input" placeholder="Cidade" value="<?= $usuario['cidade'] ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="tipo_chave_pix">Tipo da chave PIX</label>
                <select name="tipo_chave_pix" id="tipo_chave_pix" class="form-select">
                    <option <?= $usuario['tipo_chave_pix'] === 'cpf' ? 'selected' : '' ?> value="cpf">CPF</option>
                    <option <?= $usuario['tipo_chave_pix'] === 'cnpj' ? 'selected' : '' ?> value="cnpj">CNPJ</option>
                    <option <?= $usuario['tipo_chave_pix'] === 'email' ? 'selected' : '' ?> value="email">Email</option>
                    <option <?= $usuario['tipo_chave_pix'] === 'telefone' ? 'selected' : '' ?> value="telefone">Telefone</option>
                    <option <?= $usuario['tipo_chave_pix'] === 'aleatoria' ? 'selected' : '' ?> value="aleatoria">Aleatória</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="chave_pix">Chave PIX</label>
                <input type="text" name="chave_pix" id="chave_pix" class="form-input" placeholder="Chave PIX" value="<?= $usuario['chave_pix'] ?>">
            </div>
        </div>
        
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/visualizar-usuario?id=<?= $usuario['id'] ?>" class="botao">Voltar</a>
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
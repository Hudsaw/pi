<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <form class="formulario-cadastro auth-form form-responsive" method="POST" action="<?= BASE_URL ?>costura/atualizar-perfil">
        <div class="titulo">Editar Meu Perfil</div>
        
        <!-- Exibir mensagens de erro -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Exibir mensagem de sucesso -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <hr class="shadow">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="nome">Nome Completo</label>
                <input type="text" name="nome" id="nome" class="form-input" placeholder="Nome Completo" value="<?= htmlspecialchars($old['nome'] ?? $usuario['nome']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="telefone">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-input" placeholder="Telefone" value="<?= htmlspecialchars($old['telefone'] ?? $usuario['telefone']) ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="text" name="email" id="email" class="form-input" placeholder="Email" value="<?= htmlspecialchars($old['email'] ?? $usuario['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cpf">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-input" placeholder="CPF (não pode ser alterado)" maxlength='14' value="<?= htmlspecialchars($usuario['cpf']) ?>" readonly onblur="validarCPFInput(this)">
            </div>
        </div>
                
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="cep">CEP</label>
                <input type="text" name="cep" id="cep" class="form-input" placeholder="CEP" value="<?= htmlspecialchars($old['cep'] ?? $usuario['cep']) ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="logradouro">Logradouro</label>
                <input type="text" name="logradouro" id="logradouro" class="form-input" placeholder="Logradouro" value="<?= htmlspecialchars($old['logradouro'] ?? $usuario['logradouro']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="complemento">Complemento</label>
                <input type="text" name="complemento" id="complemento" class="form-input" placeholder="Complemento" value="<?= htmlspecialchars($old['complemento'] ?? $usuario['complemento']) ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cidade">Cidade</label>
                <input type="text" name="cidade" id="cidade" class="form-input" placeholder="Cidade" value="<?= htmlspecialchars($old['cidade'] ?? $usuario['cidade']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="tipo_chave_pix">Tipo da chave PIX</label>
                <select name="tipo_chave_pix" id="tipo_chave_pix" class="form-select">
                    <option value="cpf" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'cpf' ? 'selected' : '' ?>>CPF</option>
                    <option value="cnpj" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'cnpj' ? 'selected' : '' ?>>CNPJ</option>
                    <option value="email" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'email' ? 'selected' : '' ?>>Email</option>
                    <option value="telefone" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'telefone' ? 'selected' : '' ?>>Telefone</option>
                    <option value="aleatoria" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'aleatoria' ? 'selected' : '' ?>>Aleatória</option>
                </select>
            </div>
        
            <div class="form-group">
                <label class="form-label" for="chave_pix">Chave PIX</label>
                <input type="text" name="chave_pix" id="chave_pix" class="form-input" placeholder="Chave PIX" value="<?= htmlspecialchars($old['chave_pix'] ?? $usuario['chave_pix']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="senha">Nova Senha (deixe em branco para manter a atual)</label>
                <input type="password" name="senha" id="senha" class="form-input" placeholder="Senha" value="">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="csenha">Confirmar nova Senha</label>
                <input type="password" name="csenha" id="csenha" class="form-input" placeholder="Confirmar nova Senha" value="">
            </div>
        </div>
            
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>costura/visualizar-perfil" class="botao">Cancelar</a>
            <button type="submit" class="botao-azul">Salvar Alterações</button>
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
        </div>
    </form>
</div>

<script src="<?= ASSETS_URL ?>js/utils.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setupMasks();
    
    // Limpar mensagens de erro após 5 segundos
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
});
</script>
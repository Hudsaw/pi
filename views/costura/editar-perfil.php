<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="dashboard-costureira">
        <h2>Editar Meu Perfil</h2>
        
        <!-- Exibir mensagens de erro -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Exibir mensagem de sucesso -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form class="formulario-perfil" method="POST" action="<?= BASE_URL ?>costura/atualizar-perfil">
            <div class="campos-perfil">
                <div class="campo-grupo">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" name="nome" id="nome" placeholder="Nome Completo" 
                           value="<?= htmlspecialchars($old['nome'] ?? $usuario['nome']) ?>" required>
                    <?php if (isset($errors['nome'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['nome']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="campo-grupo">
                    <label for="telefone">Telefone *</label>
                    <input type="text" name="telefone" id="telefone" placeholder="Telefone" 
                           value="<?= htmlspecialchars($old['telefone'] ?? $usuario['telefone']) ?>" required>
                    <?php if (isset($errors['telefone'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['telefone']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="campo-grupo">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" placeholder="Email" 
                           value="<?= htmlspecialchars($old['email'] ?? $usuario['email']) ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['email']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="campo-grupo">
                    <label for="cpf">CPF</label>
                    <input type="text" name="cpf" id="cpf" placeholder="CPF" maxlength='14' 
                           value="<?= htmlspecialchars($usuario['cpf']) ?>" readonly>
                    <small class="texto-ajuda">CPF não pode ser alterado</small>
                </div>
                
                <div class="campo-grupo">
                    <label for="cep">CEP</label>
                    <input type="text" name="cep" id="cep" placeholder="CEP" 
                           value="<?= htmlspecialchars($old['cep'] ?? $usuario['cep']) ?>">
                    <?php if (isset($errors['cep'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['cep']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="campo-grupo">
                    <label for="logradouro">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" placeholder="Logradouro" 
                           value="<?= htmlspecialchars($old['logradouro'] ?? $usuario['logradouro']) ?>">
                </div>
                
                <div class="campo-grupo">
                    <label for="complemento">Complemento</label>
                    <input type="text" name="complemento" id="complemento" placeholder="Complemento" 
                           value="<?= htmlspecialchars($old['complemento'] ?? $usuario['complemento']) ?>">
                </div>
                
                <div class="campo-grupo">
                    <label for="cidade">Cidade</label>
                    <input type="text" name="cidade" id="cidade" placeholder="Cidade" 
                           value="<?= htmlspecialchars($old['cidade'] ?? $usuario['cidade']) ?>">
                </div>
                
                <div class="campo-grupo">
                    <label for="tipo_chave_pix">Tipo da chave PIX</label>
                    <select name="tipo_chave_pix" id="tipo_chave_pix">
                        <option value="cpf" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'cpf' ? 'selected' : '' ?>>CPF</option>
                        <option value="cnpj" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'cnpj' ? 'selected' : '' ?>>CNPJ</option>
                        <option value="email" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'email' ? 'selected' : '' ?>>Email</option>
                        <option value="telefone" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'telefone' ? 'selected' : '' ?>>Telefone</option>
                        <option value="aleatoria" <?= ($old['tipo_chave_pix'] ?? $usuario['tipo_chave_pix']) === 'aleatoria' ? 'selected' : '' ?>>Aleatória</option>
                    </select>
                </div>
                
                <div class="campo-grupo">
                    <label for="chave_pix">Chave PIX</label>
                    <input type="text" name="chave_pix" id="chave_pix" placeholder="Chave PIX" 
                           value="<?= htmlspecialchars($old['chave_pix'] ?? $usuario['chave_pix']) ?>">
                </div>
                
                <div class="campo-grupo">
                    <label for="senha">Nova Senha (deixe em branco para manter a atual)</label>
                    <input type="password" name="senha" id="senha" placeholder="Nova Senha">
                    <?php if (isset($errors['senha'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['senha']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="campo-grupo">
                    <label for="csenha">Confirmar Nova Senha</label>
                    <input type="password" name="csenha" id="csenha" placeholder="Confirmar Nova Senha">
                    <?php if (isset($errors['csenha'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['csenha']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="acoes-formulario flex h-center l-gap">
                <a href="<?= BASE_URL ?>costura/visualizar-perfil" class="botao-cinza">Cancelar</a>
                <button type="submit" class="botao-azul">Salvar Alterações</button>
                <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
            </div>
        </form>
    </div>
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
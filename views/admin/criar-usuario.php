<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <form class="formulario-cadastro auth-form form-responsive" method="POST" action="<?= BASE_URL ?>admin/cadastrar-usuario">
        <div class="titulo">Cadastro de usuário</div>
        
        <?php if (!empty($_SESSION['registrar_erros'])): ?>
            <div class="error-messages">
                <?php foreach ($_SESSION['registrar_erros'] as $error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <hr class="shadow">
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="nome">Nome Completo</label>
                <input type="text" name="nome" id="nome" class="form-input" placeholder="Nome Completo" value="<?= $_SESSION['registrar_data']['nome'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="telefone">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-input" placeholder="Telefone" value="<?= $_SESSION['registrar_data']['telefone'] ?? '' ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="text" name="email" id="email" class="form-input" placeholder="Email" value="<?= $_SESSION['registrar_data']['email'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cpf">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-input" placeholder="CPF" maxlength='14' value="<?= $_SESSION['registrar_data']['cpf'] ?? '' ?>" onblur="validarCPFInput(this)">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="senha">Senha</label>
                <input type="password" name="senha" id="senha" class="form-input" placeholder="Senha" value="">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="csenha">Confirmar Senha</label>
                <input type="password" name="csenha" id="csenha" class="form-input" placeholder="Confirmar Senha" value="">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="cep">CEP</label>
                <input type="text" name="cep" id="cep" class="form-input" placeholder="CEP" value="<?= $_SESSION['registrar_data']['cep'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="logradouro">Logradouro</label>
                <input type="text" name="logradouro" id="logradouro" class="form-input" placeholder="Logradouro" value="<?= $_SESSION['registrar_data']['logradouro'] ?? '' ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="complemento">Complemento</label>
                <input type="text" name="complemento" id="complemento" class="form-input" placeholder="Complemento" value="<?= $_SESSION['registrar_data']['complemento'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cidade">Cidade</label>
                <input type="text" name="cidade" id="cidade" class="form-input" placeholder="Cidade" value="<?= $_SESSION['registrar_data']['cidade'] ?? '' ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="especialidade_id">Especialidade</label>
                <select name="especialidade_id" id="especialidade_id" class="form-select">
                    <option value="">Selecione a especialidade</option>
                    <?php foreach ($especialidades as $especialidade): ?>
                        <option value="<?= $especialidade['id'] ?>" <?= ($_SESSION['registrar_data']['especialidade_id'] ?? '') == $especialidade['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($especialidade['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="tipo_chave_pix">Tipo da chave PIX</label>
                <select name="tipo_chave_pix" id="tipo_chave_pix" class="form-select">
                    <option value="" <?= empty($_SESSION['registrar_data']['tipo_chave_pix']) ? 'selected' : '' ?>>Selecione o tipo</option>
                    <option value="cpf" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'cpf' ? 'selected' : '' ?>>CPF</option>
                    <option value="cnpj" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'cnpj' ? 'selected' : '' ?>>CNPJ</option>
                    <option value="email" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                    <option value="telefone" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'telefone' ? 'selected' : '' ?>>Telefone</option>
                    <option value="aleatoria" <?= ($_SESSION['registrar_data']['tipo_chave_pix'] ?? '') === 'aleatoria' ? 'selected' : '' ?>>Aleatória</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group-full">
                <label class="form-label" for="chave_pix">Chave PIX</label>
                <input type="text" name="chave_pix" id="chave_pix" class="form-input" placeholder="Chave PIX" value="<?= $_SESSION['registrar_data']['chave_pix'] ?? '' ?>">
            </div>
        </div>
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/usuarios" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Salvar">
        </div>
    </form>
</div>
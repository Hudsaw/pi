<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <form class="formulario-cadastro auth-form form-responsive" method="POST" action="<?= BASE_URL ?>admin/criar-empresa">
        <div class="titulo">Cadastro de Empresa</div>
        
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <hr class="shadow">
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="nome">Nome da Empresa</label>
                <input type="text" name="nome" id="nome" class="form-input" placeholder="Nome da empresa" value="<?= htmlspecialchars($old['nome'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cnpj">CNPJ</label>
                <input type="text" name="cnpj" id="cnpj" class="form-input" placeholder="00.000.000/0000-00" value="<?= htmlspecialchars($old['cnpj'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" name="email" id="email" class="form-input" placeholder="email@empresa.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="telefone">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-input" placeholder="(00) 00000-0000" value="<?= htmlspecialchars($old['telefone'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="cep">CEP</label>
                <input type="text" name="cep" id="cep" class="form-input" placeholder="00000-000" value="<?= htmlspecialchars($old['cep'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cidade">Cidade</label>
                <input type="text" name="cidade" id="cidade" class="form-input" placeholder="Cidade" value="<?= htmlspecialchars($old['cidade'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="estado">Estado</label>
                <input type="text" name="estado" id="estado" class="form-input" placeholder="UF" maxlength="2" value="<?= htmlspecialchars($old['estado'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="endereco">Endereço</label>
                <input type="text" name="endereco" id="endereco" class="form-input" placeholder="Endereço completo" value="<?= htmlspecialchars($old['endereco'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group-full">
                <label class="form-label" for="observacao">Observação</label>
                <textarea name="observacao" id="observacao" class="form-textarea" placeholder="Observações"><?= htmlspecialchars($old['observacao'] ?? '') ?></textarea>
            </div>
        </div>
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/empresas" class="botao">Cancelar</a>
            <input type="submit" class="botao" value="Salvar Empresa">
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CNPJ
    const cnpjInput = document.getElementById('cnpj');
    if (cnpjInput) {
        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 14) {
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    }

    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    }

    // Máscara para CEP
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 8) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    }
});
</script>
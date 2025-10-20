<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar-admin.php'; ?>
    
    <form class="formulario-cadastro auth-form" method="POST" action="<?= BASE_URL ?>admin/atualizar-empresa">
        <div class="titulo">Editar Empresa: <?= htmlspecialchars($empresa['nome']) ?></div>
        
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <input type="hidden" name="id" value="<?= htmlspecialchars($empresa['id']) ?>">
        
        <hr class="shadow">
        <span class="inputs flex center">
            <span class="label flex vertical">
                <label class="flex v-center" for="nome">Nome da Empresa</label>
                <label class="flex v-center" for="cnpj">CNPJ</label>
                <label class="flex v-center" for="email">Email</label>
                <label class="flex v-center" for="telefone">Telefone</label>
                <label class="flex v-center" for="cep">CEP</label>
                <label class="flex v-center" for="endereco">Endereço</label>
                <label class="flex v-center" for="cidade">Cidade</label>
                <label class="flex v-center" for="estado">Estado</label>
                <label class="flex v-center" for="observacao">Observação</label>
            </span>
            <span class="input flex vertical">
                <input type="text" name="nome" id="nome" placeholder="Nome da empresa" 
                       value="<?= htmlspecialchars($old['nome'] ?? $empresa['nome']) ?>" required>
                <input type="text" name="cnpj" id="cnpj" placeholder="00.000.000/0000-00" 
                       value="<?= htmlspecialchars($old['cnpj'] ?? $this->formatarCNPJ($empresa['cnpj'])) ?>" required>
                <input type="email" name="email" id="email" placeholder="email@empresa.com" 
                       value="<?= htmlspecialchars($old['email'] ?? $empresa['email']) ?>">
                <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000" 
                       value="<?= htmlspecialchars($old['telefone'] ?? $this->formatarTelefone($empresa['telefone'])) ?>" required>
                <input type="text" name="cep" id="cep" placeholder="00000-000" 
                       value="<?= htmlspecialchars($old['cep'] ?? $this->formatarCEP($empresa['cep'])) ?>">
                <input type="text" name="endereco" id="endereco" placeholder="Endereço completo" 
                       value="<?= htmlspecialchars($old['endereco'] ?? $empresa['endereco']) ?>">
                <input type="text" name="cidade" id="cidade" placeholder="Cidade" 
                       value="<?= htmlspecialchars($old['cidade'] ?? $empresa['cidade']) ?>">
                <input type="text" name="estado" id="estado" placeholder="UF" maxlength="2" 
                       value="<?= htmlspecialchars($old['estado'] ?? $empresa['estado']) ?>">
                <textarea name="observacao" id="observacao" placeholder="Observações"><?= htmlspecialchars($old['observacao'] ?? $empresa['observacao']) ?></textarea>
            </span>
        </span>
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/visualizar-empresa?id=<?= $empresa['id'] ?>" class="botao">Cancelar</a>
            <input type="submit" class="botao" value="Atualizar Empresa">
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mesmas máscaras do criar-empresa
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
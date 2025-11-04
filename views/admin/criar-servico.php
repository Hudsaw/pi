<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <form class="formulario-cadastro auth-form form-responsive" method="POST" action="<?= BASE_URL ?>admin/criar-servico">
        <div class="titulo">Cadastro de Serviço</div>
        
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $field => $errorMessages): ?>
                        <?php if (is_array($errorMessages)): ?>
                            <?php foreach ($errorMessages as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><?= htmlspecialchars($errorMessages) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <hr class="shadow">
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="lote_id">Lote</label>
                <select name="lote_id" id="lote_id" class="form-select" required>
                    <option value="">Selecione um lote</option>
                    <?php foreach ($lotes as $lote): ?>
                        <option value="<?= $lote['id'] ?>" <?= (isset($old['lote_id']) && $old['lote_id'] == $lote['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lote['nome']) ?> - <?= htmlspecialchars($lote['colecao']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="operacao_id">Operação</label>
                <select name="operacao_id" id="operacao_id" class="form-select" required>
                    <option value="">Selecione uma operação</option>
                    <?php foreach ($operacoes as $operacao): ?>
                        <option value="<?= $operacao['id'] ?>" data-valor="<?= $operacao['valor'] ?>" <?= (isset($old['operacao_id']) && $old['operacao_id'] == $operacao['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($operacao['nome']) ?> - R$ <?= number_format($operacao['valor'], 2, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="quantidade_pecas">Quantidade de Peças</label>
                <input type="number" name="quantidade_pecas" id="quantidade_pecas" class="form-input" placeholder="Quantidade de peças" min="1" value="<?= htmlspecialchars($old['quantidade_pecas'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="valor_operacao">Valor da Operação (R$)</label>
                <input type="text" name="valor_operacao" id="valor_operacao" class="form-input" placeholder="0,00" value="<?= htmlspecialchars($old['valor_operacao'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="data_envio">Data de Envio</label>
                <input type="date" name="data_envio" id="data_envio" class="form-input" value="<?= htmlspecialchars($old['data_envio'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group-full">
                <label class="form-label" for="observacao">Observações</label>
                <textarea name="observacao" id="observacao" class="form-textarea" placeholder="Observações"><?= htmlspecialchars($old['observacao'] ?? '') ?></textarea>
            </div>
        </div>
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/servicos" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Criar Serviço">
        </div>
    </form>
</div>

<script>
// Preencher automaticamente o valor da operação quando selecionada
document.getElementById('operacao_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const valor = selectedOption.getAttribute('data-valor');
    if (valor) {
        document.getElementById('valor_operacao').value = valor;
    }
});
// Formatar valor monetário
document.getElementById('valor_operacao').addEventListener('blur', function() {
    let value = this.value.replace(/\D/g, '');
    value = (value / 100).toFixed(2);
    this.value = value.replace('.', ',');
});
</script>
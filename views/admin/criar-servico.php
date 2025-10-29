<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <form class="formulario-cadastro auth-form" method="POST" action="<?= BASE_URL ?>admin/criar-servico">
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
        <span class="flex vertical s-gap">
            <span class="flex space-between">
                <label class="flex v-center" for="lote_id">Lote</label>
                <select name="lote_id" id="lote_id" required>
                    <option value="">Selecione um lote</option>
                    <?php foreach ($lotes as $lote): ?>
                        <option value="<?= $lote['id'] ?>" <?= (isset($old['lote_id']) && $old['lote_id'] == $lote['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lote['nome']) ?> - <?= htmlspecialchars($lote['colecao']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label class="flex v-center" for="operacao_id">Operação</label>
                <select name="operacao_id" id="operacao_id" required>
                    <option value="">Selecione uma operação</option>
                    <?php foreach ($operacoes as $operacao): ?>
                        <option value="<?= $operacao['id'] ?>" data-valor="<?= $operacao['valor'] ?>" <?= (isset($old['operacao_id']) && $old['operacao_id'] == $operacao['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($operacao['nome']) ?> - R$ <?= number_format($operacao['valor'], 2, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label class="flex v-center" for="quantidade_pecas">Quantidade de Peças</label>
                <input type="number" name="quantidade_pecas" id="quantidade_pecas" placeholder="Quantidade de peças" min="1" value="<?= htmlspecialchars($old['quantidade_pecas'] ?? '') ?>" required>
                <label class="flex v-center" for="data_envio">Data de Envio</label>
                <input type="date" name="data_envio" id="data_envio" value="<?= htmlspecialchars($old['data_envio'] ?? '') ?>" required>
            </span>
            <span class="flex">
                <textarea name="observacao" id="observacao" placeholder="Observações"><?= htmlspecialchars($old['observacao'] ?? '') ?></textarea>
            </span>
        </span>
        
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
</script>
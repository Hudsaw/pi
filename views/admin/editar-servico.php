<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar-admin.php'; ?>
    
    <form class="formulario-cadastro auth-form" method="POST" action="<?= BASE_URL ?>admin/atualizar-servico">
        <input type="hidden" name="id" value="<?= $servico['id'] ?>">
        <div class="titulo">Editar Serviço #<?= $servico['id'] ?></div>
        
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
        <span class="inputs flex center">
            <span class="label flex vertical">
                <label class="flex v-center" for="lote_id">Lote</label>
                <label class="flex v-center" for="operacao_id">Operação</label>
                <label class="flex v-center" for="quantidade_pecas">Quantidade de Peças</label>
                <label class="flex v-center" for="valor_operacao">Valor da Operação (R$)</label>
                <label class="flex v-center" for="data_envio">Data de Envio</label>
                <label class="flex v-center" for="observacao">Observação</label>
            </span>
            <span class="input flex vertical">
                <select name="lote_id" id="lote_id" required>
                    <option value="">Selecione um lote</option>
                    <?php foreach ($lotes as $lote): ?>
                        <option value="<?= $lote['id'] ?>" <?= ($servico['lote_id'] == $lote['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lote['nome']) ?> - <?= htmlspecialchars($lote['colecao']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="operacao_id" id="operacao_id" required>
                    <option value="">Selecione uma operação</option>
                    <?php foreach ($operacoes as $operacao): ?>
                        <option value="<?= $operacao['id'] ?>" data-valor="<?= $operacao['valor'] ?>" <?= ($servico['operacao_id'] == $operacao['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($operacao['nome']) ?> - R$ <?= number_format($operacao['valor'], 2, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="number" name="quantidade_pecas" id="quantidade_pecas" placeholder="Quantidade de peças" min="1" value="<?= htmlspecialchars($servico['quantidade_pecas']) ?>" required>
                <input type="number" name="valor_operacao" id="valor_operacao" step="0.01" min="0" placeholder="Valor da operação" value="<?= htmlspecialchars($servico['valor_operacao']) ?>" required>
                <input type="date" name="data_envio" id="data_envio" value="<?= htmlspecialchars($servico['data_envio']) ?>" required>
                <textarea name="observacao" id="observacao" placeholder="Observações"><?= htmlspecialchars($servico['observacao'] ?? '') ?></textarea>
            </span>
        </span>
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/visualizar-servico?id=<?= $servico['id'] ?>" class="botao">Cancelar</a>
            <input type="submit" class="botao" value="Atualizar Serviço">
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
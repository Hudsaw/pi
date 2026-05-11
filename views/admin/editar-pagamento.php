<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <form class="formulario-cadastro auth-form form-responsive" method="POST" 
          action="<?= BASE_URL ?>admin/atualizar-pagamento" enctype="multipart/form-data">
        <div class="titulo">Editar Pagamento</div>
        
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
                <label class="form-label">Costureira</label>
                <input type="text" class="form-input" readonly 
                       value="<?= htmlspecialchars($pagamento['costureira_nome']) ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Período de Referência</label>
                <input type="text" class="form-input" readonly 
                       value="<?= date('m/Y', strtotime($pagamento['periodo_referencia'])) ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Valor Bruto</label>
                <input type="text" class="form-input" readonly 
                       value="R$ <?= number_format($pagamento['valor_bruto'], 2, ',', '.') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="valor_desconto">Desconto (R$) *</label>
                <input type="text" name="valor_desconto" id="valor_desconto" 
                       class="form-input"
                       value="<?= number_format($pagamento['valor_desconto'], 2, ',', '.') ?>" required>
                <?php if (isset($errors['valor_desconto'])): ?>
                    <small class="erro-texto"><?= $errors['valor_desconto'] ?></small>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Valor Líquido</label>
                <input type="text" id="valor_liquido" class="form-input" readonly 
                       value="R$ <?= number_format($pagamento['valor_liquido'], 2, ',', '.') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="status">Status *</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="Pendente" <?= $pagamento['status'] === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="Pago" <?= $pagamento['status'] === 'Pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="Cancelado" <?= $pagamento['status'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
        </div>
        
        <div class="form-row" id="data-pagamento-group" style="display: <?= $pagamento['status'] === 'Pago' ? 'flex' : 'none' ?>">
            <div class="form-group">
                <label class="form-label" for="data_pagamento">Data do Pagamento</label>
                <input type="date" name="data_pagamento" id="data_pagamento" 
                       class="form-input"
                       value="<?= $pagamento['data_pagamento'] ?? date('Y-m-d') ?>">
                <?php if (isset($errors['data_pagamento'])): ?>
                    <small class="erro-texto"><?= $errors['data_pagamento'] ?></small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="comprovante">Comprovante</label>
                <?php if ($pagamento['comprovante']): ?>
                    <div class="flex v-center s-gap">
                        <span>Arquivo atual:</span>
                        <a href="<?= UPLOADS_URL ?>comprovantes/<?= $pagamento['comprovante'] ?>" 
                           target="_blank" class="btn-visualizar">Visualizar</a>
                    </div>
                <?php endif; ?>
                <input type="file" name="comprovante" id="comprovante" 
                       class="form-input" accept=".pdf,.jpg,.jpeg,.png">
                <small class="info-texto">Formatos permitidos: PDF, JPG, PNG (máx. 5MB)</small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group-full">
                <label class="form-label" for="motivo_desconto">Motivo do Desconto</label>
                <textarea name="motivo_desconto" id="motivo_desconto" 
                          class="form-textarea" 
                          placeholder="Informe o motivo do desconto"><?= htmlspecialchars($pagamento['motivo_desconto'] ?? '') ?></textarea>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group-full">
                <label class="form-label" for="observacao">Observações</label>
                <textarea name="observacao" id="observacao" 
                          class="form-textarea" 
                          placeholder="Observações adicionais"><?= htmlspecialchars($pagamento['observacao'] ?? '') ?></textarea>
            </div>
        </div>
        
        <input type="hidden" name="id" value="<?= $pagamento['id'] ?>">
        
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/visualizar-pagamento?id=<?= $pagamento['id'] ?>" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Salvar Alterações">
        </div>
    </form>
</div>

<script>
// Calcular valor líquido
function calcularValorLiquido() {
    const valorBruto = <?= $pagamento['valor_bruto'] ?>;
    let desconto = document.getElementById('valor_desconto').value;
    desconto = desconto.replace(/\./g, '').replace(',', '.');
    desconto = parseFloat(desconto) || 0;
    
    const valorLiquido = valorBruto - desconto;
    document.getElementById('valor_liquido').value = 'R$ ' + valorLiquido.toFixed(2).replace('.', ',');
}

document.getElementById('valor_desconto').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value) {
        value = (parseInt(value) / 100).toFixed(2);
        value = value.replace('.', ',');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        e.target.value = value;
        calcularValorLiquido();
    } else {
        calcularValorLiquido();
    }
});

document.getElementById('status').addEventListener('change', function() {
    const dataPagamentoGroup = document.getElementById('data-pagamento-group');
    if (this.value === 'Pago') {
        dataPagamentoGroup.style.display = 'flex';
        document.getElementById('data_pagamento').required = true;
    } else {
        dataPagamentoGroup.style.display = 'none';
        document.getElementById('data_pagamento').required = false;
    }
});
</script>
<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <div class="conteudo-formulario">
        <h2>Adicionar Peça ao Lote #<?= htmlspecialchars($lote['id']) ?></h2>
        
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>admin/criar-peca">
            <input type="hidden" name="lote_id" value="<?= htmlspecialchars($lote['id']) ?>">
            
            <div class="campo">
                <label for="operacao_id">Operação</label>
                <select id="operacao_id" name="operacao_id" required>
                    <option value="">Selecione uma operação</option>
                    <?php foreach ($operacoes as $operacao): ?>
                        <option value="<?= htmlspecialchars($operacao['id']) ?>" <?= (isset($old['operacao_id']) && $old['operacao_id'] == $operacao['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($operacao['nome']) ?> - R$ <?= number_format($operacao['valor'], 2, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="campos-duplos">
                <div class="campo">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" value="<?= htmlspecialchars($old['quantidade'] ?? '') ?>" required>
                </div>
                
                <div class="campo">
                    <label for="valor_unitario">Valor Unitário (R$)</label>
                    <input type="number" id="valor_unitario" name="valor_unitario" step="0.01" min="0" value="<?= htmlspecialchars($old['valor_unitario'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="campo">
                <label for="observacao">Observação</label>
                <textarea id="observacao" name="observacao"><?= htmlspecialchars($old['observacao'] ?? '') ?></textarea>
            </div>
            
            <div class="botoes">
                <a href="<?= BASE_URL ?>admin/visualizar-lote?id=<?= $lote['id'] ?>" class="botao-cinza">Cancelar</a>
                <button type="submit" class="botao-azul">Adicionar Peça</button>
            </div>
        </form>
    </div>
</div>
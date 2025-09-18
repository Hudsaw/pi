<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a href="<?= BASE_URL ?>admin/painel" class="item">Painel</a>
        <a href="<?= BASE_URL ?>admin/usuarios" class="item">Usuários</a>
        <a href="<?= BASE_URL ?>admin/lotes" class="item bold">Lotes</a>
        <a href="<?= BASE_URL ?>admin/operacoes" class="item">Operações</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <div class="conteudo-formulario">
        <h2>Criar Novo Lote</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>admin/criar-lote">
            <div class="campo">
                <label for="empresa_id">ID da Empresa</label>
                <input type="text" id="empresa_id" name="empresa_id" value="<?= htmlspecialchars($old['empresa_id'] ?? '') ?>" required>
            </div>
            
            <div class="campo">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" required><?= htmlspecialchars($old['descricao'] ?? '') ?></textarea>
            </div>
            
            <div class="campos-duplos">
                <div class="campo">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" value="<?= htmlspecialchars($old['quantidade'] ?? '') ?>" required>
                </div>
                
                <div class="campo">
                    <label for="valor">Valor Total (R$)</label>
                    <input type="number" id="valor" name="valor" step="0.01" min="0" value="<?= htmlspecialchars($old['valor'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="campos-duplos">
                <div class="campo">
                    <label for="data_inicio">Data de Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($old['data_inicio'] ?? '') ?>" required>
                </div>
                
                <div class="campo">
                    <label for="data_prazo">Data de Prazo</label>
                    <input type="date" id="data_prazo" name="data_prazo" value="<?= htmlspecialchars($old['data_prazo'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="botoes">
                <a href="<?= BASE_URL ?>admin/lotes" class="botao-cinza">Cancelar</a>
                <button type="submit" class="botao-azul">Criar Lote</button>
            </div>
        </form>
    </div>
</div>
<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a href="<?= BASE_URL ?>admin/painel" class="item">Painel</a>
        <a href="<?= BASE_URL ?>admin/usuarios" class="item">Usuários</a>
        <a href="<?= BASE_URL ?>admin/lotes" class="item">Lotes</a>
        <a href="<?= BASE_URL ?>admin/operacoes" class="item bold">Operações</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <div class="conteudo-formulario">
        <h2>Criar Nova Operação</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>admin/criar-operacao">
            <div class="campo">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($old['nome'] ?? '') ?>" required>
            </div>
            
            <div class="campo">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" required><?= htmlspecialchars($old['descricao'] ?? '') ?></textarea>
            </div>
            
            <div class="campos-duplos">
                <div class="campo">
                    <label for="valor">Valor (R$)</label>
                    <input type="number" id="valor" name="valor" step="0.01" min="0" value="<?= htmlspecialchars($old['valor'] ?? '') ?>" required>
                </div>
                
                <div class="campo">
                    <label for="tempo_estimado">Tempo Estimado (minutos)</label>
                    <input type="number" id="tempo_estimado" name="tempo_estimado" min="1" value="<?= htmlspecialchars($old['tempo_estimado'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="botoes">
                <a href="<?= BASE_URL ?>admin/operacoes" class="botao-cinza">Cancelar</a>
                <button type="submit" class="botao-azul">Criar Operação</button>
            </div>
        </form>
    </div>
</div>
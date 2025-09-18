<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a href="<?= BASE_URL ?>admin/painel" class="item">Painel</a>
        <a href="<?= BASE_URL ?>admin/usuarios" class="item">Usuários</a>
        <a href="<?= BASE_URL ?>admin/lotes" class="item bold">Lotes</a>
        <a href="<?= BASE_URL ?>admin/operacoes" class="item">Operações</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap">
            <h2>Lote #<?= htmlspecialchars($lote['id']) ?> - <?= htmlspecialchars($lote['descricao']) ?></h2>
            <a href="<?= BASE_URL ?>admin/adicionar-peca?lote_id=<?= $lote['id'] ?>" class="botao-azul">Adicionar Peça</a>
        </div>
        
        <div class="detalhes-lote">
            <div class="info-lote">
                <div class="info-item">
                    <span class="info-label">ID da Empresa:</span>
                    <span class="info-value"><?= htmlspecialchars($lote['empresa_id']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Quantidade:</span>
                    <span class="info-value"><?= htmlspecialchars($lote['quantidade']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Valor Total:</span>
                    <span class="info-value">R$ <?= number_format($lote['valor'], 2, ',', '.') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Início:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($lote['data_inicio'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Prazo:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($lote['data_prazo'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value <?= $lote['ativo'] ? 'ativo' : 'inativo' ?>">
                        <?= $lote['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </span>
                </div>
            </div>
        </div>
        
        <h3>Peças do Lote</h3>
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow" id="tabelaPecas">
                <thead>
                    <tr>
                        <th class="ae">ID</th>
                        <th class="ae">Operação</th>
                        <th class="ae">Quantidade</th>
                        <th class="ae">Valor Unitário</th>
                        <th class="ae">Valor Total</th>
                        <th class="ae">Observação</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pecas as $peca): ?>
                        <tr class="linha-peca">
                            <td class="ae"><?= htmlspecialchars($peca['id']) ?></td>
                            <td class="ae"><?= htmlspecialchars($peca['operacao_nome']) ?></td>
                            <td class="ae"><?= htmlspecialchars($peca['quantidade']) ?></td>
                            <td class="ae">R$ <?= number_format($peca['valor_unitario'], 2, ',', '.') ?></td>
                            <td class="ae">R$ <?= number_format($peca['quantidade'] * $peca['valor_unitario'], 2, ',', '.') ?></td>
                            <td class="ae"><?= htmlspecialchars($peca['observacao'] ?? 'Nenhuma') ?></td>
                            <td class="ac">
                                <a href="<?= BASE_URL ?>admin/remover-peca?id=<?= $peca['id'] ?>&lote_id=<?= $lote['id'] ?>" onclick="return confirm('Tem certeza que deseja remover esta peça?')">
                                    <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
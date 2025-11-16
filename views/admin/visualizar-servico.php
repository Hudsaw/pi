<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="formulario-cadastro">
        <div class="titulo">Serviço #<?= htmlspecialchars($servico['id']) ?></div>
        <hr class="shadow">
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">Lote</span>
                <span class="flex v-center">Operação</span>
                <span class="flex v-center">Quantidade de peças</span>
                <span class="flex v-center">Valor da operação</span>
                <span class="flex v-center">Valor total</span>
                <span class="flex v-center">Data de envio</span>
                <span class="flex v-center">Status</span>
                <?php if ($servico['data_finalizacao']): ?>
                <span class="flex v-center">Data de finalização</span>
                <?php endif; ?>
                <span class="flex v-center">Observação</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['lote_nome']) ?> - <?= htmlspecialchars($servico['colecao']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['operacao_nome']) ?> (R$ <?= number_format($servico['valor_base_operacao'], 2, ',', '.') ?>)</span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['quantidade_pecas']) ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($servico['valor_operacao'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($servico['valor_operacao'] * $servico['quantidade_pecas'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($servico['data_envio'])) ?></span>
                <span class="flex v-center status-<?= strtolower($servico['status']) ?>" style="min-height:20px"><?= htmlspecialchars($servico['status']) ?></span>
                <?php if ($servico['data_finalizacao']): ?>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($servico['data_finalizacao'])) ?></span>
                <?php endif; ?>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['observacao'] ?? 'Nenhuma') ?></span>
            </span>
        </span>
       
        <br>
        <hr>
        <div class="flex h-center l-gap">    

            <!-- Ações -->
            <div class="acoes-servico flex s-gap" style="margin-top: 20px;">
                
                <a href="<?= BASE_URL ?>admin/servicos" class="botao">Voltar</a>
                <?php if ($servico['status'] === 'Em andamento'): ?>
                <!-- Botão Editar -->
                <a href="<?= BASE_URL ?>admin/editar-servico?id=<?= $servico['id'] ?>" class="botao">
                    Editar Serviço
                </a>
                
                <!-- Finalizar serviço -->
                <form method="POST" action="<?= BASE_URL ?>admin/finalizar-servico?id=<?= $servico['id'] ?>">
                    <input type="hidden" name="data_finalizacao" value="<?= date('Y-m-d') ?>">
                    <button type="submit" class="botao-remover" onclick="return confirm('Tem certeza que deseja finalizar este serviço?')">
                        Finalizar Serviço
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
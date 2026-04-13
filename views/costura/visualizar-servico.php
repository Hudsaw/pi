<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="formulario-cadastro"> 
        <div class="page-header flex" style="justify-content: space-between; align-items: center;">
            <h2>Detalhes do Serviço #<?= htmlspecialchars($servico['id']) ?></h2>
            <a href="<?= BASE_URL ?>costura/servicos" class="botao"> 
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        <hr class="shadow">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">Operação</span>
                <span class="flex v-center">Lote / Coleção</span>
                <span class="flex v-center">Quantidade de peças</span>
                <span class="flex v-center">Peças concluídas</span>
                <span class="flex v-center">Valor por peça</span>
                <span class="flex v-center">Valor total</span>
                <span class="flex v-center">Data de envio</span>
                <span class="flex v-center">Prazo de entrega</span>
                <?php if (!empty($servico['data_finalizacao'])): ?>
                <span class="flex v-center">Data de finalização</span>
                <?php endif; ?>
                <span class="flex v-center">Status</span>
                <span class="flex v-center">Observação</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['operacao_nome'] ?? 'N/A') ?></span>
                <span class="flex v-center" style="min-height:20px">
                    <?= htmlspecialchars($servico['lote_nome'] ?? 'N/A') ?> - <?= htmlspecialchars($servico['colecao'] ?? 'N/A') ?>
                </span>
                <span class="flex v-center" style="min-height:20px"><?= number_format($servico['quantidade_pecas'] ?? 0, 0, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px">
                    <?php if (($servico['status'] ?? '') != 'Finalizado'): ?>
                    <form method="POST" action="<?= BASE_URL ?>costura/atualizar-progresso" style="display: flex; gap: 5px;">
                        <input type="hidden" name="servico_id" value="<?= $servico['id'] ?>">
                        <input type="number" 
                               name="pecas_concluidas" 
                               value="<?= $servico['pecas_concluidas'] ?? 0 ?>"
                               min="0" 
                               max="<?= $servico['quantidade_pecas'] ?>"
                               style="width: 100px; padding: 4px; border: 1px solid #ddd; border-radius: 4px;">
                        <button type="submit" class="btn-visualizar" title="Atualizar">
                            <i class="fas fa-save"></i>
                        </button>
                    </form>
                    <?php else: ?>
                        <?= number_format($servico['pecas_concluidas'] ?? 0, 0, ',', '.') ?>
                    <?php endif; ?>
                </span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($servico['valor_operacao'] ?? 0, 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format(($servico['valor_operacao'] ?? 0) * ($servico['quantidade_pecas'] ?? 0), 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= isset($servico['data_envio']) ? date('d/m/Y', strtotime($servico['data_envio'])) : 'N/A' ?></span>
                <span class="flex v-center" style="min-height:20px">
                    <?= isset($servico['data_entrega']) ? date('d/m/Y', strtotime($servico['data_entrega'])) : 'N/A' ?>
                    <?php
                    if (isset($servico['data_entrega']) && ($servico['status'] ?? '') != 'Finalizado') {
                        $diasRestantes = (strtotime($servico['data_entrega']) - time()) / (60 * 60 * 24);
                        if ($diasRestantes <= 3 && $diasRestantes > 0) {
                            echo ' <i class="fas fa-exclamation-triangle" style="color: #f39c12;" title="Prazo próximo!"></i>';
                        }
                    }
                    ?>
                </span>
                <?php if (!empty($servico['data_finalizacao'])): ?>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($servico['data_finalizacao'])) ?></span>
                <?php endif; ?>
                <span class="flex v-center" style="min-height:20px">
                    <?php if (($servico['status'] ?? '') == 'Em andamento'): ?>
                        <span class="status-badge in-progress">Em andamento</span>
                    <?php elseif (($servico['status'] ?? '') == 'Finalizado'): ?>
                        <span class="status-badge completed">Finalizado</span>
                    <?php else: ?>
                        <?= htmlspecialchars($servico['status'] ?? 'N/A') ?>
                    <?php endif; ?>
                </span>
                <span class="flex v-center" style="min-height:20px"><?= nl2br(htmlspecialchars($servico['observacao'] ?? 'Nenhuma observação')) ?></span>
            </span>
        </span>
    </div>
</div>
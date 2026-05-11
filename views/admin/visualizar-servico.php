<div class="conteudo flex">
<?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="formulario-cadastro">
        <div class="titulo">Serviço #<?= htmlspecialchars($servico['id']) ?></div>
        <hr class="shadow">
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">Lote</span>
                <span class="flex v-center">Operação</span>
                <span class="flex v-center">Costureira</span>
                <span class="flex v-center">Peças concluídas</span>
                <span class="flex v-center">Quantidade de peças</span>
                <span class="flex v-center">Valor da operação</span>
                <span class="flex v-center">Valor total</span>
                <span class="flex v-center">Data de envio</span>
                <span class="flex v-center">Status</span>
                <?php if ($servico['data_finalizacao']): ?>
                <span class="flex v-center">Data de finalização</span>
                <?php endif; ?>
                <?php if (isset($servico['quantidade_concluida']) && $servico['quantidade_concluida']): ?>
                <span class="flex v-center">Quantidade concluída</span>
                <?php endif; ?>
                <span class="flex v-center">Observação</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
            <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['lote_nome']) ?> - <?= htmlspecialchars($servico['colecao']) ?></span>
            <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['operacao_nome']) ?> (R$ <?= number_format($servico['valor_base_operacao'], 2, ',', '.') ?>)</span>
            <span class="flex v-center" style="min-height:20px">
        <?php if ($servico['costureira_nome']): ?>
            <?= htmlspecialchars($servico['costureira_nome']) ?> - <?= htmlspecialchars($servico['costureira_especialidade']) ?>
            <?php if ($servico['status'] === 'Em andamento'): ?>
                <a href="<?= BASE_URL ?>admin/desvincular-costureira?servico_id=<?= $servico['id'] ?>" 
                   onclick="return confirm('Tem certeza que deseja desvincular esta costureira?')"
                   class="btn-remover" style="margin-left: 10px;" title="Desvincular Costureira">
                    Desvincular
                </a>
            <?php endif; ?>
        <?php else: ?>
            <span class="texto-vermelho">Nenhuma costureira vinculada</span>
        <?php endif; ?>
    </span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['pecas_concluidas']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['quantidade_pecas']) ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($servico['valor_operacao'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px">R$ <?= number_format($servico['valor_operacao'] * $servico['quantidade_pecas'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($servico['data_envio'])) ?></span>
                <span class="flex v-center status-<?= strtolower($servico['status']) ?>" style="min-height:20px"><?= htmlspecialchars($servico['status']) ?></span>
                <?php if ($servico['data_finalizacao']): ?>
                    <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y', strtotime($servico['data_finalizacao'])) ?></span>
                <?php endif; ?>
                <?php if (isset($servico['quantidade_concluida']) && $servico['quantidade_concluida']): ?>
                    <span class="flex v-center" style="min-height:20px">
                        <?= $servico['quantidade_concluida'] ?> peças
                        <?php if ($servico['quantidade_concluida'] < $servico['quantidade_pecas']): ?>
                            <span class="texto-vermelho">(Perda: <?= $servico['quantidade_pecas'] - $servico['quantidade_concluida'] ?> peças)</span>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($servico['observacao'] ?? 'Nenhuma') ?></span>
            </span>
        </span>
       
        <br>
        <hr>
        <div class="flex h-center l-gap">    
            <div class="acoes-servico flex s-gap" style="margin-top: 20px;">
                <a href="<?= BASE_URL ?>admin/servicos" class="botao">Voltar</a>
                <?php if ($servico['status'] === 'Em andamento'): ?>
                <!-- Botão Editar -->
                <a href="<?= BASE_URL ?>admin/editar-servico?id=<?= $servico['id'] ?>" class="botao">
                    Editar Serviço
                </a>
                
                <!-- Finalizar serviço -->
<form method="POST" action="<?= BASE_URL ?>admin/finalizar-servico">
    <input type="hidden" name="id" value="<?= $servico['id'] ?>">
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

<!-- Modal para finalizar serviço -->
<div id="modalFinalizar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; width: 90%; max-width: 500px;">
        <div style="padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Finalizar Serviço</h3>
            <span onclick="fecharModalFinalizar()" style="font-size: 28px; cursor: pointer;">&times;</span>
        </div>
        <form method="POST" action="<?= BASE_URL ?>admin/finalizar-servico?id=<?= $servico['id'] ?>">
            <div style="padding: 20px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Quantidade Total de Peças:</label>
                    <strong><?= $servico['quantidade_pecas'] ?> peças</strong>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="quantidade_concluida" style="display: block; margin-bottom: 5px; font-weight: bold;">Quantidade de Peças Concluídas *</label>
                    <input type="number" name="quantidade_concluida" id="quantidade_concluida" 
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                           min="0" max="<?= $servico['quantidade_pecas'] ?>" 
                           value="<?= $servico['quantidade_pecas'] ?>" required>
                    <small style="color: #666;">Informe quantas peças foram efetivamente concluídas</small>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="data_finalizacao" style="display: block; margin-bottom: 5px; font-weight: bold;">Data de Finalização</label>
                    <input type="date" name="data_finalizacao" id="data_finalizacao" 
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                           value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="observacao_finalizacao" style="display: block; margin-bottom: 5px; font-weight: bold;">Observações sobre perdas</label>
                    <textarea name="observacao_finalizacao" id="observacao_finalizacao" 
                              style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 80px;"
                              placeholder="Informe se houve perdas e os motivos..."></textarea>
                </div>
                
                <div id="info-perdas" style="display: none; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 10px; margin-top: 10px;">
                    <strong>Informação sobre perdas</strong>
                    <p>Peças perdidas: <strong id="pecas-perdidas">0</strong></p>
                    <p>Valor do serviço será ajustado proporcionalmente às peças concluídas.</p>
                </div>
            </div>
            <div style="padding: 15px 20px; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="fecharModalFinalizar()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="submit" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Confirmar Finalização</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalFinalizar() {
    document.getElementById('modalFinalizar').style.display = 'block';
    calcularPerdas();
}

function fecharModalFinalizar() {
    document.getElementById('modalFinalizar').style.display = 'none';
}

function calcularPerdas() {
    const quantidadeTotal = <?= $servico['quantidade_pecas'] ?>;
    const quantidadeConcluida = parseInt(document.getElementById('quantidade_concluida').value) || 0;
    const pecasPerdidas = quantidadeTotal - quantidadeConcluida;
    
    document.getElementById('pecas-perdidas').textContent = pecasPerdidas;
    
    const infoPerdas = document.getElementById('info-perdas');
    if (pecasPerdidas > 0) {
        infoPerdas.style.display = 'block';
    } else {
        infoPerdas.style.display = 'none';
    }
}

document.getElementById('quantidade_concluida').addEventListener('input', calcularPerdas);
document.getElementById('quantidade_concluida').addEventListener('change', function() {
    let value = parseInt(this.value);
    const max = parseInt(this.max);
    if (value > max) {
        this.value = max;
        calcularPerdas();
    }
    if (value < 0) {
        this.value = 0;
        calcularPerdas();
    }
});
</script>
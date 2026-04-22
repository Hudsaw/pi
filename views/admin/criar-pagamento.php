<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>

    <form class="formulario-cadastro" method="post" action="<?= BASE_URL ?>admin/processar-pagamento" enctype="multipart/form-data">
        <div class="titulo">Registrar Pagamento</div>
        
        <hr class="shadow">
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">Costureira</span>
                <span class="flex v-center">Período</span>
                <span class="flex v-center">Valor a Pagar</span>
                <span class="flex v-center">Chave Pix</span>
                <span class="flex v-center">Data do pagamento</span>
                <span class="flex v-center">Comprovante</span>
                <span class="flex v-center">Observação (Opcional)</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:35px"><?= htmlspecialchars($pagamento['costureira_nome']) ?></span>
                <span class="flex v-center" style="min-height:35px"><?= date('m/Y', strtotime($pagamento['periodo_referencia'])) ?></span>
                <span class="flex v-center" style="min-height:35px">R$ <?= number_format($pagamento['valor_liquido'] ?? $pagamento['valor_bruto'], 2, ',', '.') ?></span>
                <span class="flex v-center" style="min-height:35px">
                    <?php if (!empty($pagamento['chave_pix'])): ?>
                        <?= ucfirst($pagamento['tipo_chave_pix'] ?? 'PIX') ?> - <?= htmlspecialchars($pagamento['chave_pix']) ?>
                    <?php else: ?>
                        <span class="texto-cinza">Chave PIX não cadastrada</span>
                    <?php endif; ?>
                </span>

                <span class="flex v-center" style="min-height:35px">
                    <input type="date" name="data_pagamento" id="data_pagamento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </span>
                
                <span class="flex v-center" style="min-height:35px">
                    <div class="upload-comprovante-wrapper">
                        <label class="btn-upload" for="comprovante">
                            <img class="icone" src="<?= ASSETS_URL ?>icones/anexo.svg" alt="Anexo">
                            <span id="texto-upload">Escolher arquivo</span>
                        </label>
                        <input type="file" name="comprovante" id="comprovante" class="form-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" onchange="atualizarNomeArquivo(this)">
                        <small class="upload-info">Formatos: PDF, JPG, PNG (Max: 5MB)</small>
                        <div id="arquivo-selecionado" class="arquivo-selecionado"></div>
                    </div>
                </span>
                
                <span class="flex v-center" style="min-height:35px">
                    <textarea name="observacao" id="observacao" class="form-control" rows="2" placeholder="Informações adicionais sobre o pagamento..."><?= htmlspecialchars($observacao ?? '') ?></textarea>
                </span>
            </span>
         </span>
        <input type="hidden" name="id" value="<?= $pagamento['id'] ?>">

        <br>
        <hr>
        <div class="flex h-center l-gap">
            <button type="submit" class="botao-azul">Confirmar Pagamento</button>
            <a href="<?= BASE_URL ?>admin/pagamentos" class="botao">Cancelar</a>
        </div>

        <!-- Seção de Serviços Incluídos -->
        <div style="margin-top: 2rem;">
            <h3>Serviços Incluídos neste Pagamento</h3>
            
            <div class="tabela">
                <table cellspacing='0' class="redondinho shadow">
                    <thead>
                        <tr>
                            <th class="ae">Lote</th>
                            <th class="ae">Coleção</th>
                            <th class="ae">Operação</th>
                            <th class="ae">Quantidade</th>
                            <th class="ae">Valor Unitário</th>
                            <th class="ae">Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $itens = $pagamento['itens'] ?? [];
                        if (empty($itens)): 
                        ?>
                            <tr>
                                <td colspan="6" class="ac">Nenhum serviço encontrado para este pagamento</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($itens as $item): ?>
                            <tr>
                                <td class="ae"><?= htmlspecialchars($item['lote_nome'] ?? 'N/A') ?></td>
                                <td class="ae"><?= htmlspecialchars($item['colecao'] ?? 'N/A') ?></td>
                                <td class="ae"><?= htmlspecialchars($item['operacao_nome'] ?? 'N/A') ?></td>
                                <td class="ae"><?= $item['quantidade_pecas'] ?? 0 ?> peças</td>
                                <td class="ae">R$ <?= number_format($item['valor_operacao'] ?? 0, 2, ',', '.') ?></td>
                                <td class="ae">R$ <?= number_format($item['valor_calculado'] ?? 0, 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="5" class="ae"><strong>Total</strong></td>
                            <td class="ae"><strong>R$ <?= number_format($pagamento['valor_bruto'], 2, ',', '.') ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
function atualizarNomeArquivo(input) {
    const arquivoSelecionado = document.getElementById('arquivo-selecionado');
    const textoUpload = document.getElementById('texto-upload');
    
    if (input.files && input.files.length > 0) {
        const arquivo = input.files[0];
        const nomeArquivo = arquivo.name;
        const tamanhoMB = (arquivo.size / (1024 * 1024)).toFixed(2);
        
        // Validar tamanho (5MB máximo)
        if (arquivo.size > 5 * 1024 * 1024) {
            arquivoSelecionado.innerHTML = '<span style="color: #dc2626;">❌ Arquivo muito grande! Máximo 5MB</span>';
            textoUpload.textContent = 'Escolher arquivo';
            input.value = ''; // Limpar input
            return;
        }
        
        // Validar extensão
        const extensao = nomeArquivo.split('.').pop().toLowerCase();
        const extensoesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        
        if (!extensoesPermitidas.includes(extensao)) {
            arquivoSelecionado.innerHTML = '<span style="color: #dc2626;">❌ Formato não permitido</span>';
            textoUpload.textContent = 'Escolher arquivo';
            input.value = ''; // Limpar input
            return;
        }
        
        arquivoSelecionado.innerHTML = `✓ ${nomeArquivo} (${tamanhoMB} MB)`;
        textoUpload.textContent = 'Trocar arquivo';
    } else {
        arquivoSelecionado.innerHTML = '';
        textoUpload.textContent = 'Escolher arquivo';
    }
}

// Validar formulário antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const comprovante = document.getElementById('comprovante');
    const arquivoSelecionado = document.getElementById('arquivo-selecionado');
    
    // Se tem arquivo mas deu erro de validação
    if (comprovante.files.length > 0) {
        const arquivo = comprovante.files[0];
        
        if (arquivo.size > 5 * 1024 * 1024) {
            e.preventDefault();
            alert('O arquivo selecionado é muito grande. O tamanho máximo é 5MB.');
            return false;
        }
        
        const extensao = arquivo.name.split('.').pop().toLowerCase();
        const extensoesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        
        if (!extensoesPermitidas.includes(extensao)) {
            e.preventDefault();
            alert('Formato de arquivo não permitido. Use PDF, JPG, JPEG, PNG, DOC ou DOCX.');
            return false;
        }
    }
});

// Definir data máxima como hoje para o campo de data
document.addEventListener('DOMContentLoaded', function() {
    const dataInput = document.getElementById('data_pagamento');
    const hoje = new Date().toISOString().split('T')[0];
    dataInput.max = hoje;
});
</script>
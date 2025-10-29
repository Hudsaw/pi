<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>

    <form class="formulario-cadastro auth-form form-responsive" method="POST" 
          action="<?= BASE_URL ?>admin/atualizar-lote" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $lote['id'] ?>">
        
        <div class="titulo">Edição de Lote - <?= htmlspecialchars($lote['nome']) ?></div>

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
                <label class="form-label" for="empresa_id">Empresa</label>
                <select name="empresa_id" id="empresa_id" class="form-select" required>
                    <option value="">Selecione a empresa</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?= $empresa['id'] ?>" <?= ($lote['empresa_id'] == $empresa['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($empresa['nome']) ?> - CNPJ: <?= htmlspecialchars($empresa['cnpj']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">    
            <div class="form-group">
                <label class="form-label" for="colecao">Coleção</label>
                <input type="text" name="colecao" id="colecao" class="form-input" placeholder="Coleção" value="<?= htmlspecialchars($lote['colecao'] ?? '') ?>" required>
            </div>
        
            <div class="form-group">
                <label class="form-label" for="nome">Nome do Lote</label>
                <input type="text" name="nome" id="nome" class="form-input" placeholder="Nome do Lote" value="<?= htmlspecialchars($lote['nome'] ?? '') ?>" required>
            </div>
            </div>
        
        <div class="form-row">    
            <div class="form-group">
                <label class="form-label" for="data_entrada">Data Entrada</label>
                <input type="date" name="data_entrada" id="data_entrada" class="form-input" value="<?= htmlspecialchars($lote['data_entrada'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="data_entrega">Data Entrega</label>
                <input type="date" name="data_entrega" id="data_entrega" class="form-input" value="<?= htmlspecialchars($lote['data_entrega'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="anexo">
                    Anexo
                    <img class="icone" src="<?php echo ASSETS_URL?>icones/anexo.svg" alt="Anexo">
                </label>
                <?php if (!empty($lote['anexos'])): ?>
                    <div class="anexo-atual">
                        <small>Anexo atual: <?= htmlspecialchars($lote['anexos']) ?></small>
                    </div>
                <?php endif; ?>
                <input type="file" name="anexo" id="anexo" class="form-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                <small>Deixe em branco para manter o anexo atual</small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group-full">
                <label class="form-label" for="observacao">Observação</label>
                <textarea name="observacao" id="observacao" class="form-textarea" placeholder="Observações"><?= htmlspecialchars($lote['observacao'] ?? '') ?></textarea>
            </div>
        </div>
        
        <hr>
        <div class="titulo">Peças</div>
        <div class="tabela-formulario">
            <table cellspacing='0' class="redondinho shadow">
                <thead>
                    <tr>
                        <th>Tipo Peça</th>
                        <th>Cor</th>
                        <th>Tamanho</th>
                        <th>Operação</th>
                        <th>Quantidade</th>
                        <th>Valor Unitário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="pecas-container">
                    <!-- As peças existentes serão carregadas aqui via JavaScript -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="ac">
                            <button type="button" class="botao-azul" onclick="adicionarPeca()">Adicionar Peça</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/visualizar-lote?id=<?= $lote['id'] ?>" class="botao">Cancelar</a>
            <a href="<?= BASE_URL ?>admin/lotes" class="botao">Voltar para Lista</a>
            <input type="submit" class="botao-azul" value="Atualizar Lote">
        </div>
    </form>
</div>

<script>
    let pecaCount = 0;
    const pecasExistentes = <?= json_encode($pecasExistentes ?? []) ?>;

    // Carregar peças existentes
    function carregarPecasExistentes() {
        const container = document.getElementById('pecas-container');
        
        pecasExistentes.forEach((peca, index) => {
            const novaLinha = document.createElement('tr');
            novaLinha.className = 'linha-peca';
            
            novaLinha.innerHTML = `
                <td class="ac">
                    <select name="pecas[${index}][tipo_peca_id]" class="form-select" required>
                        <option value="">Selecione o tipo</option>
                        <?php foreach ($tiposPeca as $tipo): ?>
                            <option value="<?= $tipo['id'] ?>" ${peca.tipo_peca_id == <?= $tipo['id'] ?> ? 'selected' : ''}>
                                <?= htmlspecialchars($tipo['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="ac">
                    <select name="pecas[${index}][cor_id]" class="form-select" required>
                        <option value="">Selecione a cor</option>
                        <?php foreach ($cores as $cor): ?>
                            <option value="<?= $cor['id'] ?>" ${peca.cor_id == <?= $cor['id'] ?> ? 'selected' : ''}>
                                <?= htmlspecialchars($cor['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="ac">
                    <select name="pecas[${index}][tamanho_id]" class="form-select" required>
                        <option value="">Selecione o tamanho</option>
                        <?php foreach ($tamanhos as $tamanho): ?>
                            <option value="<?= $tamanho['id'] ?>" ${peca.tamanho_id == <?= $tamanho['id'] ?> ? 'selected' : ''}>
                                <?= htmlspecialchars($tamanho['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="ac">
                    <select name="pecas[${index}][operacao_id]" class="form-select" required>
                        <option value="">Selecione a operação</option>
                        <?php foreach ($operacoes as $operacao): ?>
                            <option value="<?= $operacao['id'] ?>" ${peca.operacao_id == <?= $operacao['id'] ?> ? 'selected' : ''}>
                                <?= htmlspecialchars($operacao['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="ac">
                    <input type="number" name="pecas[${index}][quantidade]" class="form-input" placeholder="Quantidade" min="1" value="${peca.quantidade}" required>
                </td>
                <td class="ac">
                    <input type="number" name="pecas[${index}][valor_unitario]" class="form-input" step="0.01" min="0" placeholder="Valor Unitário" value="${peca.valor_unitario}" required>
                </td>
                <td class="ac">
                    <button type="button" class="botao-remover pequeno" onclick="removerPeca(this)">Remover</button>
                </td>
            `;
            
            container.appendChild(novaLinha);
            pecaCount++;
        });

        // Se não houver peças existentes, adiciona uma linha vazia
        if (pecasExistentes.length === 0) {
            adicionarPeca();
        } else {
            pecaCount = pecasExistentes.length;
        }
    }

    function adicionarPeca() {
        const container = document.getElementById('pecas-container');
        const novaLinha = document.createElement('tr');
        novaLinha.className = 'linha-peca';

        novaLinha.innerHTML = `
        <td class="ac">
            <select name="pecas[${pecaCount}][tipo_peca_id]" class="form-select" required>
                <option value="">Selecione o tipo</option>
                <?php foreach ($tiposPeca as $tipo): ?>
                    <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="ac">
            <select name="pecas[${pecaCount}][cor_id]" class="form-select" required>
                <option value="">Selecione a cor</option>
                <?php foreach ($cores as $cor): ?>
                    <option value="<?= $cor['id'] ?>"><?= htmlspecialchars($cor['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="ac">
            <select name="pecas[${pecaCount}][tamanho_id]" class="form-select" required>
                <option value="">Selecione o tamanho</option>
                <?php foreach ($tamanhos as $tamanho): ?>
                    <option value="<?= $tamanho['id'] ?>"><?= htmlspecialchars($tamanho['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="ac">
            <select name="pecas[${pecaCount}][operacao_id]" class="form-select" required>
                <option value="">Selecione a operação</option>
                <?php foreach ($operacoes as $operacao): ?>
                    <option value="<?= $operacao['id'] ?>"><?= htmlspecialchars($operacao['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="ac">
            <input type="number" name="pecas[${pecaCount}][quantidade]" class="form-input" placeholder="Quantidade" min="1" required>
        </td>
        <td class="ac">
            <input type="number" name="pecas[${pecaCount}][valor_unitario]" class="form-input" step="0.01" min="0" placeholder="Valor Unitário" required>
        </td>
        <td class="ac">
            <button type="button" class="botao-remover pequeno" onclick="removerPeca(this)">Remover</button>
        </td>
    `;

        container.appendChild(novaLinha);
        pecaCount++;
    }

    function removerPeca(botao) {
        const linha = botao.closest('.linha-peca');
        if (document.querySelectorAll('.linha-peca').length > 1) {
            linha.remove();
        } else {
            alert('É necessário ter pelo menos uma peça no lote.');
        }
    }

    // Carregar peças quando a página carregar
    document.addEventListener('DOMContentLoaded', carregarPecasExistentes);
</script>
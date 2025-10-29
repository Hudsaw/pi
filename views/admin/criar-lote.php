<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>

    <form class="formulario-cadastro auth-form form-responsive" method="POST" action="<?= BASE_URL ?>admin/criar-lote">
        <div class="titulo">Cadastro de Lotes</div>

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
                <label class="form-label" for="empresa_id">ID Empresa</label>
                <input type="text" name="empresa_id" id="empresa_id" class="form-input" placeholder="ID da Empresa" value="<?= htmlspecialchars($old['empresa_id'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="colecao">Coleção</label>
                <input type="text" name="colecao" id="colecao" class="form-input" placeholder="Coleção" value="<?= htmlspecialchars($old['colecao'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="nome">Nome do Lote</label>
                <input type="text" name="nome" id="nome" class="form-input" placeholder="Nome do Lote" value="<?= htmlspecialchars($old['nome'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="data_entrada">Data Entrada</label>
                <input type="date" name="data_entrada" id="data_entrada" class="form-input" value="<?= htmlspecialchars($old['data_entrada'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="anexo">
                    Anexo
                    <img class="icone" src="<?php echo ASSETS_URL?>icones/anexo.svg" alt="Anexo">
                </label>
                <input type="file" name="anexo" id="anexo" class="form-input escondido">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group-full">
                <label class="form-label" for="observacao">Observação</label>
                <textarea name="observacao" id="observacao" class="form-textarea" placeholder="Observações"><?= htmlspecialchars($old['observacao'] ?? '') ?></textarea>
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
                        <th>Quantidade</th>
                        <th>Valor Unitário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="pecas-container">
                    <!-- Linha inicial -->
                    <tr class="linha-peca">
                        <td class="ac">
                            <select name="pecas[0][tipo_peca_id]" class="form-select" required>
                                <option value="">Selecione o tipo</option>
                                <?php foreach ($tiposPeca as $tipo): ?>
                                    <option value="<?= $tipo['id'] ?>" <?= (isset($old['pecas'][0]['tipo_peca_id']) && $old['pecas'][0]['tipo_peca_id'] == $tipo['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="ac">
                            <select name="pecas[0][cor_id]" class="form-select" required>
                                <option value="">Selecione a cor</option>
                                <?php foreach ($cores as $cor): ?>
                                    <option value="<?= $cor['id'] ?>" <?= (isset($old['pecas'][0]['cor_id']) && $old['pecas'][0]['cor_id'] == $cor['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cor['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="ac">
                            <select name="pecas[0][tamanho_id]" class="form-select" required>
                                <option value="">Selecione o tamanho</option>
                                <?php foreach ($tamanhos as $tamanho): ?>
                                    <option value="<?= $tamanho['id'] ?>" <?= (isset($old['pecas'][0]['tamanho_id']) && $old['pecas'][0]['tamanho_id'] == $tamanho['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tamanho['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="ac">
                            <input type="number" name="pecas[0][quantidade]" class="form-input" placeholder="Quantidade" min="1"
                                value="<?= htmlspecialchars($old['pecas'][0]['quantidade'] ?? '') ?>" required>
                        </td>
                        <td class="ac">
                            <input type="number" name="pecas[0][valor_unitario]" class="form-input" step="0.01" min="0"
                                placeholder="Valor Unitário"
                                value="<?= htmlspecialchars($old['pecas'][0]['valor_unitario'] ?? '') ?>" required>
                        </td>
                        <td class="ac">
                            <button type="button" class="botao-remover pequeno"
                                onclick="removerPeca(this)">Remover</button>
                        </td>
                    </tr>
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
            <a href="<?= BASE_URL ?>admin/lotes" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Salvar Lote">
        </div>
    </form>
</div>

<script>
    let pecaCount = 1;

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
</script>
<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>

    <form class="formulario-cadastro auth-form" method="POST" action="<?= BASE_URL ?>admin/criar-lote">
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
        <span class="flex vertical s-gap">
            <span class="flex space-between">
                <label class="flex v-center" for="empresa_id">ID Empresa</label>
                <!--<select name="empresa_id" id="empresa_id" required>
                <option value="">Selecione uma empresa</option>
                <?php foreach ($empresas as $empresa): ?>
                    <option value="<?= $empresa['id'] ?>" 
                        <?= (isset($old['empresa_id']) && $old['empresa_id'] == $empresa['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($empresa['nome']) ?> - CNPJ: <?= htmlspecialchars($empresa['cnpj']) ?>
                    </option>
                <?php endforeach; ?>
                </select> -->
                <input type="text" name="empresa_id" id="empresa_id" placeholder="ID da Empresa" value="<?= htmlspecialchars($old['empresa_id'] ?? '') ?>" required>
                <label class="flex v-center" for="colecao">Coleção</label>
                <input type="text" name="colecao" id="colecao" placeholder="Coleção" value="<?= htmlspecialchars($old['colecao'] ?? '') ?>" required>
                <label class="flex v-center" for="nome">Nome do Lote</label>
                <input type="text" name="nome" id="nome" placeholder="Nome do Lote" value="<?= htmlspecialchars($old['nome'] ?? '') ?>" required>
                <label class="flex v-center" for="data_entrada">Data Entrada</label>
                <input type="date" name="data_entrada" id="data_entrada" value="<?= htmlspecialchars($old['data_entrada'] ?? '') ?>" required>
                <label class="flex v-center s-gap" for="anexo">Anexo
                    <img class="icone" src="<?php echo ASSETS_URL?>icones/anexo.svg" alt="Anexo">
                </label>
                <input type="file" name="anexo" id="anexo" class="escondido" > 
            </span>
            <span class="flex"> 
                <label class="flex v-center" for="observacao">Observação</label>
                <input class="input-grande " name="observacao" id="observacao" placeholder="Observações"><?= htmlspecialchars($old['observacao'] ?? '') ?></input>
            </span>
        </span>
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
                            <select name="pecas[0][tipo_peca_id]" required>
                                <option value="">Selecione o tipo</option>
                                <?php foreach ($tiposPeca as $tipo): ?>
                                    <option value="<?= $tipo['id'] ?>" <?= (isset($old['pecas'][0]['tipo_peca_id']) && $old['pecas'][0]['tipo_peca_id'] == $tipo['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="ac">
                            <select name="pecas[0][cor_id]" required>
                                <option value="">Selecione a cor</option>
                                <?php foreach ($cores as $cor): ?>
                                    <option value="<?= $cor['id'] ?>" <?= (isset($old['pecas'][0]['cor_id']) && $old['pecas'][0]['cor_id'] == $cor['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cor['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="ac">
                            <select name="pecas[0][tamanho_id]" required>
                                <option value="">Selecione o tamanho</option>
                                <?php foreach ($tamanhos as $tamanho): ?>
                                    <option value="<?= $tamanho['id'] ?>" <?= (isset($old['pecas'][0]['tamanho_id']) && $old['pecas'][0]['tamanho_id'] == $tamanho['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tamanho['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="ac">
                            <input type="number" name="pecas[0][quantidade]" placeholder="Quantidade" min="1"
                                value="<?= htmlspecialchars($old['pecas'][0]['quantidade'] ?? '') ?>" required>
                        </td>
                        <td class="ac">
                            <input type="number" name="pecas[0][valor_unitario]" step="0.01" min="0"
                                placeholder="Valor Unitário"
                                value="<?= htmlspecialchars($old['pecas'][0]['valor_unitario'] ?? '') ?>" required>
                        </td>
                        <td class="ac">
                            <button type="button" class="botao-vermelho pequeno"
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
            <select name="pecas[${pecaCount}][tipo_peca_id]" required>
                <option value="">Selecione o tipo</option>
                <?php foreach ($tiposPeca as $tipo): ?>
                    <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="ac">
            <select name="pecas[${pecaCount}][cor_id]" required>
                <option value="">Selecione a cor</option>
                <?php foreach ($cores as $cor): ?>
                    <option value="<?= $cor['id'] ?>"><?= htmlspecialchars($cor['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="ac">
            <select name="pecas[${pecaCount}][tamanho_id]" required>
                <option value="">Selecione o tamanho</option>
                <?php foreach ($tamanhos as $tamanho): ?>
                    <option value="<?= $tamanho['id'] ?>"><?= htmlspecialchars($tamanho['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="ac">
            <input type="number" name="pecas[${pecaCount}][quantidade]" placeholder="Quantidade" min="1" required>
        </td>
        <td class="ac">
            <input type="number" name="pecas[${pecaCount}][valor_unitario]" step="0.01" min="0" placeholder="Valor Unitário" required>
        </td>
        <td class="ac">
            <button type="button" class="botao-vermelho pequeno" onclick="removerPeca(this)">Remover</button>
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
<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>

    <form class="formulario-cadastro auth-form" method="POST" action="<?= BASE_URL ?>admin/criar-lote">
        <div class="titulo">Cadastro de Peças</div>

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
        <span class="flex horizontal space-between"> 
        <span class="item-tabela">    
            <div class="titulo ">Tipo de peça</div>
            <div class="tabela-formulario">
                <table cellspacing='0' class="redondinho shadow">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="pecas-container">
                        <!-- Linha inicial -->
                        <tr class="linha-peca">
                            <td class="ac">
                                <input type="text" name="tipo[0][tipo]" placeholder="Tipo"
                                    value="<?= htmlspecialchars($old['tipo'][0]['tipo'] ?? '') ?>" required>
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
                                <button type="button" class="botao-azul" onclick="adicionarTipo()">Adicionar Tipo</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>  
            </span> 
            <span class="item-tabela"> 
             <div class="titulo ">Cores</div>
            <div class="tabela-formulario">
                <table cellspacing='0' class="redondinho shadow">
                    <thead>
                        <tr>
                            <th>Cor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="pecas-container">
                        <!-- Linha inicial -->
                        <tr class="linha-peca">
                            <td class="ac">
                                <input type="text" name="cor[0][cor]" placeholder="Cor"
                                    value="<?= htmlspecialchars($old['cor'][0]['cor'] ?? '') ?>" required>
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
                                <button type="button" class="botao-azul" onclick="adicionarTipo()">Adicionar cor</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </span> 
            <span class="item-tabela">
               <div class="titulo ">Tamanhos</div>
            <div class="tabela-formulario">
                <table cellspacing='0' class="redondinho shadow">
                    <thead>
                        <tr>
                            <th>Tamanho</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="pecas-container">
                        <!-- Linha inicial -->
                        <tr class="linha-peca">
                            <td class="ac">
                                <input type="text" name="tamanho[0][tamanho]" placeholder="Tamanho"
                                    value="<?= htmlspecialchars($old['tamanho'][0]['tamanho'] ?? '') ?>" required>
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
                                <button type="button" class="botao-azul" onclick="adicionarTipo()">Adicionar Tamanho</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </span>
        </span>
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/lotes" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Salvar">
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
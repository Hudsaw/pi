<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a href="<?= BASE_URL ?>admin/painel" class="item">Painel</a>
        <a href="<?= BASE_URL ?>admin/usuarios" class="item">Usuários</a>
        <a href="<?= BASE_URL ?>admin/lotes" class="item bold">Lotes</a>
        <a href="<?= BASE_URL ?>admin/operacoes" class="item">Operações</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
        <form class="formulario-cadastro" class="auth-form" method="POST" action="<?= BASE_URL ?>admin/criar-lote">
        <div class="titulo">Cadastro de Lotes</div>
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <hr class="shadow">
        <span class="inputs flex center">
            <span class="label flex vertical">
                <label class="flex v-center" for="colecao">Coleção</label>
                <label class="flex v-center" for="nome">Nome</label>
                <label class="flex v-center" for="data_inicio">Data entrada</label>
                <label class="flex v-center" for="descricao">Descrição</label>
                <label class="flex v-center" for="anexo">Anexo</label>
            </span>
            <span class="input flex vertical">
                <input type="text" name="colecao" id="colecao" placeholder="Coleção" value="<?= htmlspecialchars($old['colecao'] ?? '') ?>">
                <input type="text" name="nome" id="nome" placeholder="Nome" value="<?= htmlspecialchars($old['nome'] ?? '') ?>">
                <input type="date" name="data_inicio" id="data_inicio" value="<?= htmlspecialchars($old['data_inicio'] ?? '') ?>">
                <input type="text" name="descricao" id="descricao" placeholder="Descrição" value="<?= htmlspecialchars($old['descricao'] ?? '') ?>">
                <input type="file" name="anexo" id="anexo">
            </span>
        </span>
        <hr>
        <div class="titulo">Peças</div>
        <div class="tabela-formulario">
            <table cellspacing='0' class="redondinho shadow">
                <thead>
                    <tr>
                        <th class="ac">Tipo</th>
                        <th class="ac">Cor</th>
                        <th class="ac">Tamanho</th>
                        <th class="ac">Quantidade</th>
                        <th class="ac">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ac">
                            <select>
                                <option>Tipo</option>
                                <option>Camiseta</option>
                                <option>Moletom</option>
                                <option>Regata</option>
                                <option>Manga comprida</option>
                            </select>
                        </td>
                        <td class="ac">
                            <select>
                                <option>Cor</option>
                                <option>Ciano</option>
                                <option>Bege</option>
                                <option>Fucsia</option>
                                <option>Laranja</option>
                            </select>
                        </td>
                        <td class="ac">
                            <select>
                                <option>Tamanho</option>
                                <option>P</option>
                                <option>M</option>
                                <option>G</option>
                                <option>GG</option>
                            </select>
                        </td>
                        <td class="ac">
                            <input type="text" name="quantidade" id="quantidade" placeholder="Quantidade">
                        </td>
                        <td class="ac">
                            <input type="text" name="valor" id="valor" placeholder="Valor">
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="ac">
                            <button type="button" class="botao-azul" onfocus="adicionarPeca(this)">Adicionar Peça</button>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td colspan="3"></td>
                        <td id="quantidadeTotal">123</td>
                        <td id="valorTotal">123,23</td>
                    </tr> -->
                </tfoot>
            </table>
        </div>
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/lotes" class="botao">Voltar</a>
            <input type="submit" class="botao" value="Salvar">
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setupMasks();
    });

    function adicionarPeca(elemento) {
        const tabela = document.querySelector('.tabela-formulario table tbody');
        const novaLinha = document.createElement('tr');

        novaLinha.innerHTML = `
            <td class="ac">
                <select>
                    <option>Tipo</option>
                    <option>Camiseta</option>
                    <option>Moletom</option>
                    <option>Regata</option>
                    <option>Manga comprida</option>
                </select>
            </td>
            <td class="ac">
                <select>
                    <option>Cor</option>
                    <option>Ciano</option>
                    <option>Bege</option>
                    <option>Fucsia</option>
                    <option>Laranja</option>
                </select>
            </td>
            <td class="ac">
                <select>
                    <option>Tamanho</option>
                    <option>P</option>
                    <option>M</option>
                    <option>G</option>
                    <option>GG</option>
                </select>
            </td>
            <td class="ac">
                <input type="text" name="quantidade" id="quantidade" placeholder="Quantidade">
            </td>
            <td class="ac">
                <input type="text" name="valor" id="valor" placeholder="Valor">
            </td>
        `;

        console.log(novaLinha);

        tabela.appendChild(novaLinha);
        elemento.blur();
    }
</script>
    <!-- <div class="conteudo-formulario">
        <h2>Criar Novo Lote</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="erro">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>admin/criar-lote">
            <div class="campo">
                <label for="empresa_id">ID da Empresa</label>
                <input type="text" id="empresa_id" name="empresa_id" value="<?= htmlspecialchars($old['empresa_id'] ?? '') ?>" required>
            </div>
            
            <div class="campo">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" required><?= htmlspecialchars($old['descricao'] ?? '') ?></textarea>
            </div>
            
            <div class="campos-duplos">
                <div class="campo">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" value="<?= htmlspecialchars($old['quantidade'] ?? '') ?>" required>
                </div>
                
                <div class="campo">
                    <label for="valor">Valor Total (R$)</label>
                    <input type="number" id="valor" name="valor" step="0.01" min="0" value="<?= htmlspecialchars($old['valor'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="campos-duplos">
                <div class="campo">
                    <label for="data_inicio">Data de Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($old['data_inicio'] ?? '') ?>" required>
                </div>
                
                <div class="campo">
                    <label for="data_prazo">Data de Prazo</label>
                    <input type="date" id="data_prazo" name="data_prazo" value="<?= htmlspecialchars($old['data_prazo'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="botoes">
                <a href="<?= BASE_URL ?>admin/lotes" class="botao-cinza">Cancelar</a>
                <button type="submit" class="botao-azul">Criar Lote</button>
            </div>
        </form>
    </div>
</div> -->
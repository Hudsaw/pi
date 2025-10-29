<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>

    <div class="conteudo-tabela">
        <h2>Cadastro de Peças</h2>
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

        <div class="accordion-container">
            <!-- Tipos de Peça -->
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion('tipos')">
                    <h3>Tipos de Peça</h3>
                    <span class="accordion-icon" id="icon-tipos">+</span>
                </div>
                <div class="accordion-content" id="content-tipos">
                    <form method="POST" action="<?= BASE_URL ?>admin/criar-tipo-peca" class="form-adicionar">
                        <div class="flex s-gap v-center">
                            <input type="text" name="nome" placeholder="Novo tipo de peça" 
                                   value="<?= htmlspecialchars($old['tipo']['nome'] ?? '') ?>" required
                                   class="flex-1">
                            <input type="text" name="descricao" placeholder="Descrição (opcional)"
                                   value="<?= htmlspecialchars($old['tipo']['descricao'] ?? '') ?>"
                                   class="flex-1">
                            <button type="submit" class="botao-azul pequeno">Adicionar</button>
                        </div>
                    </form>
                    
                    <div class="tabela-formulario">
                        <table cellspacing='0' class="redondinho shadow">
                            <thead>
                                <tr>
                                    <th class="ae">Nome</th>
                                    <th class="ae">Descrição</th>
                                    <th class="ac">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tiposPeca)): ?>
                                    <tr>
                                        <td colspan="3" class="ac">Nenhum tipo de peça cadastrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tiposPeca as $tipo): ?>
                                        <tr>
                                            <td class="ae"><?= htmlspecialchars($tipo['nome']) ?></td>
                                            <td class="ae"><?= htmlspecialchars($tipo['descricao'] ?? '') ?></td>
                                            <td class="ac">
                                                <a href="<?= BASE_URL ?>admin/remover-tipo-peca?id=<?= $tipo['id'] ?>" 
                                                   onclick="return confirm('Tem certeza que deseja remover este tipo de peça?')"
                                                   title="Remover">
                                                    <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover">
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Cores -->
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion('cores')">
                    <h3>Cores</h3>
                    <span class="accordion-icon" id="icon-cores">+</span>
                </div>
                <div class="accordion-content" id="content-cores">
                    <form method="POST" action="<?= BASE_URL ?>admin/criar-cor" class="form-adicionar">
                        <div class="flex s-gap v-center">
                            <input type="text" name="nome" placeholder="Nova cor" 
                                   value="<?= htmlspecialchars($old['cor']['nome'] ?? '') ?>" required
                                   class="flex-1">
                            <div class="flex v-center s-gap">
                                <label class="small-label">Cor:</label>
                                <input type="color" name="codigo_hex" value="<?= htmlspecialchars($old['cor']['codigo_hex'] ?? '#000000') ?>" 
                                       class="color-picker">
                            </div>
                            <button type="submit" class="botao-azul pequeno">Adicionar</button>
                        </div>
                    </form>
                    
                    <div class="tabela-formulario">
                        <table cellspacing='0' class="redondinho shadow">
                            <thead>
                                <tr>
                                    <th class="ae">Nome</th>
                                    <th class="ae">Cor</th>
                                    <th class="ac">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cores)): ?>
                                    <tr>
                                        <td colspan="3" class="ac">Nenhuma cor cadastrada</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cores as $cor): ?>
                                        <tr>
                                            <td class="ae"><?= htmlspecialchars($cor['nome']) ?></td>
                                            <td class="ae">
                                                <div class="flex v-center s-gap">
                                                    <div class="cor-display" style="background-color: <?= htmlspecialchars($cor['codigo_hex']) ?>"></div>
                                                    <?= htmlspecialchars($cor['codigo_hex']) ?>
                                                </div>
                                            </td>
                                            <td class="ac">
                                                <a href="<?= BASE_URL ?>admin/remover-cor?id=<?= $cor['id'] ?>" 
                                                   onclick="return confirm('Tem certeza que deseja remover esta cor?')"
                                                   title="Remover">
                                                    <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover">
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tamanhos -->
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion('tamanhos')">
                    <h3>Tamanhos</h3>
                    <span class="accordion-icon" id="icon-tamanhos">+</span>
                </div>
                <div class="accordion-content" id="content-tamanhos">
                    <form method="POST" action="<?= BASE_URL ?>admin/criar-tamanho" class="form-adicionar">
                        <div class="flex s-gap v-center">
                            <input type="text" name="nome" placeholder="Novo tamanho" 
                                   value="<?= htmlspecialchars($old['tamanho']['nome'] ?? '') ?>" required
                                   class="flex-1">
                            <input type="number" name="ordem" placeholder="Ordem" min="1"
                                   value="<?= htmlspecialchars($old['tamanho']['ordem'] ?? '') ?>" required 
                                   class="input-ordem">
                            <button type="submit" class="botao-azul pequeno">Adicionar</button>
                        </div>
                    </form>
                    
                    <div class="tabela-formulario">
                        <table cellspacing='0' class="redondinho shadow">
                            <thead>
                                <tr>
                                    <th class="ae">Nome</th>
                                    <th class="ae">Ordem</th>
                                    <th class="ac">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tamanhos)): ?>
                                    <tr>
                                        <td colspan="3" class="ac">Nenhum tamanho cadastrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tamanhos as $tamanho): ?>
                                        <tr>
                                            <td class="ae"><?= htmlspecialchars($tamanho['nome']) ?></td>
                                            <td class="ae"><?= htmlspecialchars($tamanho['ordem']) ?></td>
                                            <td class="ac">
                                                <a href="<?= BASE_URL ?>admin/remover-tamanho?id=<?= $tamanho['id'] ?>" 
                                                   onclick="return confirm('Tem certeza que deseja remover este tamanho?')"
                                                   title="Remover">
                                                    <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="remover">
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <br>

    </div>
</div>

<script>
let accordionAberto = null;

function toggleAccordion(id) {
    const content = document.getElementById('content-' + id);
    const icon = document.getElementById('icon-' + id);
    
    // Fecha o accordion anterior se existir
    if (accordionAberto && accordionAberto !== id) {
        const contentAnterior = document.getElementById('content-' + accordionAberto);
        const iconAnterior = document.getElementById('icon-' + accordionAberto);
        
        contentAnterior.classList.remove('open');
        iconAnterior.classList.remove('open');
    }
    
    // Abre/fecha o atual
    if (content.classList.contains('open')) {
        content.classList.remove('open');
        icon.classList.remove('open');
        accordionAberto = null;
    } else {
        content.classList.add('open');
        icon.classList.add('open');
        accordionAberto = id;
    }
}
</script>
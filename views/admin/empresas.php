<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar-admin.php'; ?>
    
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap">
            <h2>Empresas</h2>
            <a href="<?= BASE_URL ?>admin/criar-empresa" class="botao-azul">Criar Empresa</a>
        </div>
        
        <!-- Filtros e busca -->
        <div class="filtro flex s-gap v-center">
            <form method="GET" class="flex v-center s-gap">
                <input type="text" name="search" placeholder="Buscar empresa..." value="<?= htmlspecialchars($termoBusca ?? '') ?>" class="campo-busca">
                <button type="submit" class="botao-azul pequeno">Buscar</button>
                <?php if (!empty($termoBusca)): ?>
                    <a href="?filtro=<?= $filtro ?>" class="botao-cinza pequeno">Limpar</a>
                <?php endif; ?>
            </form>
            
            <div class="filtro flex s-gap">
                <a href="?filtro=ativos" class="<?= ($filtro === 'ativos') ? 'botao-azul' : 'botao-cinza' ?>">Ativas</a>
                <a href="?filtro=inativos" class="<?= ($filtro === 'inativos') ? 'botao-azul' : 'botao-cinza' ?>">Inativas</a>
                <a href="?filtro=todos" class="<?= ($filtro === 'todos') ? 'botao-azul' : 'botao-cinza' ?>">Todas</a>
            </div>
        </div>
        
        <div class="tabela">
            <table cellspacing='0' class="redondinho shadow">
                <thead>
                    <tr>
                        <th class="ae">ID</th>
                        <th class="ae">Nome</th>
                        <th class="ae">CNPJ</th>
                        <th class="ae">Telefone</th>
                        <th class="ae">Cidade/UF</th>
                        <th class="ae">Status</th>
                        <th class="ac">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaEmpresas)): ?>
                        <tr>
                            <td colspan="7" class="ac">Nenhuma empresa encontrada</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaEmpresas as $empresa): ?>
                            <tr>
                                <td class="ae"><?= htmlspecialchars($empresa['id']) ?></td>
                                <td class="ae"><?= htmlspecialchars($empresa['nome']) ?></td>
                                <td class="ae"><?= $this->formatarCNPJ($empresa['cnpj']) ?></td>
                                <td class="ae"><?= $this->formatarTelefone($empresa['telefone']) ?></td>
                                <td class="ae"><?= htmlspecialchars($empresa['cidade']) ?>/<?= htmlspecialchars($empresa['estado']) ?></td>
                                <td class="ae">
                                    <span class="status-<?= $empresa['ativo'] ? 'ativo' : 'inativo' ?>">
                                        <?= $empresa['ativo'] ? 'Ativa' : 'Inativa' ?>
                                    </span>
                                </td>
                                <td class="ac">
                                    <a href="<?= BASE_URL ?>admin/mostrar-empresa?id=<?= $empresa['id'] ?>" title="Visualizar">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/visualizar.svg" alt="visualizar">
                                    </a>
                                    <a href="<?= BASE_URL ?>admin/editar-empresa?id=<?= $empresa['id'] ?>" title="Editar">
                                        <img class="icone" src="<?= ASSETS_URL ?>icones/editar.svg" alt="editar">
                                    </a>
                                    <?php if ($empresa['ativo']): ?>
                                        <a href="<?= BASE_URL ?>admin/remover-empresa?id=<?= $empresa['id'] ?>" 
                                           onclick="return confirm('Tem certeza que deseja desativar esta empresa?')"
                                           title="Desativar">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/remover.svg" alt="desativar">
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>admin/reativar-empresa?id=<?= $empresa['id'] ?>" 
                                           onclick="return confirm('Tem certeza que deseja reativar esta empresa?')"
                                           title="Reativar">
                                            <img class="icone" src="<?= ASSETS_URL ?>icones/reativar.svg" alt="reativar">
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

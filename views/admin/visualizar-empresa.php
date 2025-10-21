<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="conteudo-tabela">
        <div class="filtro flex s-gap v-center">
            <h2>Empresa: <?= htmlspecialchars($empresa['nome']) ?></h2>
            <a href="<?= BASE_URL ?>admin/editar-empresa?id=<?= $empresa['id'] ?>" class="botao-azul">Editar Empresa</a>
        </div>
        
        <div class="detalhes-empresa">
            <div class="info-empresa">
                <div class="info-item">
                    <span class="info-label">ID:</span>
                    <span class="info-value"><?= htmlspecialchars($empresa['id']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nome:</span>
                    <span class="info-value"><?= htmlspecialchars($empresa['nome']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">CNPJ:</span>
                    <span class="info-value"><?= $this->formatarCNPJ($empresa['cnpj']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($empresa['email'] ?? 'Não informado') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telefone:</span>
                    <span class="info-value"><?= $this->formatarTelefone($empresa['telefone']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Endereço:</span>
                    <span class="info-value"><?= htmlspecialchars($empresa['endereco'] ?? 'Não informado') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Cidade/UF:</span>
                    <span class="info-value"><?= htmlspecialchars($empresa['cidade'] ?? 'Não informado') ?>/<?= htmlspecialchars($empresa['estado'] ?? '') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">CEP:</span>
                    <span class="info-value"><?= $this->formatarCEP($empresa['cep']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Observação:</span>
                    <span class="info-value"><?= htmlspecialchars($empresa['observacao'] ?? 'Nenhuma') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value status-<?= $empresa['ativo'] ? 'ativo' : 'inativo' ?>">
                        <?= $empresa['ativo'] ? 'Ativa' : 'Inativa' ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Cadastro:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($empresa['created_at'])) ?></span>
                </div>
            </div>
        </div>
        
        <div class="botoes" style="margin-top: 20px;">
            <a href="<?= BASE_URL ?>admin/empresas" class="botao">Voltar para Lista</a>
        </div>
    </div>
</div>

<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    <form class="formulario-cadastro">
        <div class ="titulo"> <?= htmlspecialchars($empresa['nome']) ?></div>
        <hr class="shadow">
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center">ID:</span>
                <span class="flex v-center">Nome:</span>
                <span class="flex v-center">CNPJ:</span>
                <span class="flex v-center">Email:</span>
                <span class="flex v-center">Telefone:</span>
                <span class="flex v-center">Endereço:</span>
                <span class="flex v-center">Cidade/UF:</span>
                <span class="flex v-center">CEP:</span>
                <span class="flex v-center">Observação:</span>
                <span class="flex v-center">Status:</span>
                <span class="flex v-center">Data de Cadastro:</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($empresa['id']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($empresa['nome']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $this->formatarCNPJ($empresa['cnpj']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($empresa['email'] ?? 'Não informado') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $this->formatarTelefone($empresa['telefone']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($empresa['endereco'] ?? 'Não informado') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($empresa['cidade'] ?? 'Não informado') ?>/<?= htmlspecialchars($empresa['estado'] ?? '') ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $this->formatarCEP($empresa['cep']) ?></span>
                <span class="flex v-center" style="min-height:20px"><?= htmlspecialchars($empresa['observacao'] ?? 'Nenhuma') ?></span>
                <span class="flex v-center status-<?= $empresa['ativo'] ? 'ativo' : 'inativo' ?>" style="min-height:20px">
                    <?= $empresa['ativo'] ? 'Ativa' : 'Inativa' ?>
                </span>
                <span class="flex v-center" style="min-height:20px"><?= date('d/m/Y H:i', strtotime($empresa['created_at'])) ?></span>
            </span>
        </span>
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>admin/empresas" class="botao">Voltar</a>
            <a href="<?= BASE_URL ?>admin/editar-empresa?id=<?= $empresa['id'] ?>" class="botao">Editar Empresa</a>
        </div>
    </form>
</div>

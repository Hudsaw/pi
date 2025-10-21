<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="dashboard-costureira">
        <h2>Meu Perfil - <?= htmlspecialchars($nomeUsuario ?? 'Usuário') ?></h2>
        
        <div class="detalhes-perfil">
            <div class="info-perfil">
                <div class="info-item">
                    <span class="info-label">Nome Completo:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['nome']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telefone:</span>
                    <span class="info-value" id="telefone"><?= htmlspecialchars($usuario['telefone']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['email']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">CPF:</span>
                    <span class="info-value" id="cpf"><?= htmlspecialchars($usuario['cpf']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">CEP:</span>
                    <span class="info-value" id="cep"><?= htmlspecialchars($usuario['cep']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Logradouro:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['logradouro']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Complemento:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['complemento']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Cidade:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['cidade']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Especialidade:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['especialidade'] ?? 'Não informada') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo da chave PIX:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['tipo_chave_pix']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">PIX:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['chave_pix']) ?></span>
                </div>
            </div>
        </div>
        
        <div class="acoes-perfil flex h-center l-gap" style="margin-top: 30px;">
            <a href="<?= BASE_URL ?>costura/painel" class="botao">Voltar ao Painel</a>
            <a href="<?= BASE_URL ?>costura/editar-perfil" class="botao">Editar Perfil</a>
        </div>
    </div>
</div>

<script src="<?= ASSETS_URL ?>js/utils.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    formatarDadosExibidos();
});
</script>
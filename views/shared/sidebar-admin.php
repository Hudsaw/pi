<div class="menu flex vertical shadow">
    <a href="<?= BASE_URL ?>admin/painel" class="item <?= $paginaAtual === 'painel' ? 'bold' : '' ?>">Painel</a>
    <a href="<?= BASE_URL ?>admin/usuarios" class="item <?= $paginaAtual === 'usuarios' ? 'bold' : '' ?>">Usuários</a>
    <a href="<?= BASE_URL ?>admin/empresas" class="item <?= $paginaAtual === 'empresas' ? 'bold' : '' ?>">Empresas</a>
    <a href="<?= BASE_URL ?>admin/lotes" class="item <?= $paginaAtual === 'lotes' ? 'bold' : '' ?>">Lotes</a>
    <a href="<?= BASE_URL ?>admin/operacoes" class="item <?= $paginaAtual === 'operacoes' ? 'bold' : '' ?>">Operações</a>
    <a href="<?= BASE_URL ?>admin/servicos" class="item <?= $paginaAtual === 'servicos' ? 'bold' : '' ?>">Serviços</a>
    <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
</div>
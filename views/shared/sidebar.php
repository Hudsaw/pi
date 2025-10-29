<?php
$currentRoute = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$userRole = $_SESSION['tipo_usuario'] ?? 'guest';

// Mapeamento de rotas para cada tipo de usuário
$menuItems = [
    'admin' => [
        ['route' => 'admin/painel', 'label' => 'Painel'],
        ['route' => 'admin/lotes', 'label' => 'Lotes'],
        ['route' => 'admin/servicos', 'label' => 'Serviços'],
        ['route' => 'admin/usuarios', 'label' => 'Usuários'],
        ['route' => 'admin/empresas', 'label' => 'Empresas'],
        ['route' => 'admin/operacoes', 'label' => 'Operações'],
        ['route' => 'admin/pecas', 'label'=> 'Peças'],
    ],
    'costureira' => [
        ['route' => 'costura/painel', 'label' => 'Painel'],
        ['route' => 'costura/servicos', 'label' => 'Meus Serviços'],
        ['route' => 'costura/pagamentos', 'label' => 'Meus Pagamentos'],
        ['route' => 'costura/mensagens', 'label' => 'Mensagens'],
        ['route' => 'costura/visualizar-perfil', 'label' => 'Perfil']
    ]
];
?>

<aside class="menu flex vertical shadow">
    <div class="sidebar-header">
        <h3 style="color: var(--white);"><?= "Menu " . ucfirst($userRole) ?></h3>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <?php foreach ($menuItems[$userRole] ?? [] as $item): ?>
                <li class="<?= ($currentRoute === $item['route'] || strpos($currentRoute, $item['route'] . '/') === 0) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL . $item['route'] ?>">
                        <?= $item['label'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

<!-- Navbar Mobile -->
<nav class="navbar-mobile">
    <ul class="navbar-mobile-nav">
        <?php foreach ($menuItems[$userRole] ?? [] as $item): ?>
            <li class="<?= ($currentRoute === $item['route'] || strpos($currentRoute, $item['route'] . '/') === 0) ? 'active' : '' ?>">
                <a href="<?= BASE_URL . $item['route'] ?>">
                    <?= $item['label'] ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
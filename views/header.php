<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'PontoCerto' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL?>style.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL?>styleH.css">
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL?>icon.png">
</head>


<body class="light-theme">
    
    <header>
        <div class="container">
            <div class="header-container">
                <nav class="secao">
                    <div class="logo">
                        <div>
                        <a href="<?php echo BASE_URL?>" class="logo">
            <img src="<?php echo ASSETS_URL?>banner.png" alt="PontoCerto Logo" class="banner-img">
            </a>    
                        </div>
                    </div>

                    <nav class="nav-user">
                        <div class="user-greeting">
                            <a href="<?= BASE_URL ?>/cadastro" class="greeting-link">
                                Olá, <?= htmlspecialchars($nomeUsuario ?? 'Visitante') ?>!
                            </a>
                        </div>
                        <div class="user-actions">
                            <div>
                                <?php if ($usuarioLogado ?? false): ?>
                                    <a href="<?= BASE_URL ?>/logout" class="btn-logout">
                                        <span class="btn-text">Sair</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/login" class="btn-login">
                                        <span class="btn-text">Login</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </nav>
                </nav>
            </div>
    </header>
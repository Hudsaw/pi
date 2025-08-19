<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'PontoCerto' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleH.css">
    <link rel="icon" type="image/png" href="icon.png">
</head>


<body class="light-theme">
    
    <header>
        <div class="container">
            <div class="header-container">
                <nav class="secao">
                    <div class="logo">
                        <div>
                            <a href="<?= BASE_URL ?>" class="logo-text">PontoCerto</a>
                        </div>
                    </div>

                    <nav class="nav-user">
                        <div class="user-greeting">
                            <span>Olá, <?= htmlspecialchars($nomeUsuario ?? 'Visitante') ?>!</span>
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
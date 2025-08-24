<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'PontoCerto' ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL?>ajuda.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL?>style.css">
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL?>icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <?php if ($login ?? false): ?>
        <div class="principal azul flex center">
    <?php else: ?>
        <div class="principal">
            <?php if ($usuarioLogado ?? false): ?>
                <div class="topo flex h-center shadow">
                    <img class="imagem-topo" src="<?php echo ASSETS_URL?>banner.png" alt="banner">
                    <span class="flex l-gap v-center">
                        <span class="nome-usuario"><?= htmlspecialchars($nomeUsuario) ?></span>
                    </span>
                </div>
                <?php else: ?>        
                    <div class="topo flex space-between shadow">
                        <img src="<?php echo ASSETS_URL?>banner.png" alt="banner">
                        <span class="flex l-gap v-center">
                            <span class="bold">OLÁ, VISITANTE!</span>
                            <a href="<?= BASE_URL ?>/login" class="botao-grande">LOGIN</a>
                        </span>
                    </div>
            <?php endif; ?>
    <?php endif; ?>
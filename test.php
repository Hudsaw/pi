<?php
require_once __DIR__ . '/vendor/autoload.php';

// Teste se as classes podem ser carregadas
try {
    $base = new App\Controllers\BaseController();
    echo "BaseController carregado com sucesso!<br>";
    
    $page = new App\Controllers\PageController();
    echo "PageController carregado com sucesso!<br>";
    
    echo "Autoload está funcionando corretamente!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
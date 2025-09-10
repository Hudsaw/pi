<div class="conteudo">
    <div class="painel flex space-between">
        <span class="texto-inicial flex vertical">
            <h1><?= $dados['titulo'] ?></h1>
            <h4><?= $dados['descricao'] ?></h2>
        </span>
        <img src="<?php echo ASSETS_URL?>img/malharia.png" alt="maquina">
    </div>
    <div class="flex v-center vertical">
        <span class="titulo-cards">Com ele você:</span>
        <div class="cards-landing">
            <!-- Isso poderia ser uma lista que vem do back -->
            <div class="card-landing">Centraliza cadastros</div>
            <div class="card-landing">Organiza a produção</div>
            <div class="card-landing">Acompanha pedidos</div>
            <div class="card-landing">Gera relatórios de desempenho</div>
        </div>
    </div>
</div>
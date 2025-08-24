<div class="conteudo flex">
    <div class="menu flex vertical shadow">
        <a class="item bold">Usuários</a>
        <a href="<?= BASE_URL ?>/" class="sair">Sair</a>
    </div>
    <form class="formulario-cadastro">
        <div class="titulo"><?= $usuario['nome'] ?></div>
        <hr class="shadow">
        <span class="lista-informacoes flex center">
            <span class="lista-informacoes-coluna bold flex vertical">
                <span class="flex v-center" for="nome">Nome Completo</span>
                <span class="flex v-center" for="telefone">Telefone</span>
                <span class="flex v-center" for="email">Email</span>
                <span class="flex v-center" for="cpf">CPF</span>
                <span class="flex v-center" for="cep">CEP</span>
                <span class="flex v-center" for="logradouro">logradouro</span>
                <span class="flex v-center" for="complemento">Complemento</span>
                <span class="flex v-center" for="cidade">Cidade</span>
                <span class="flex v-center" for="tipo">Tipo da chave PIX</span>
                <span class="flex v-center" for="pix">PIX</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px" for="nome"><?= $usuario['nome'] ?></span>
                <span class="flex v-center" style="min-height:20px" for="telefone"><?= $usuario['telefone'] ?></span>
                <span class="flex v-center" style="min-height:20px" for="email"><?= $usuario['email'] ?></span>
                <span class="flex v-center" style="min-height:20px" for="cpf"><?= $usuario['cpf'] ?></span>
                <span class="flex v-center" style="min-height:20px" for="cep"><?= $usuario['cep'] ?></span>
                <span class="flex v-center" style="min-height:20px" for="logradouro"><?= $usuario[''] ?? '' ?></span>
                <span class="flex v-center" style="min-height:20px" for="complemento"><?= $usuario['complemento'] ?></span>
                <span class="flex v-center" style="min-height:20px" for="cidade"><?= $usuario[''] ?? '' ?></span>
                <span class="flex v-center" style="min-height:20px" for="tipo"><?= $usuario[''] ?? '' ?></span>
                <span class="flex v-center" style="min-height:20px" for="pix"><?= $usuario[''] ?? '' ?></span>
            </span>
        </span>
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>/usuarios" class="botao">Voltar</a>
            <a onclick="alert()" class="botao">Editar usuario</a>
            <a onclick="alert()" class="botao-remover">Remover usuário</a>
        </div>
    </form>
</div>
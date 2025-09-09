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
                <span class="flex v-center">Nome Completo</span>
                <span class="flex v-center">Telefone</span>
                <span class="flex v-center">Email</span>
                <span class="flex v-center">CPF</span>
                <span class="flex v-center">CEP</span>
                <span class="flex v-center">Logradouro</span>
                <span class="flex v-center">Complemento</span>
                <span class="flex v-center">Cidade</span>
                <span class="flex v-center">Tipo da chave PIX</span>
                <span class="flex v-center">PIX</span>
            </span>
            <span class="lista-informacoes-coluna flex vertical">
                <span class="flex v-center" style="min-height:20px"><?= $usuario['nome'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['telefone'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['email'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['cpf'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['cep'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['logradouro'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['complemento'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['cidade'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['tipo_chave_pix'] ?></span>
                <span class="flex v-center" style="min-height:20px"><?= $usuario['chave_pix'] ?></span>
            </span>
        </span>
        <br>
        <hr>
        <div class="flex h-center l-gap">
            <a href="<?= BASE_URL ?>/usuarios" class="botao">Voltar</a>
            <a href="<?= BASE_URL ?>/editar-usuario?id=<?= $usuario['id'] ?>" class="botao">Editar usuario</a>
            <a href="<?= BASE_URL ?>/remover-usuario?id=<?= $usuario['id'] ?>" class="botao-remover">Remover usuário</a>
        </div>
    </form>
</div>
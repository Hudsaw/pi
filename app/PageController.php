<?php
class PageController
{
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function home()
    {
        error_log("Exibindo pagina inicial");
        $user  = $this->getUsuario();
        $especialidade = $this->userModel->getEspecialidade();

        $this->render('home', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'dados'         => [
                'titulo'    => 'Centralize, organize e produza com precisão',
                'descricao' => 'O PontoCerto é um sistema de gerenciamento desenvolvido
                        especialmente para malharias.',
            ],
        ]);
    }

    public function mostrarCadastro()
    {
        error_log("Exibindo formulario de cadastro");
        $data = [
            'errors' => $_SESSION['registrar_erros'] ?? [],
            'dados'  => [],
            'areas'  => $this->userModel->getEspecialidade(),
        ];

        // Se usuário está logado, carrega seus dados
        if (isset($_SESSION['user_id'])) {
            $data['dados'] = $this->userModel->getUserPeloId($_SESSION['user_id']);
        }

        $this->render('cadastro', $data);
        unset($_SESSION['registrar_erros']);
    }

    // Métodos auxiliares

    private function getUsuario()
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $user = $this->userModel->getUserPeloId($_SESSION['user_id']);
    
    return $user;
}

    private function estaLogado()
    {
        return isset($_SESSION['user_id']);
    }


    private function render($view, $data = [])
    {
        extract($data);
        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . $view . '.php';
        require VIEWS_PATH . 'footer.php';
    }

    private function renderError($message)
    {
        $this->render('error', [
            'title'         => 'Erro',
            'message'       => $message,
            'user'          => $this->getUsuario(),
            'nomeUsuario'   => $this->getUsuario() ? $this->getUsuario()['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
        ]);
    }

    private function redirect($url)
    {
        $baseUrl = rtrim(BASE_URL, '/') . '/';
        $path = ltrim($url, '/');
        header("Location: " . $baseUrl . $path);
        exit();
    }

    public function painel()
    {
        error_log("Exibindo painel");
        $user  = $this->getUsuario();

        $this->render('painel', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
        ]);
    }

    public function usuarios()
    {
        error_log("Exibindo usuarios");
        $user  = $this->getUsuario();

        $this->render('usuarios', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'listaUsuarios' => $this->userModel->getTodosUser(), 
        ]);
    }

    public function visualizarUsuario()
    {
        error_log("Exibindo usuarios");
        $user  = $this->getUsuario();
        
        $id = $_GET['id'];

        $this->render('visualizar-usuario', [
            'title'         => 'PontoCerto',
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'usuario'       => $this->userModel->getUserPeloId($id),
        ]);
    }
}

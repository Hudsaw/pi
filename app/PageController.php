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
            'areas'         => $especialidade,
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->estaLogado(),
            'dados'         => [
                'titulo'    => 'Bem-vindo ao Gerenciamento de Facção',
                'descricao' => 'Sistema de gerenciamento de facção para controle de lotes e serviços',
                'usuario'   => $user,
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

}

# Crie o arquivo composer.json (se não existir)
composer init

# Ou se já tiver um composer.json, instale as dependências:
composer install
composer require pusher/pusher-php-server
composer require tecnickcom/tcpdf
composer require phpmailer/phpmailer

# Se precisar do autoloader do Composer
composer dump-autoload

pi/
├── App/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── PageController.php
│   │   └── ... outros controllers
│   ├── Core/
│   │   ├── BaseController.php
│   │   ├── Container.php
│   │   ├── Database.php
│   │   └── Router.php
│   ├── Middlewares/
│   │   ├── AuthMiddleware.php
│   │   └── RoleMiddleware.php
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── NotificacaoModel.php
│   │   └── ... outros models
│   └── Middlewares/
├── config/
│   └── constants.php
├── public/
│   ├── css/
│   │   └── style.php
│   ├── fonts/
│   │   └── SchibstedGrotesk.ttf
│   ├── img/
│   │   ├── banner.png
│   │   ├── icon.png
│   │   ├── logo.png
│   │   ├── malharia.png
│   │   └── maquina.png
│   ├── .htaccess
│   └── index.php
├── vendor/
├── views/
│   ├── admin/
│   │   ├── criar-usuario.php
│   │   ├── editar-usuario.php
│   │   ├── painel.php
│   │   ├── usuarios.php
│   │   └── visualizar-usuario.php
│   ├── auth/
│   │   ├── login.php
│   │   ├── nova-senha.php
│   │   ├── politica.php
│   │   ├── resetar-senha.php
│   │   └── termos.php
│   └── shared/
│       ├── 404.php
│       ├── footer.php
│       ├── header.php
│       └── home.php
├── .htaccess
├── banco.sql
├── composer.json
├── composer.lock
└── index.php

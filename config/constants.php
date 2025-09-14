<?php
// constants.php
 
// URLs absolutas (para navegador)
define('BASE_URL', 'http://localhost/pi/');
define('PUBLIC_URL', BASE_URL . 'public/');
define('ASSETS_URL', PUBLIC_URL . 'assets/');

// Caminhos físicos (para servidor)
define('BASE_PATH', '/pi/');
define('ROOT_PATH', realpath(__DIR__ . '/../') . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('VIEWS_PATH', ROOT_PATH . 'views/');
define('BACKUP_PATH', ROOT_PATH . 'backups/');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'tonhapaiano25@gmail.com');
define('SMTP_PASS', 'tnaz');
define('SMTP_PORT', 587);
define('SMTP_FROM', 'tonhapaiano25@gmail.com');

 // Configurações de ambiente
 define('DB_HOST', 'localhost');
 define('DB_NAME', 'pi');
 define('DB_USER', 'root');
 define('DB_PASS', '');
 
 // Configurações de sessão
ini_set('session.cookie_lifetime', 0); 

// Configurações do Pusher
define('PUSHER_APP_ID', '00');
define('PUSHER_APP_KEY', '00');
define('PUSHER_APP_SECRET', '00');
define('PUSHER_APP_CLUSTER', 'sa1');

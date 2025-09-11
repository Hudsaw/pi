<?php
namespace App\Core;

use App\Core\Container;

class Router
{
    private $routes = [
        'GET' => [],
        'POST' => []
    ];
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function get($uri, $controllerAction, $middlewares = [])
    {
        $this->addRoute('GET', $uri, $controllerAction, $middlewares);
    }

    public function post($uri, $controllerAction, $middlewares = [])
    {
        $this->addRoute('POST', $uri, $controllerAction, $middlewares);
    }

    private function addRoute($method, $uri, $controllerAction, $middlewares)
    {
        $this->routes[$method][$uri] = [
            'handler' => $controllerAction,
            'middlewares' => $middlewares
        ];
    }

    // No Router::dispatch(), adicione mais logs:
public function dispatch()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    error_log("SESSION no dispatch: " . print_r($_SESSION, true));
    
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    error_log("Request URI: " . $uri);
    error_log("Method: " . $method);

    if (BASE_PATH !== '' && strpos($uri, BASE_PATH) === 0) {
        $uri = substr($uri, strlen(BASE_PATH));
    }
    $uri = $uri ?: '/';
if ($uri[0] !== '/') {
    $uri = '/' . $uri;
}

    error_log("URI após processamento: " . $uri);
    
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $_GET);

    // Verifique se a rota existe
    if (isset($this->routes[$method][$uri])) {
        $route = $this->routes[$method][$uri];
        error_log("Rota encontrada: " . print_r($route, true));

        // Execute middlewares
        foreach ($route['middlewares'] as $middleware) {
            error_log("Executando middleware: " . $middleware);
            
            if (strpos($middleware, '::') !== false) {
                [$middlewareClass, $method] = explode('::', $middleware);
                error_log("Middleware class: " . $middlewareClass . ", method: " . $method);
                
                $middlewareInstance = $this->container->get($middlewareClass);
                if (!$middlewareInstance->$method()) {
                    error_log("Middleware bloqueou o acesso: " . $middleware);
                    return; 
                }
            } else {
                $middlewareInstance = $this->container->get($middleware);
                if (!$middlewareInstance->handle()) {
                    error_log("Middleware bloqueou o acesso: " . $middleware);
                    return; 
                }
            }
        }

        [$controllerName, $action] = explode('@', $route['handler']);
        error_log("Controller: " . $controllerName . ", Action: " . $action);
        
        $controller = $this->container->get($controllerName);
        
        if (method_exists($controller, $action)) {
            error_log("Executando controller: " . $controllerName . "::" . $action);
            return $controller->$action();
        } else {
            error_log("Método não existe: " . $action . " no controller " . $controllerName);
        }
    } else {
        error_log("Rota não encontrada para: " . $uri . " (" . $method . ")");
        error_log("Rotas GET disponíveis: " . print_r(array_keys($this->routes['GET']), true));
    }

    http_response_code(404);
    require VIEWS_PATH . 'shared/404.php';
}
}
<?php 

namespace App\Core;

class Router {
    private $routes = [];

    public function get($path,$handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function put($path, $handler) {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete($path, $handler) {
        $this->routes['DELETE'][$path] = $handler;
    }
    public function dispatch($method, $uri)
    {
    $path = parse_url($uri, PHP_URL_PATH);
    $path = str_replace('/albergue/public', '', $path);
    $path = rtrim($path, '/');
    if ($path === '') $path = '/';

    if (!isset($this->routes[$method])) {
        http_response_code(404);
        echo json_encode(['error' => 'Método nao registrado']);
        return;
    }

    foreach ($this->routes[$method] as $route => $handler) {

        $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route);

        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $path, $matches)) {
            array_shift($matches);
            return $handler(...$matches);
        }
    }

    http_response_code(404);
    echo json_encode(['error' => 'Rota nao encontrada', 'path_recebido' => $path]);
    }
}

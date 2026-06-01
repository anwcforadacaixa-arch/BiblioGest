<?php
namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler, ?callable $middleware = null): void {
        $this->routes[] = [
            'method'     => 'GET',
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware
        ];
    }

    public function post(string $path, array $handler, ?callable $middleware = null): void {
        $this->routes[] = [
            'method'     => 'POST',
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $uri    = '/' . ltrim(substr($uri, strlen($base)), '/');

        // Deixar passar ficheiros estáticos
        $extensao = pathinfo($uri, PATHINFO_EXTENSION);
        if (in_array($extensao, ['css', 'js', 'png', 'jpg', 'ico', 'svg', 'woff', 'woff2'])) {
            return;
        }

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
            $pattern = "@^{$pattern}$@";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                // Correr middleware se existir
                if ($route['middleware']) {
                    ($route['middleware'])();
                }

                [$controllerClass, $method] = $route['handler'];
                $controller = new $controllerClass();
                $controller->$method(...$matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["erro" => "Rota não encontrada."]);
    }
}
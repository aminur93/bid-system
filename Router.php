<?php
class Router {
    private $routes = [];

    public function add($method, $route, $action) {
        $this->routes[] = ['method' => $method, 'route' => $route, 'action' => $action];
    }

    public function dispatch($method, $uri) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['route'] === $uri) {
                // Split the action and call the corresponding controller method
                list($controller, $method) = explode('@', $route['action']);
                if (class_exists($controller) && method_exists($controller, $method)) {
                    $controllerInstance = new $controller();
                    $controllerInstance->$method();
                    return;
                } else {
                    // Handle method or controller not found
                    http_response_code(404);
                    echo "Controller or method not found";
                    return;
                }
            }
        }

        // If route not found
        http_response_code(404);
        echo "Route not found";
    }
}
?>
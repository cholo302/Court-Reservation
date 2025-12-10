<?php
/**
 * Simple Router Class
 */
class Router {
    private $routes = [];
    private static $instance = null;
    
    public function __construct() {
        self::$instance = $this;
    }
    
    public static function getInstance() {
        return self::$instance;
    }
    
    public function get($path, $handler) {
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
    
    public function dispatch($uri, $method) {
        // Handle PUT/DELETE via POST with _method field
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        $routes = $this->routes[$method] ?? [];
        
        foreach ($routes as $route => $handler) {
            $pattern = $this->convertToRegex($route);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                $params = [];
                
                // Extract named parameters
                preg_match_all('/\{(\w+)\}/', $route, $paramNames);
                foreach ($paramNames[1] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }
                
                return $this->callHandler($handler, $params);
            }
        }
        
        // 404 Not Found
        $this->show404();
    }
    
    private function convertToRegex($route) {
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }
    
    private function callHandler($handler, $params = []) {
        if (is_array($handler)) {
            [$controller, $method] = $handler;
            $instance = new $controller();
            return call_user_func_array([$instance, $method], $params);
        }
        
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
    }
    
    private function show404() {
        http_response_code(404);
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }
}

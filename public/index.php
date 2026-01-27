<?php
/**
 * Court Reservation System - Main Entry Point
 */

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Start session
session_start();

// Set base path
define('BASE_PATH', dirname(dirname(__FILE__)));
define('PUBLIC_PATH', __DIR__);

// Load .env file
if (file_exists(BASE_PATH . '/.env')) {
    $envLines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
        }
    }
}

// Simple routing
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$request_path = substr($request_uri, strlen($script_name));

// Remove trailing slash except for root
if ($request_path !== '/' && substr($request_path, -1) === '/') {
    $request_path = substr($request_path, 0, -1);
}

// Route handler
function route($path, $controller, $method = 'GET')
{
    global $request_path, $_SERVER;
    
    $request_method = $_SERVER['REQUEST_METHOD'];
    
    // Check method
    if (is_array($method) ? !in_array($request_method, $method) : $request_method !== $method) {
        return false;
    }
    
    // Simple path matching (you can enhance this with regex if needed)
    if ($path === $request_path) {
        // Load controller
        $controller_parts = explode('@', $controller);
        $controller_class = 'App\\Http\\Controllers\\' . $controller_parts[0];
        $controller_method = $controller_parts[1] ?? 'index';
        
        // Try to call the controller
        try {
            $ctrl = new $controller_class();
            return call_user_func([$ctrl, $controller_method]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return true;
        }
    }
    
    return false;
}

// Helper function to render views
function view($path, $data = [])
{
    extract($data);
    include BASE_PATH . '/resources/views/' . str_replace('.', '/', $path) . '.php';
}

// Define routes
$handled = false;

// Auth routes
if (route('/login', 'Auth\\AuthController@showLogin')) { $handled = true; }
elseif (route('/login', 'Auth\\AuthController@login', 'POST')) { $handled = true; }
elseif (route('/register', 'Auth\\AuthController@showRegister')) { $handled = true; }
elseif (route('/register', 'Auth\\AuthController@register', 'POST')) { $handled = true; }
elseif (route('/logout', 'Auth\\AuthController@logout')) { $handled = true; }

// Home route
elseif (route('/', function() {
    echo view('home.index', ['title' => 'Home - Court Reservation System']);
})) { $handled = true; }

// If no route matched
if (!$handled) {
    http_response_code(404);
    echo view('errors.404', ['title' => '404 Not Found']);
}

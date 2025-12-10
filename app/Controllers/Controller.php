<?php
/**
 * Base Controller Class
 */
class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    protected function view($name, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $name) . '.php';
        
        if (!file_exists($viewPath)) {
            die("View not found: {$name}");
        }
        
        ob_start();
        require $viewPath;
        return ob_get_clean();
    }
    
    protected function render($name, $data = []) {
        echo $this->view($name, $data);
    }
    
    protected function renderWithLayout($name, $data = [], $layout = 'layouts.app') {
        $data['content'] = $this->view($name, $data);
        echo $this->view($layout, $data);
    }
    
    protected function json($data, $code = 200) {
        jsonResponse($data, $code);
    }
    
    protected function redirect($path, $message = null, $type = 'success') {
        if ($message) {
            flash($type, $message);
        }
        redirect($path);
    }
    
    protected function back($message = null, $type = 'error') {
        if ($message) {
            flash($type, $message);
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        header('Location: ' . $referer);
        exit;
    }
    
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $ruleSet) {
            $ruleList = explode('|', $ruleSet);
            $value = $data[$field] ?? null;
            
            foreach ($ruleList as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }
                
                $error = $this->validateRule($field, $value, $rule, $params, $data);
                if ($error) {
                    $errors[$field] = $error;
                    break;
                }
            }
        }
        
        return $errors;
    }
    
    private function validateRule($field, $value, $rule, $params, $allData) {
        $fieldName = ucfirst(str_replace('_', ' ', $field));
        
        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    return "{$fieldName} is required.";
                }
                break;
                
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "{$fieldName} must be a valid email.";
                }
                break;
                
            case 'min':
                if (strlen($value) < $params[0]) {
                    return "{$fieldName} must be at least {$params[0]} characters.";
                }
                break;
                
            case 'max':
                if (strlen($value) > $params[0]) {
                    return "{$fieldName} must not exceed {$params[0]} characters.";
                }
                break;
                
            case 'confirmed':
                if ($value !== ($allData[$field . '_confirmation'] ?? null)) {
                    return "{$fieldName} confirmation does not match.";
                }
                break;
                
            case 'unique':
                $table = $params[0];
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;
                
                $sql = "SELECT id FROM {$table} WHERE {$column} = ?";
                $sqlParams = [$value];
                
                if ($exceptId) {
                    $sql .= " AND id != ?";
                    $sqlParams[] = $exceptId;
                }
                
                $existing = $this->db->fetch($sql, $sqlParams);
                if ($existing) {
                    return "{$fieldName} already exists.";
                }
                break;
                
            case 'numeric':
                if ($value && !is_numeric($value)) {
                    return "{$fieldName} must be a number.";
                }
                break;
                
            case 'date':
                if ($value && !strtotime($value)) {
                    return "{$fieldName} must be a valid date.";
                }
                break;
                
            case 'phone':
                // Philippine phone format
                if ($value && !preg_match('/^(09|\+639)\d{9}$/', preg_replace('/\s+/', '', $value))) {
                    return "{$fieldName} must be a valid Philippine phone number.";
                }
                break;
        }
        
        return null;
    }
    
    protected function requireAuth() {
        if (!isLoggedIn()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            $this->redirect('login', 'Please login to continue.', 'warning');
        }
    }
    
    protected function requireAdmin() {
        $this->requireAuth();
        if (!isAdmin()) {
            $this->redirect('/', 'Access denied.', 'error');
        }
    }
    
    protected function requireGuest() {
        if (isLoggedIn()) {
            $this->redirect('/');
        }
    }
}

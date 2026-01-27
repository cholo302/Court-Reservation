<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

class AuthController
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        
        return view('auth.login', [
            'title' => 'Login - Court Reservation System'
        ]);
    }
    
    /**
     * Handle login request
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required.';
            header('Location: /login');
            exit;
        }
        
        // Authenticate user
        $user = User::authenticate($email, $password);
        
        if (!$user) {
            $_SESSION['error'] = 'Invalid email or password.';
            header('Location: /login');
            exit;
        }
        
        // Check if blacklisted
        if ($user['is_blacklisted']) {
            $_SESSION['error'] = 'Your account has been suspended. Reason: ' . ($user['blacklist_reason'] ?? 'Contact admin.');
            header('Location: /login');
            exit;
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        $_SESSION['success'] = 'Welcome back, ' . $user['name'] . '!';
        
        // Redirect based on role
        $redirect = $user['role'] === 'admin' ? '/admin/dashboard' : '/';
        header('Location: ' . $redirect);
        exit;
    }
    
    /**
     * Show registration form
     */
    public function showRegister()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        
        return view('auth.register', [
            'title' => 'Register - Court Reservation System'
        ]);
    }
    
    /**
     * Handle registration request
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';
        
        // Validate input
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters.';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email.';
        }
        
        if (User::exists($email)) {
            $errors[] = 'Email already registered.';
        }
        
        if (empty($phone) || strlen($phone) < 10) {
            $errors[] = 'Please enter a valid phone number.';
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            $_SESSION['old'] = $_POST;
            header('Location: /register');
            exit;
        }
        
        // Create user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $password
            ]);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            $_SESSION['success'] = 'Registration successful! Welcome, ' . $name . '!';
            header('Location: /');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Registration failed: ' . $e->getMessage();
            header('Location: /register');
            exit;
        }
    }
    
    /**
     * Handle logout
     */
    public function logout()
    {
        session_destroy();
        $_SESSION = [];
        header('Location: /');
        exit;
    }
}

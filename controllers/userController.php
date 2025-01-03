<?php
namespace controllers;

use models\UserModel;
use entity\UserEntity;

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function register($data) {
        // Validate input data (you might want to add more validation)
        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }

        // Attempt to create user
        $result = $this->userModel->create($data);
        
        if ($result['status'] === 'exists') {
            return ['success' => false, 'message' => $result['message']];
        }
        
        if ($result['status'] === 'success') {
            return ['success' => true, 'user_id' => $result['user_id']];
        }
        
        return ['success' => false, 'message' => $result['message']];
    }

    public function login($email, $password) {
        // Find user by email
        $user = $this->userModel->getOneByEmail($email);

        if ($user['status'] === 'not_exist') {
            return ['success' => false, 'message' => 'User does not exist'];
        }

        if ($user['data']['password'] === $password) {
            return ['success' => true, 'user' => $user['data']];
        }
        
        return ['success' => false, 'message' => 'Invalid password'];
    }

    public function handleRequests() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Check if login or register
                if (isset($_POST['login'])) {
                    $email = $_POST['email'] ?? null;
                    $password = $_POST['password'] ?? null;
    
                    if (!$email || !$password) {
                        $_SESSION['auth_error'] = 'Email and password are required.';
                        header('Location: /auth');
                        exit();
                    }
    
                    $res = $this->login($email, $password);
    
                    if (!$res['success']) {
                        $_SESSION['auth_error'] = $res['message'];
                        header('Location: /auth');
                        exit();
                    }
                    
                    $_SESSION['user'] = $res['user'];
                    header('Location: /');
                    exit();
    
                } elseif (isset($_POST['register'])) {
                    $username = $_POST['username'] ?? null;
                    $email = $_POST['email'] ?? null;
                    $password = $_POST['password'] ?? null;
    
                    if (!$username || !$email || !$password) {
                        $_SESSION['auth_error'] = 'All fields are required for registration.';
                        header('Location: /auth');
                        exit();
                    }
    
                    $res = $this->register([
                        'username' => $username,
                        'email' => $email,
                        'password' => $password
                    ]);
    
                    if (!$res['success']) {
                        $_SESSION['auth_error'] = $res['message'];
                        header('Location: /auth');
                        exit();
                    }

                    $_SESSION['auth_success'] = 'Registration successful! Please login.';
                    header('Location: /auth');
                    exit();
                }
            }
        } catch (\Exception $e) {
            $_SESSION['auth_error'] = $e->getMessage();
            header('Location: /auth');
            exit();
        }
    }
    
    private function handleError($message, $statusCode = 400) {
        $_SESSION['auth_error'] = $message;
        header('Location: /auth');
        exit();
    }
}
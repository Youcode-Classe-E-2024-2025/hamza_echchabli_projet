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

        return $result;
    }

    public function login($email, $password) {
        // Find user by email
        $user = $this->userModel->getOneByEmail($email);

        if ($user === null) {
            return ['success' => false, 'message' => 'email not found'];
        }


        if ($user->getPassword() === $password) {
            return ['success' => true, 'message' => 'Login successful', 'user' => $user];
        } else {
            return ['success' => false, 'message' => 'Invalid password'];
        }
    }

    public function handleRequests() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' ){

         try {
            $rawInput = file_get_contents('php://input');
            $requestData = json_decode($rawInput, true);
            if (isset($requestData['action']) && $requestData['action'] === 'login') {
             
                http_response_code(200);
            // echo json_encode('login');
            $res=$this->login($requestData['email'], $requestData['password']);

            echo json_encode($res);

              }elseif(isset($requestData['action']) && $requestData['action'] === 'register') {
                http_response_code(200);
                $res=$this->register([
                    'username' => $requestData['username'],
                    'email' => $requestData['email'],
                    'password' => $requestData['password']
                ]);
                $_SESSION['user_id'] = $res['user_id'];
                header('Location: /home');
                exit();
             }
             else {

                throw new \Exception("Invalid action specified");
            }

        }catch (\Exception $e) {
            

                http_response_code(500);  
                echo json_encode([
                    'error' => true,
                    'message' => $e->getMessage()
                ]);
            }
        

    }


        
    }

    private function handleError($message, $statusCode = 400) {


        http_response_code($statusCode);


        $_SESSION['login_error'] = $message;

        header('Location: /auth');
        exit();
    }
}
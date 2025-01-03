<?php
namespace models;

use entities\UserEntity;
use Config\DB;

class UserModel {

    public  function ifExists($email) {
        $res = DB::query("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        return $res->fetch(\PDO::FETCH_ASSOC);
    }

   
    public function create($data){
        // Check if email already exists
        $existingUser = $this->ifExists($data['email']);
        if ($existingUser) {
            return [
                'status' => 'exists',
                'message' => 'Email already registered',
                'user_id' => null
            ];
        }

        try {
            $res = DB::query(
                "INSERT INTO users (username, email, password) VALUES (:username, :email, :password) RETURNING id",
                $data
            );
            $result = $res->fetch(\PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'message' => 'User registered successfully',
                'user_id' => $result['id']
            ];
        } catch(\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage(),
                'user_id' => null
            ];
        }
    }


    public function getOneByEmail($email){
        $res = $this->ifExists($email);
        
        if (empty($res)) {
            return [
                'status' => 'not_exist',
                'message' => 'User not found',
                'data' => null
            ];
        }

        return [
            'status' => 'exists',
            'message' => 'User found',
            'data' => $res
        ];
    }




}






?>
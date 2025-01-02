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
        
        if($this->ifExists($data['email'])){
            return ['success' => false, 'message' => 'Email already exists'];
        }


        try{
            $res = DB::query(
                "INSERT INTO users (username, email, password) VALUES (:username, :email, :password) RETURNING id",
                $data
            );
            $result = $res->fetch(\PDO::FETCH_ASSOC);
        return ['success' => true, 'user_id' => $result['id']];
        }catch(\Exception $e){
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }



    }


    public function getOneByEmail($email){

        $res = $this->ifExists($email);
        
        if (empty($res)) {
            return null;
        }

       return new UserEntity(
            $res['id'], 
            $res['username'], 
            $res['email'], 
            $res['password']
        );
    }




}






?>
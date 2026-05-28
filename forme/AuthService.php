<?php

namespace forme;

use PDOException;

class AuthService{
    private $users;
    function __construct($b){
        $this->users = $b;
    }
function register(string $username, string $password){
     $t = $this->users->prepare("SELECT * FROM users WHERE login = :username");
     $t->execute(["username" => $username]);

     if($t->rowCount() > 0){
         return false;
     }else{
         try{
             $t = $this->users->prepare('INSERT INTO users (login, password_hash) VALUES (:username, :password)');
             $t->execute(array(':username' => $username, ':password' => password_hash($password, PASSWORD_DEFAULT)));
             return true;
         }catch(PDOException $e){
             echo "User with such username already exists";
         }


return true;
     }
}

    function login(string $username, string $password){
        $s = $this->users->prepare('SELECT * FROM users');
        $s->execute();
        if(empty($s->fetchAll())){
            $k = $this->users->prepare('INSERT INTO users (login, password_hash) VALUES (:username, :password)');
            $k->execute(array(':username' => "admin", ':password' => password_hash("admin123", PASSWORD_DEFAULT)));

            $k = $this->users->prepare('INSERT INTO users (login, password_hash) VALUES (:username, :password)');
            $k->execute(array(':username' => "jan", ':password' => password_hash("haslo456", PASSWORD_DEFAULT)));

            $k = $this->users->prepare('INSERT INTO users (login, password_hash) VALUES (:username, :password)');
            $k->execute(array(':username' => "anna", ':password' => password_hash("anna789", PASSWORD_DEFAULT)));
        }else{
            $t = $this->users->prepare('SELECT * FROM users WHERE login = :username');
            $t->execute(array(':username' => $username));
            $user = $t->fetch();
                if(!$user){
                    echo "Such user does not exist, please register a new one";
                }else{
                    if(password_verify($password, $user['password_hash'])){
                        $_SESSION['username'] = $user['username'];
                        return true;
                    }else{
                        echo "Wrong password";
                        return false;
                    }
                }









        }

    }






    function logout(){
        unset($_SESSION);
        setcookie(session_name(), session_id(), time() - 3600);
    }
    function  currentUser(){
        return $_SESSION['user'];
    }

    function isLoggedIn(){
        return isset($_SESSION['user']);
    }




}
<?php

namespace forme;

class AuthService{



    function login(string $username, string $password){
        require_once("forme/User.php");
        $accounts=["admin" =>password_hash("admin123", PASSWORD_DEFAULT),"user" =>password_hash("user123",PASSWORD_DEFAULT),"employee" =>password_hash("employee123", PASSWORD_DEFAULT)];
        if(key_exists($username, $accounts)){
            if(password_verify($password, $accounts[$username])){
                session_regenerate_id(true);
                $user = new User($username, $password);
                $_SESSION['user'] = $user;
                return true;
            }else{
                return false;
            }
        }else{
            echo "No such account under this username exists";
            return false;
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
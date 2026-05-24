<?php

namespace forme;

class AuthService{
    private $users;
    function __construct($b){
        $this->users = $b;
    }
function register(string $username, string $password){
    $t = $this->users->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
    $t->execute(array(':username' => $username, ':password' => password_hash($password, PASSWORD_DEFAULT)));


}

    function login(string $username, string $password){
        $s = $this->users->prepare('SELECT * FROM users');
        if(empty($s->fetchAll())){
            $k = $this->users->prepare('INSERT INTO users (login, password_hash) VALUES (:username, :password)');
            $k->execute(array(':username' => "admin", ':password' => password_hash("admin123", PASSWORD_DEFAULT)));

            $k = $this->users->prepare('INSERT INTO users (login, password_hash) VALUES (:username, :password)');
            $k->execute(array(':username' => "jan", ':password' => password_hash("haslo456", PASSWORD_DEFAULT)));

            $k = $this->users->prepare('INSERT INTO users (login, password_hash) VALUES (:username, :password)');
            $k->execute(array(':username' => "anna", ':password' => password_hash("anna789", PASSWORD_DEFAULT)));
        }


    $t = $this->users->prepare('SELECT * FROM users WHERE login = :username');
    $t->execute(array(':username' => $username));
    $user = $t->fetch();
    if(password_verify($password, $user['password_hash'])){
$_SESSION['username'] = $user['username'];
    }
    return true;

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
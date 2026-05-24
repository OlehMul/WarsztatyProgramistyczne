<?php

namespace forme;

class User{
private string $login;
private string $password;
private string $createdAt;

function __construct(string $login, string $password, string $createdAt){
    $this->login = $login;
    $this->password = password_hash($password, PASSWORD_DEFAULT );
}

    public function getLogin(): string
    {
        return $this->login;
    }




}
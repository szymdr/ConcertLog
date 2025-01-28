<?php

class User {
    private $username;
    private $email;
    private $password;

    public function __construct(
        string $username,
        string $email,
        string $password
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string 
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
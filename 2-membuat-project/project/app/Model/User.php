<?php

namespace AriefKarditya\LocalDomainPhp\Model;

class User
{
    public function __construct(
        private string $id,
        private string $username,
        private string $email,
        private string $password,
    ) {
        # Kode
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}

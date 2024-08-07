<?php

namespace AriefKarditya\LocalDomainPhp\Domain;

class User
{
    public function __construct(
        private string $id,
        private string $name,
        private string $password
    ) # 
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}

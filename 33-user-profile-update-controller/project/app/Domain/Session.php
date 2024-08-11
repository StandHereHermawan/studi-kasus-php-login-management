<?php

namespace AriefKarditya\LocalDomainPhp\Domain;

class Session
{
    private string $id;
    private string $userId;

    public function __construct()
    {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function setUserId(string $userId)
    {
        $this->userId = $userId;
    }
}

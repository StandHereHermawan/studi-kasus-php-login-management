<?php

namespace AriefKarditya\LocalDomainPhp\Model;

class response
{
    public function __construct(private string $message)
    {
        # konstruktor
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}

<?php

namespace AriefKarditya\LocalDomainPhp\Model;

use AriefKarditya\LocalDomainPhp\Domain\User;

class UserPasswordUpdateRequest
{
    private ?string $id = null;
    private ?string $oldPassword = null;
    private ?string $newPassword = null;

    public function __construct() {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function setId(string $newPassword): void
    {
        $this->id = $newPassword;
    }

    public function setIdAndOldPasswordFromDomainUser(User $user): void
    {
        $this->id = $user->getId();
        $this->oldPassword = $user->getPassword();
    }
}

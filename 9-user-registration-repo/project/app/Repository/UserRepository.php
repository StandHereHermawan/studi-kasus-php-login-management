<?php

namespace AriefKarditya\LocalDomainPhp\Repository;

use AriefKarditya\LocalDomainPhp\Domain\User;

class UserRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $prepareStatement = $this->connection->prepare("INSERT INTO users(id, name, password) VALUES(?, ?, ?)");
        $prepareStatement->execute([$user->getId(), $user->getName(), $user->getPassword()]);
        return $user;
    }
}

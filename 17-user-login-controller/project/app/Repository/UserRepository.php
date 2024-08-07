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

    public function findById(string $id): ?User
    {
        $prepareStatement = $this->connection->prepare("SELECT id, name, password FROM users WHERE id = ?");
        $prepareStatement->execute([$id]);

        try {
            if ($row = $prepareStatement->fetch()) {
                $user = new User();
                $user->setId($row['id']);
                $user->setName($row['name']);
                $user->setPassword($row['password']);
                return $user;
            } else {
                return null;
            }
        } catch (\Throwable $th) {
        } finally {
            $prepareStatement->closeCursor();
        }
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM users");
    }
}

<?php

namespace AriefKarditya\LocalDomainPhp\Repository;

use AriefKarditya\LocalDomainPhp\Domain\Session;

class SessionRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Session $session): Session
    {
        $prepareStatement = $this->connection->prepare("INSERT INTO sessions(id, user_id) VALUES (?,?)");
        $prepareStatement->execute([$session->getId(), $session->getUserId()]);
        return $session;
    }

    public function findById(string $id): ?Session
    {
        $prepareStatement = $this->connection->prepare("SELECT id, user_id FROM sessions WHERE id = ?");
        $prepareStatement->execute([$id]);

        try {
            if ($row = $prepareStatement->fetch()) {
                $session = new Session();
                $session->setId($row['id']);
                $session->setUserId($row['user_id']);
                return $session;
            } else {
                return null;
            }
        } finally {
            $prepareStatement->closeCursor();
        }
    }

    public function deleteById(string $id): void
    {
        $prepareStatement = $this->connection->prepare("DELETE FROM sessions WHERE id = ?");
        $prepareStatement->execute([$id]);
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM sessions");
    }
}

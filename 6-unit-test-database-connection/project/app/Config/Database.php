<?php

namespace AriefKarditya\LocalDomainPhp\Config;

class Database
{
    private static ?\PDO $pdo = null;

    public static function getConnection(string $env = "test"): \PDO
    {
        if (self::$pdo != null) {
            return self::$pdo;
        } else {
            # Create new PHP Data Object
            require_once __DIR__ . "/../../config/database.php";
            $config = getDatabaseConfig();
            self::$pdo = new \PDO(
                $config['database'][$env]['url'],
                $config['database'][$env]['username'],
                $config['database'][$env]['password']
            );
            return self::$pdo;
        }
    }
}

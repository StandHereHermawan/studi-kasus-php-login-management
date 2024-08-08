<?php

namespace AriefKarditya\LocalDomainPhp\Config;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    /**
     * @test
     */
    public function getConnection()
    {
        $connection = Database::getConnection();
        self::assertNotNull($connection);
    }

    /**
     * @test
     */
    public function connectionSingleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        TestCase::assertSame($connection1, $connection2);
    }
}

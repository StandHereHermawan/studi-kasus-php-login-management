<?php

namespace AriefKarditya\LocalDomainPhp\Repository;

use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;

    /**
     * @before
     */
    protected function setRepoAndDeleteAll()
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    /**
     * @test
     */
    public function saveSuccess()
    {
        $user = new User();
        $user->setId("USER-001");
        $user->setName("Terry");
        $user->setPassword("RasisBorn");

        $this->userRepository->save($user);

        $queryResult = $this->userRepository->findById($user->getId());

        TestCase::assertEquals($user->getId(), $queryResult->getId());
        TestCase::assertEquals($user->getName(), $queryResult->getName());
        TestCase::assertEquals($user->getPassword(), $queryResult->getPassword());
    }

    /**
     * @test
     */
    public function returnNull()
    {
        $user = $this->userRepository->findById("notfound");
        TestCase::assertNull($user);
    }
}

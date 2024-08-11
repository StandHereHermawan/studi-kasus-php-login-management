<?php

namespace AriefKarditya\LocalDomainPhp\Repository;

use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepo;

    /**
     * @before
     */
    protected function setRepoAndDeleteAll()
    {
        $this->sessionRepo = new SessionRepository(Database::getConnection());
        $this->sessionRepo->deleteAll();

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

    /**
     * @test
     */
    public function update()
    {
        $user = new User();
        $user->setId(uniqid());
        $user->setName("Terry Davis");
        $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $user->setName("Derek Chauvin");

        $this->userRepository->update($user);

        $queryResult = $this->userRepository->findById($user->getId());

        TestCase::assertEquals($user->getId(), $queryResult->getId());
        TestCase::assertEquals($user->getName(), $queryResult->getName());
        TestCase::assertEquals($user->getPassword(), $queryResult->getPassword());
    }
}

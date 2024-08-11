<?php

namespace AriefKarditya\LocalDomainPhp\Repository;

use AriefKarditya\LocalDomainPhp\Domain\User;
use PHPUnit\Framework\TestCase;
use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Domain\Session;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    /**
     * @before
     */
    protected function setRepo()
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");
        $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));
        $this->userRepository->save($user);
    }

    /**
     * @test
     */
    public function successSaveSession()
    {
        self::assertNotNull($this->sessionRepository);
        self::assertNotNull($this->userRepository);

        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId("USER-1");

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->getId());
        TestCase::assertEquals($session->getId(), $result->getId());
        TestCase::assertEquals($session->getUserId(), $result->getUserId());
    }

    /**
     * @test
     */
    public function deleteByIdSuccess()
    {
        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId("USER-1");

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->getId());

        TestCase::assertEquals($session->getId(), $result->getId());
        TestCase::assertEquals($session->getUserId(), $result->getUserId());

        $this->sessionRepository->deleteById($session->getId());

        $result = $this->sessionRepository->findById($session->getId());
        TestCase::assertNull($result);
    }
}

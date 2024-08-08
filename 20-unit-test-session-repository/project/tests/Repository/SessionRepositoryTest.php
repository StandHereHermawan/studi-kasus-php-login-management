<?php

namespace AriefKarditya\LocalDomainPhp\Repository;

use PHPUnit\Framework\TestCase;
use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Domain\Session;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;

    /**
     * @before
     */
    protected function setRepo()
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
    }

    /**
     * @test
     */
    public function successSaveSession()
    {
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

<?php

namespace AriefKarditya\LocalDomainPhp\Service;

require_once __DIR__ . "/../Helper/helper.php"; # import dari tests/Helper/helper.php

use AriefKarditya\LocalDomainPhp\Domain\Session;
use PHPUnit\Framework\TestCase;
use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use AriefKarditya\LocalDomainPhp\Domain\User;
use PHPUnit\Framework\Test;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepo;
    private UserRepository $userRepo;

    /**
     * @before
     */
    protected function setThingsNeeded()
    {
        $this->sessionRepo = new SessionRepository(Database::getConnection());
        $this->userRepo = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepo, $this->userRepo);

        $this->sessionRepo->deleteAll(); # Harus session repo dulu
        $this->userRepo->deleteAll();

        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");
        $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));
        $this->userRepo->save($user);
    }

    /**
     * @test
     */
    public function create()
    {
        self::assertNotNull($this->userRepo);
        self::assertNotNull($this->sessionRepo);
        self::assertNotNull($this->sessionService);

        $session = $this->sessionService->create("USER-1");

        $this->expectOutputRegex("[X-PZN-LOGIN-MANAGEMENT: {$session->getId()}]");

        $result = $this->sessionRepo->findById($session->getId());

        TestCase::assertEquals("USER-1", $result->getUserId());
    }

    /**
     * @test
     */
    public function destroy()
    {
        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId("USER-1");
        self::assertNotNull($session);

        $userSession = $session->getId();

        $this->sessionRepo->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->getId();

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-PZN-LOGIN-MANAGEMENT: ]");

        $result = $this->sessionRepo->findById($userSession);
        TestCase::assertNull($result);
    }

    /**
     * @test
     */
    public function current()
    {
        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId("USER-1");
        self::assertNotNull($session);

        $userSession = $session->getId();

        $this->sessionRepo->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->getId();

        $user = $this->sessionService->current();

        self::assertEquals($session->getUserId(), $user->getId());
    }
}

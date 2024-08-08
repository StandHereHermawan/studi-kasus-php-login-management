<?php

namespace AriefKarditya\LocalDomainPhp\Service;

use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Domain\Session;
use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use AriefKarditya\LocalDomainPhp\Domain\User;
use AriefKarditya\LocalDomainPhp\Helper\CookieManager;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    # Dependency untuk services
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    private CookieManager $cookieManager;

    /**
     * @before
     */
    protected function setThingsNeeded()
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->cookieManager = new CookieManager();

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        # Simpan User ke Repo
        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");
        $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId("USER-1");

        $mockCookieManager = $this->createMock(CookieManager::class);
        $mockCookieManager->expects($this->once())
            ->method("getCookie")
            ->with(
                $this->equalTo(SessionService::$COOKIE_NAME),
                $this->equalTo($session->getId()),
                $this->equalTo(time() + (60 * 60 * 24 * 36)),
                $this->equalTo("/")
            )->willReturn($session);

        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository, $this->cookieManager);
    }

    /**
     * @test
     */
    public function create()
    {
        $session = $this->sessionService->create("USER-1");

        $this->expectOutputRegex("[X-PZN-SESSION: {$session->getId()}]");
    }
}

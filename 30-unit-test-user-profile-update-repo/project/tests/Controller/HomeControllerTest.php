<?php

namespace AriefKarditya\LocalDomainPhp\Controller;

use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Domain\{User, Session};
use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use AriefKarditya\LocalDomainPhp\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    /**
     * @before
     */
    public function setNeededThings()
    {
        $this->homeController = new HomeController();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    /**
     * @test
     */
    public function guest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");
    }

    /**
     * @test
     */
    public function userLogin()
    {
        $user = new User();
        $user->setId(uniqid());
        $user->setName("Davis Andrew");
        $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId($user->getId());
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->getId();

        $this->homeController->index();

        $this->expectOutputRegex("[Hello {$user->getName()}]");
    }
}

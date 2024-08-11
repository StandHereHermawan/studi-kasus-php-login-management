<?php

namespace AriefKarditya\LocalDomainPhp\Middleware {

    require_once __DIR__ . "/../Helper/helper.php"; # import dari tests/Helper/helper.php

    use AriefKarditya\LocalDomainPhp\Config\Database;
    use AriefKarditya\LocalDomainPhp\Domain\Session;
    use AriefKarditya\LocalDomainPhp\Domain\User;
    use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
    use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
    use AriefKarditya\LocalDomainPhp\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustNotLoginMiddlewareTest extends TestCase
    {
        private MustNotLoginMiddleware $middleware;
        private UserRepository $userRepo;
        private SessionRepository $sessionRepo;

        /**
         * @before
         */
        protected function setThingsNeeded()
        {
            $this->middleware = new MustNotLoginMiddleware();
            putenv("mode=test"); # logic di redirect view.u

            $this->userRepo = new UserRepository(Database::getConnection());
            $this->sessionRepo = new SessionRepository(Database::getConnection());

            $this->sessionRepo->deleteAll();
            $this->userRepo->deleteAll();

            $this->assertNotNull($this->middleware);
            $this->assertNotNull($this->sessionRepo);
            $this->assertNotNull($this->userRepo);
        }

        /**
         * @test
         */
        public function beforeGuest()
        {
            $this->middleware->before();

            $this->expectOutputString("");
        }

        /**
         * @test
         */
        public function beforeUser()
        {
            $user = new User();
            $user->setId(uniqid());
            $user->setName("Davis Terry");
            $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));
            $this->userRepo->save($user);

            $queryResultUser = $this->userRepo->findById($user->getId());
            self::assertNotNull($queryResultUser);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepo->save($session);

            $queryResultSession = $this->sessionRepo->findById($session->getId());
            self::assertNotNull($queryResultSession);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->getId();
            self::assertNotNull($_COOKIE[SessionService::$COOKIE_NAME]);

            $this->middleware->before();
            $this->expectOutputRegex("[Location: /]");
        }
    }
}

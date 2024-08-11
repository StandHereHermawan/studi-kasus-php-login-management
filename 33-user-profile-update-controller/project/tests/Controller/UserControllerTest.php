<?php

namespace AriefKarditya\LocalDomainPhp\App {

    use AriefKarditya\LocalDomainPhp\Domain\Session;

    function header(string $value)
    {
        echo $value;
    }
}


namespace AriefKarditya\LocalDomainPhp\Controller {
    require_once __DIR__ . "/../Service/SessionServiceTest.php";

    use AriefKarditya\LocalDomainPhp\Config\Database;
    use AriefKarditya\LocalDomainPhp\Domain\User;
    use AriefKarditya\LocalDomainPhp\Domain\Session;
    use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
    use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
    use AriefKarditya\LocalDomainPhp\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepo;
        private SessionRepository $sessionRepo;

        /**
         * @before
         */
        protected function setControllerAndRepo()
        {
            $this->userController = new UserController();

            $this->userRepo = new UserRepository(Database::getConnection());
            $this->sessionRepo = new SessionRepository(Database::getConnection());
            $this->sessionRepo->deleteAll();
            $this->userRepo->deleteAll();

            putenv("mode=test");
        }

        /**
         * @test
         */
        public function register()
        {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }

        /**
         * @test
         */
        public function oneRegisterSuccess()
        {
            $duplId = "USER-001";
            $duplName = "Terry Davis";
            $dupPass = "rasis@born";

            $_POST['id'] = "$duplId";
            $_POST['name'] = "$duplName";
            $_POST['password'] = "$dupPass";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        /**
         * @test
         */
        public function twoRegisterBlank()
        {
            $_POST['id'] = '';
            $_POST['name'] = '';
            $_POST['password'] = '';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }

        /**
         * @test
         */
        public function threeRegisterDuplicate()
        {
            $duplId = "USER-001";
            $duplName = "Terry Davis";
            $dupPass = "rasis@born";

            $user = new User();
            $user->setId($duplId);
            $user->setName($duplName);
            $user->setPassword($dupPass);

            $this->userRepo->save($user);

            $_POST['id'] = "$duplId";
            $_POST['name'] = "$duplName";
            $_POST['password'] = "$dupPass";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[User Id already exists]");
        }

        /**
         * @test
         */
        public function login()
        {
            $this->userController->login();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
        }

        /**
         * @test
         */
        public function successLogin()
        {
            $user = new User();
            $user->setId("USER-1");
            $user->setName("Arief Hermawan");
            $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));

            $this->userRepo->save($user);

            $_POST['id'] = "USER-1";
            $_POST['password'] = "rahasia";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[" . SessionService::$COOKIE_NAME . ": ]");
        }

        /**
         * @test
         */
        public function unvalidLoginValidationError()
        {
            $_POST['id'] = "  ";
            $_POST['password'] = "  ";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[can not Blank]");
        }

        /**
         * @test
         */
        public function userNotFoundCaselogin()
        {
            $_POST['id'] = "not found";
            $_POST['password'] = "not found";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[is wrong]");
        }

        /**
         * @test
         */
        public function wrongPasswordLogin()
        {
            $user = new User();
            $user->setId("USER-1");
            $user->setName("Arief Hermawan");
            $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));

            $this->userRepo->save($user);

            $_POST['id'] = "USER-1";
            $_POST['password'] = "salah";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[is wrong]");
        }

        /**
         * @test
         */
        public function logout()
        {
            $user = new User();
            $user->setId("USER-1");
            $user->setName("Arief Hermawan");
            $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));

            $this->userRepo->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());

            $this->sessionRepo->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->getId();

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[" . SessionService::$COOKIE_NAME . ": ]");
        }
    }
}

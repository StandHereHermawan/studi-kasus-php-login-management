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

        /**
         * @test
         */
        public function updateProfile()
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

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[" . $user->getId() . "]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[" . $user->getName() . "]");
        }

        /**
         * @test
         */
        public function postUpdateProfileSuccess()
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

            $_POST['name'] = "Nigger";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepo->findById($user->getId());
            self::assertEquals($_POST['name'], $result->getName());
        }

        /**
         * @test
         */
        public function postUpdateProfileValidationError()
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

            $_POST['name'] = "          ";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[" . $user->getId() . "]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Id or name can not Blank.]");
        }

        /**
         * @test
         */
        public function updatePasswordPage()
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
            TestCase::assertNotNull($_COOKIE[SessionService::$COOKIE_NAME]);

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[" . $user->getId() . "]");
        }

        /**
         * @test
         */
        public function postUpdatePasswordSuccess()
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
            TestCase::assertNotNull($_COOKIE[SessionService::$COOKIE_NAME]);

            $_POST['oldPassword'] = "rahasia";
            $_POST['newPassword'] = "Rasis@Born12";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepo->findById($user->getId());
            TestCase::assertTrue(password_verify($_POST['newPassword'], $result->getPassword()));
        }

        /**
         * @test
         */
        public function postUpdatePasswordValidationError()
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
            TestCase::assertNotNull($_COOKIE[SessionService::$COOKIE_NAME]);

            $_POST['oldPassword'] = "           ";
            $_POST['newPassword'] = "           ";

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[" . $user->getId() . "]");
            $this->expectOutputRegex("[Id, old password and new password can not Blank.]");
        }

        /**
         * @test
         */
        public function postUpdatePasswordOldPasswordIsWrong()
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
            TestCase::assertNotNull($_COOKIE[SessionService::$COOKIE_NAME]);

            $_POST['oldPassword'] = "salah";
            $_POST['newPassword'] = "rasis";

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[" . $user->getId() . "]");
            $this->expectOutputRegex("[Old password is wrong]");
        }
    }
}

<?php

namespace AriefKarditya\LocalDomainPhp\App {

    function header(string $value)
    {
        echo $value;
    }
}

namespace AriefKarditya\LocalDomainPhp\Controller {

    use AriefKarditya\LocalDomainPhp\Config\Database;
    use AriefKarditya\LocalDomainPhp\Domain\User;
    use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $controller;

        private UserRepository $userRepo;

        /**
         * @before
         */
        protected function setControllerAndRepo()
        {
            $this->controller = new UserController();

            $this->userRepo = new UserRepository(Database::getConnection());
            $this->userRepo->deleteAll();

            putenv("mode=test");
        }

        /**
         * @test
         */
        public function register()
        {
            $this->controller->register();

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

            $this->controller->postRegister();

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

            $this->controller->postRegister();

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

            $this->controller->postRegister();

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
            $this->controller->login();

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

            $this->controller->postLogin();

            $this->expectOutputRegex("[Location: /]");
        }

        /**
         * @test
         */
        public function unvalidLoginValidationError()
        {
            $_POST['id'] = "  ";
            $_POST['password'] = "  ";

            $this->controller->postLogin();

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

            $this->controller->postLogin();

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

            $this->controller->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[is wrong]");
        }
    }
}

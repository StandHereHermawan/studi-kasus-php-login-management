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

        private UserRepository $repo;

        /**
         * @before
         */
        protected function setControllerAndRepo()
        {
            $this->controller = new UserController();

            $this->repo = new UserRepository(Database::getConnection());
            $this->repo->deleteAll();

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

            $this->repo->save($user);

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
    }
}

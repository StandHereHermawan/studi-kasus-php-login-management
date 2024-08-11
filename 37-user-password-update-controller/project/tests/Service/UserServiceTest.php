<?php

namespace AriefKarditya\LocalDomainPhp\Service;

use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Domain\User;
use AriefKarditya\LocalDomainPhp\Exception\ValidationException;
use AriefKarditya\LocalDomainPhp\Model\UserLoginRequest;
use AriefKarditya\LocalDomainPhp\Model\UserPasswordUpdateRequest;
use AriefKarditya\LocalDomainPhp\Model\UserProfileUpdateRequest;
use AriefKarditya\LocalDomainPhp\Model\UserRegisterRequest;
use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $service;
    private UserRepository $userRepo;
    private SessionRepository $sessionRepo;

    /**
     * @before
     */
    protected function setRepoAndService()
    {
        $connection = Database::getConnection();
        $this->sessionRepo = new SessionRepository($connection);
        $this->userRepo = new UserRepository($connection);
        $this->service = new UserService($this->userRepo);
        $this->sessionRepo->deleteAll();
        $this->userRepo->deleteAll();
    }

    /**
     * @test
     */
    public function registerSuccess()
    {
        $request = new UserRegisterRequest;
        $request->setId("USER-1");
        $request->setName("Terry Andrew");
        $request->setPassword("rasisborn");

        $response = $this->service->register($request);

        TestCase::assertEquals($request->getId(), $response->getUser()->getId());
        TestCase::assertEquals($request->getName(), $response->getUser()->getName());
        TestCase::assertNotEquals($request->getPassword(), $response->getUser()->getPassword());

        TestCase::assertTrue(password_verify($request->getPassword(), $response->getUser()->getPassword()));
    }

    /**
     * @test
     */
    public function registerFailedBlank()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest;
        $request->setId("  ");
        $request->setName("  ");
        $request->setPassword("  ");

        $response = $this->service->register($request);
    }

    /**
     * @test
     */
    public function registerFailedDuplicateId()
    {
        $this->expectException(ValidationException::class);

        $request = new User();
        $request->setId("USER-1");
        $request->setName("Terry Andrew");
        $request->setPassword("rasisborn");

        $response = $this->userRepo->save($request);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest;
        $request->setId("USER-1");
        $request->setName("Terry Andrew");
        $request->setPassword("rasisborn");

        $response = $this->service->register($request);
    }

    /**
     * @test
     */
    public function notFoundCaseExceptionLogin()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->setId("USER-1");
        $request->setPassword("rahasia");

        $this->service->login($request);
    }

    /**
     * @test
     */
    public function wrongPasswordCaseExceptionLogin()
    {
        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");
        $user->setPassword("rahasia");

        $this->userRepo->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->setId("USER-1");
        $request->setPassword("salah");

        $this->service->login($request);
    }

    /**
     * @test
     */
    public function successCaseLogin()
    {
        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");

        # Harus selalu menggunakan password hash untuk save password ke database tanpa lewat service
        $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));

        $this->userRepo->save($user);

        $request = new UserLoginRequest();
        $request->setId("USER-1");
        $request->setPassword("rahasia");

        $response = $this->service->login($request);

        TestCase::assertEquals($request->getId(), $response->getUser()->getId());
        TestCase::assertTrue(password_verify($request->getPassword(), $response->getUser()->getPassword()));
    }

    /**
     * @test
     */
    public function updateSuccess()
    {
        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");
        $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));

        $this->userRepo->save($user);

        $userFromQuery = $this->userRepo->findById($user->getId());
        TestCase::assertNotNull($userFromQuery);

        $updateRequest = new UserProfileUpdateRequest();
        $updateRequest->setFieldByUser($user);
        $updateRequest->setName("Terry si Rasis");

        $this->service->updateProfile($updateRequest);

        $userFromQueryAgain = $this->userRepo->findById($user->getId());
        TestCase::assertNotNull($userFromQueryAgain);

        TestCase::assertNotSame($user->getName(), $userFromQueryAgain->getName());
        TestCase::assertEquals($updateRequest->getName(), $userFromQueryAgain->getName());
    }

    /**
     * @test
     */
    public function updateValidationError()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");
        $user->setPassword(password_hash("rahasia", PASSWORD_BCRYPT));

        $this->userRepo->save($user);

        $userFromQuery = $this->userRepo->findById($user->getId());
        TestCase::assertNotNull($userFromQuery);

        $updateRequest = new UserProfileUpdateRequest();
        $updateRequest->setId("             ");
        $updateRequest->setName("           ");

        $this->service->updateProfile($updateRequest);
    }

    /**
     * @test
     */
    public function updateFailUserNotFound()
    {
        self::expectException(ValidationException::class);

        $updateRequest = new UserProfileUpdateRequest();
        $updateRequest->setId(uniqid());
        $updateRequest->setName("Terry si Rasis");

        $this->service->updateProfile($updateRequest);
    }

    /**
     * @test
     */
    public function updatePasswordSuccess()
    {
        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");
        $password = "rahasia";
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));

        $this->userRepo->save($user);
        $result = $this->userRepo->findById($user->getId());
        TestCase::assertNotNull($result);

        $request = new UserPasswordUpdateRequest();
        $request->setId($user->getId());
        $request->setOldPassword($password);
        $request->setNewPassword("rahasiaRasis");

        $this->service->updatePassword($request);

        $result = $this->userRepo->findById($user->getId());
        TestCase::assertNotNull($result);

        TestCase::assertTrue(password_verify($request->getNewPassword(), $result->getPassword()));
        TestCase::assertNotTrue(password_verify($user->getPassword(), $result->getPassword()));
    }

    /**
     * @test
     */
    public function updatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->setId("           ");
        $request->setOldPassword("              ");
        $request->setNewPassword("              ");

        $this->service->updatePassword($request);
    }

    /**
     * @test
     */
    public function updatePasswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->setId("USER-1");
        $user->setName("Terry Davis");
        $password = "rahasia";
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));

        $this->userRepo->save($user);
        $result = $this->userRepo->findById($user->getId());
        TestCase::assertNotNull($result);

        $request = new UserPasswordUpdateRequest();
        $request->setId($user->getId());
        $request->setOldPassword("salah");
        $request->setNewPassword("rahasiaRasis");

        $this->service->updatePassword($request);
    }

    /**
     * @test
     */
    public function updatePasswordUserNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->setId(uniqid());
        $request->setOldPassword("rahasia");
        $request->setNewPassword("rahasiaRasis");

        $this->service->updatePassword($request);
    }
}

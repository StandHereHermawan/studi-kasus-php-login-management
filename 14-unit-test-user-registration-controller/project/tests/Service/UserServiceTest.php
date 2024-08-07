<?php

namespace AriefKarditya\LocalDomainPhp\Service;

use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Domain\User;
use AriefKarditya\LocalDomainPhp\Exception\ValidationException;
use AriefKarditya\LocalDomainPhp\Model\UserRegisterRequest;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $service;
    private UserRepository $repo;
    /**
     * @before
     */
    protected function setRepoAndService()
    {
        $connection = Database::getConnection();
        $this->repo = new UserRepository($connection);
        $this->service = new UserService($this->repo);
        $this->repo->deleteAll();
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

        $response = $this->repo->save($request);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest;
        $request->setId("USER-1");
        $request->setName("Terry Andrew");
        $request->setPassword("rasisborn");

        $response = $this->service->register($request);
    }
}

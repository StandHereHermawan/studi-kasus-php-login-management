<?php

namespace AriefKarditya\LocalDomainPhp\Service;

use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Exception\ValidationException;
use AriefKarditya\LocalDomainPhp\Model\UserLoginRequest;
use AriefKarditya\LocalDomainPhp\Model\UserLoginResponse;
use AriefKarditya\LocalDomainPhp\Model\UserProfileUpdateRequest;
use AriefKarditya\LocalDomainPhp\Model\UserProfileUpdateResponse;
use AriefKarditya\LocalDomainPhp\Model\UserRegisterRequest;
use AriefKarditya\LocalDomainPhp\Model\UserRegisterResponse;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->repository->findById($request->getId());
            if ($user != null) {
                if ($user->getId() != null) {
                    throw new ValidationException("User Id already exists");
                }
            }

            $response = new UserRegisterResponse();
            $response->createObjectUser(
                id: $request->getId(),
                name: $request->getName(),
                password: password_hash($request->getPassword(), PASSWORD_BCRYPT)
            );

            $registeredUser = $response->getUser();

            $this->repository->save($registeredUser);

            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if (
            $request->getId() == null || $request->getName() == null || $request->getPassword() == null ||
            trim($request->getId()) == "" || trim($request->getName()) == "" || trim($request->getPassword()) == ""
        ) {
            throw new ValidationException("Id, name or password can not Blank.");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if (
            $request->getId() == null || $request->getPassword() == null ||
            trim($request->getId()) == "" || trim($request->getPassword()) == ""
        ) {
            throw new ValidationException("Id or password can not Blank.");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->repository->findById($request->getId());
        if ($user == null) {
            throw new ValidationException("Id or password is wrong.");
        }

        if (password_verify($request->getPassword(), $user->getPassword())) {
            $response = new UserLoginResponse();
            $response->setUser($user);
            return $response;
        } else {
            throw new ValidationException("Id or password is wrong.");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserUpdateProfile($request);

        try {
            Database::beginTransaction();

            $user = $this->repository->findById($request->getId());
            if ($user == null) {
                throw new ValidationException("User not found.");
            }

            # mengganti nama user
            $user->setName($request->getName());

            # Save ke database
            $this->repository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->createObjectUser(
                $user->getId(),
                $user->getName(),
                $user->getPassword()
            );
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserUpdateProfile(UserProfileUpdateRequest $request)
    {
        if (
            $request->getId() == null || $request->getName() == null ||
            trim($request->getId()) == "" || trim($request->getName()) == ""
        ) {
            throw new ValidationException("Id or name can not Blank.");
        }
    }
}

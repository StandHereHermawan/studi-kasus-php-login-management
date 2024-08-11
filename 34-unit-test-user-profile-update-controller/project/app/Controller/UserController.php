<?php

namespace AriefKarditya\LocalDomainPhp\Controller;

use AriefKarditya\LocalDomainPhp\App\View;
use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Exception\ValidationException;
use AriefKarditya\LocalDomainPhp\Model\UserLoginRequest;
use AriefKarditya\LocalDomainPhp\Model\UserProfileUpdateRequest;
use AriefKarditya\LocalDomainPhp\Model\UserRegisterRequest;
use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use AriefKarditya\LocalDomainPhp\Service\SessionService;
use AriefKarditya\LocalDomainPhp\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepo = new UserRepository($connection);
        $this->userService = new UserService($userRepo);

        $sessionRepo = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepo, $userRepo);
    }

    public function register()
    {
        View::render('User/register', [
            'title' => "Register new User"
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->setId($_POST['id']);
        $request->setName($_POST['name']);
        $request->setPassword($_POST['password']);

        try {
            $this->userService->register($request);

            # redirect ke path info /users/login
            View::redirect('/users/login');
        } catch (\Throwable $th) {
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function login()
    {
        View::render('User/login', [
            "title" => "Login user"
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->setId($_POST['id']);
        $request->setPassword($_POST['password']);

        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->getUser()->getId());
            View::redirect('/');
        } catch (\Throwable $th) {
            View::render('User/login', [
                'title' => 'Login user',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect("/");
    }

    public function updateProfile()
    {
        $user = $this->sessionService->current();

        View::render('User/profile', [
            "title" => "Update user profile",
            "user" => [
                "id" => $user->getId(),
                "name" => $user->getName()
            ]
        ]);
    }

    public function postUpdateProfile()
    {
        $user = $this->sessionService->current();

        $request = new UserProfileUpdateRequest();
        $request->setId($user->getId());
        $request->setName($_POST['name']);

        try {
            $this->userService->updateProfile($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/profile', [
                "title" => "Update user profile",
                "error" => $exception->getMessage(),
                "user" => [
                    "id" => $user->getId(),
                    "name" => $user->getName()
                ]
            ]);
        }
    }
}

<?php

namespace AriefKarditya\LocalDomainPhp\Controller;

use AriefKarditya\LocalDomainPhp\App\View;
use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Model\UserLoginRequest;
use AriefKarditya\LocalDomainPhp\Model\UserRegisterRequest;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use AriefKarditya\LocalDomainPhp\Service\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $repo = new UserRepository($connection);
        $this->userService = new UserService($repo);
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
            $this->userService->login($request);
            View::redirect('/');
        } catch (\Throwable $th) {
            View::render('User/login', [
                'title' => 'Login user',
                'error' => $th->getMessage()
            ]);
        }
    }
}

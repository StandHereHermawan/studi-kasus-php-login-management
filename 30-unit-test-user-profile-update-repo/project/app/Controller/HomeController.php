<?php

namespace AriefKarditya\LocalDomainPhp\Controller;

use AriefKarditya\LocalDomainPhp\App\View;
use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use AriefKarditya\LocalDomainPhp\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepo = new SessionRepository($connection);
        $userRepo = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepo, $userRepo);
    }

    function index()
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            View::render('Home/index', [
                "title" => "PHP Login Management"
            ]);
        } else {
            View::render('Home/dashboard', [
                "title" => "Dasboard " . $user->getName(),
                "user" => [
                    "name" => $user->getName()
                ]
            ]);
        }
    }
}

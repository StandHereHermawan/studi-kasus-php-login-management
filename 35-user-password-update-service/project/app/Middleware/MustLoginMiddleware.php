<?php

namespace AriefKarditya\LocalDomainPhp\Middleware;

use AriefKarditya\LocalDomainPhp\App\View;
use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use AriefKarditya\LocalDomainPhp\Service\SessionService;

class MustLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepo = new SessionRepository(Database::getConnection());
        $userRepo = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepo, $userRepo);
    }

    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            View::redirect('/users/login');
        }
    }
}

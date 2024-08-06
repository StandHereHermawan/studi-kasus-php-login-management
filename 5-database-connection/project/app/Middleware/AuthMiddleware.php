<?php

namespace AriefKarditya\LocalDomainPhp\Middleware;

use AriefKarditya\LocalDomainPhp\Middleware\Middleware;

class AuthMiddleware implements Middleware
{
    function before(): void
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
    }
}

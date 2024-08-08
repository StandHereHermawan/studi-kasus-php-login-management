<?php

namespace AriefKarditya\LocalDomainPhp\Service;

use AriefKarditya\LocalDomainPhp\Domain\Session;
use AriefKarditya\LocalDomainPhp\Domain\User;
use AriefKarditya\LocalDomainPhp\Repository\SessionRepository;
use AriefKarditya\LocalDomainPhp\Repository\UserRepository;
use AriefKarditya\LocalDomainPhp\Helper\CookieManager;

class SessionService
{
    public static string $COOKIE_NAME = "X-PZN-SESSION";
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    private CookieManager $cookieManager;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository, CookieManager $cookieManager)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
        $this->cookieManager = $cookieManager;
    }

    public function create(string $userId): Session
    {
        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId($userId);

        $this->sessionRepository->save($session);

        // $this->cookieManager->set(self::$COOKIE_NAME, $session->getId(), time() + 60 * 60 * 24 * 30, "/");
        $this->cookieManager->setCookie(SessionService::$COOKIE_NAME, $session->getId(), time() + (60 * 60 * 24 * 30), "/");
        $this->cookieManager->getCookie();

        return $session;
    }

    public function destroy()
    {
        $sessionId = $_COOKIE[SessionService::$COOKIE_NAME] ?? '';
        $this->sessionRepository->deleteById($sessionId);

        // $this->cookieManager->set(self::$COOKIE_NAME, '', 1, "/");
        $this->cookieManager->setCookie(SessionService::$COOKIE_NAME, '', 1, "/");
        $this->cookieManager->getCookie();
    }

    public function current(): ?User
    {
        $sessionId = $_COOKIE[SessionService::$COOKIE_NAME] ?? '';

        $session = $this->sessionRepository->findById($sessionId);
        if ($session == null) {
            return null;
        }

        return $this->userRepository->findById($session->getUserId());
    }
}

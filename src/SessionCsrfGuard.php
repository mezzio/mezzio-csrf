<?php

declare(strict_types=1);

namespace Mezzio\Csrf;

use Mezzio\Session\SessionInterface;

use function bin2hex;
use function random_bytes;

class SessionCsrfGuard implements CsrfGuardInterface
{
    public function __construct(private SessionInterface $session)
    {
    }

    public function generateToken(string $keyName = '__csrf'): string
    {
        $token = bin2hex(random_bytes(16));
        $this->session->set($keyName, $token);
        return $token;
    }

    public function validateToken(string $token, string $csrfKey = '__csrf'): bool
    {
        $storedToken = $this->session->get($csrfKey, '');
        $this->session->unset($csrfKey);
        return $token === $storedToken;
    }
}

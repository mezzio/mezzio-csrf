<?php

declare(strict_types=1);

namespace Mezzio\Csrf;

use Mezzio\Flash\FlashMessagesInterface;

use function bin2hex;
use function random_bytes;

class FlashCsrfGuard implements CsrfGuardInterface
{
    public function __construct(private FlashMessagesInterface $flashMessages)
    {
    }

    public function generateToken(string $keyName = '__csrf'): string
    {
        $token = bin2hex(random_bytes(16));
        $this->flashMessages->flash($keyName, $token);
        return $token;
    }

    public function validateToken(string $token, string $csrfKey = '__csrf'): bool
    {
        $storedToken = $this->flashMessages->getFlash($csrfKey, '');
        return $token === $storedToken;
    }
}

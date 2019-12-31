<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Csrf;

use Mezzio\Flash\FlashMessagesInterface;

class FlashCsrfGuard implements CsrfGuardInterface
{
    /**
     * @var FlashMessagesInterface
     */
    private $flashMessages;

    public function __construct(FlashMessagesInterface $flashMessages)
    {
        $this->flashMessages = $flashMessages;
    }

    public function generateToken(string $keyName = '__csrf') : string
    {
        $token = bin2hex(random_bytes(16));
        $this->flashMessages->flash($keyName, $token);
        return $token;
    }

    public function validateToken(string $token, string $csrfKey = '__csrf') : bool
    {
        $storedToken = $this->flashMessages->getFlash($csrfKey, '');
        return $token === $storedToken;
    }
}

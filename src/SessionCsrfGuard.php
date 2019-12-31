<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Csrf;

use Mezzio\Session\SessionInterface;

class SessionCsrfGuard implements CsrfGuardInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function generateToken(string $keyName = '__csrf') : string
    {
        $token = bin2hex(random_bytes(16));
        $this->session->set($keyName, $token);
        return $token;
    }

    public function validateToken(string $token, string $csrfKey = '__csrf') : bool
    {
        $storedToken = $this->session->get($csrfKey, '');
        $this->session->unset($csrfKey);
        return $token === $storedToken;
    }
}

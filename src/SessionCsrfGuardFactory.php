<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Csrf;

use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class SessionCsrfGuardFactory implements CsrfGuardFactoryInterface
{
    /**
     * @var string
     */
    private $attributeKey;

    public function __construct(string $attributeKey = SessionMiddleware::SESSION_ATTRIBUTE)
    {
        $this->attributeKey = $attributeKey;
    }

    public function createGuardFromRequest(ServerRequestInterface $request) : CsrfGuardInterface
    {
        $session = $request->getAttribute($this->attributeKey, false);
        if (! $session instanceof SessionInterface) {
            throw Exception\MissingSessionException::create();
        }

        return new SessionCsrfGuard($session);
    }
}

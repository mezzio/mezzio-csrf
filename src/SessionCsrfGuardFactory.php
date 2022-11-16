<?php

declare(strict_types=1);

namespace Mezzio\Csrf;

use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class SessionCsrfGuardFactory implements CsrfGuardFactoryInterface
{
    public function __construct(private string $attributeKey = SessionMiddleware::SESSION_ATTRIBUTE)
    {
    }

    public function createGuardFromRequest(ServerRequestInterface $request): CsrfGuardInterface
    {
        $session = $request->getAttribute($this->attributeKey, false);
        if (! $session instanceof SessionInterface) {
            throw Exception\MissingSessionException::create();
        }

        return new SessionCsrfGuard($session);
    }
}

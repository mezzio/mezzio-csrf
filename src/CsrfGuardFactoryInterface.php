<?php

declare(strict_types=1);

namespace Mezzio\Csrf;

use Psr\Http\Message\ServerRequestInterface;

interface CsrfGuardFactoryInterface
{
    public function createGuardFromRequest(ServerRequestInterface $request): CsrfGuardInterface;
}

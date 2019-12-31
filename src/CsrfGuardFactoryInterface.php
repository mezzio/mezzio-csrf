<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Csrf;

use Psr\Http\Message\ServerRequestInterface;

interface CsrfGuardFactoryInterface
{
    public function createGuardFromRequest(ServerRequestInterface $request) : CsrfGuardInterface;
}

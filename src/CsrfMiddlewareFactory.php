<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Csrf;

use Psr\Container\ContainerInterface;

class CsrfMiddlewareFactory
{
    /**
     * @return CsrfMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new CsrfMiddleware(
            $container->get(CsrfGuardFactoryInterface::class)
        );
    }
}

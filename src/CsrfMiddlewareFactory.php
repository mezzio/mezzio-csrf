<?php

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

<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace MezzioTest\Csrf;

use Mezzio\Csrf\CsrfGuardFactoryInterface;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Csrf\CsrfMiddlewareFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class CsrfMiddlewareFactoryTest extends TestCase
{
    public function testFactoryReturnsMiddlewareUsingDefaultAttributeAndConfiguredGuardFactory()
    {
        $guardFactory = $this->prophesize(CsrfGuardFactoryInterface::class)->reveal();
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(CsrfGuardFactoryInterface::class)->willReturn($guardFactory);

        $factory = new CsrfMiddlewareFactory();

        $middleware = $factory($container->reveal());

        $this->assertInstanceOf(CsrfMiddleware::class, $middleware);
        $this->assertAttributeSame($guardFactory, 'guardFactory', $middleware);
        $this->assertAttributeSame($middleware::GUARD_ATTRIBUTE, 'attributeKey', $middleware);
    }
}

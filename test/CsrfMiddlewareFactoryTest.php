<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace MezzioTest\Csrf;

use Mezzio\Csrf\CsrfGuardFactoryInterface;
use Mezzio\Csrf\CsrfGuardInterface;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Csrf\CsrfMiddlewareFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddlewareFactoryTest extends TestCase
{
    public function testFactoryReturnsMiddlewareUsingDefaultAttributeAndConfiguredGuardFactory(): void
    {
        $guardFactory = $this->createMock(CsrfGuardFactoryInterface::class);
        $container    = $this->createMock(ContainerInterface::class);

        $container->expects(self::atLeastOnce())
                  ->method('get')
                  ->with(CsrfGuardFactoryInterface::class)
                  ->willReturn($guardFactory);

        $factory    = new CsrfMiddlewareFactory();
        $middleware = $factory($container);

        $this->assertInstanceOf(CsrfMiddleware::class, $middleware);

        $request = $this->createMock(ServerRequestInterface::class);
        $guard   = $this->createMock(CsrfGuardInterface::class);

        $guardFactory->expects(self::atLeastOnce())
                     ->method('createGuardFromRequest')
                     ->with($request)
                     ->willReturn($guard);

        $request->expects(self::atLeastOnce())
                ->method('withAttribute')
                ->with($middleware::GUARD_ATTRIBUTE, $guard)
                ->willReturn($request);

        $middleware->process($request, $this->createMock(RequestHandlerInterface::class));
    }
}

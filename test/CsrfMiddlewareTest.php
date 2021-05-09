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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddlewareTest extends TestCase
{
    /** @var MockObject<CsrfGuardFactoryInterface> */
    private $guardFactory;

    protected function setUp(): void
    {
        $this->guardFactory = $this->createMock(CsrfGuardFactoryInterface::class);
    }

    public function testConstructorUsesSaneAttributeKeyByDefault(): void
    {
        $middleware = new CsrfMiddleware($this->guardFactory);
        /**
         * TODO: Replace checks to internal properties
         */
        //$this->assertAttributeSame($this->guardFactory, 'guardFactory', $middleware);
        //$this->assertAttributeSame($middleware::GUARD_ATTRIBUTE, 'attributeKey', $middleware);
    }

    public function testConstructorAllowsProvidingAlternateAttributeKey(): void
    {
        $middleware = new CsrfMiddleware($this->guardFactory, 'alternate-key');
        /**
         * TODO: Replace checks to internal properties
         */
        //$this->assertAttributeSame($this->guardFactory, 'guardFactory', $middleware);
        //$this->assertAttributeSame('alternate-key', 'attributeKey', $middleware);
    }

    /**
     * @return array<string, array<null|string>>
     */
    public function attributeKeyProvider(): array
    {
        return [
            'null-default' => [null],
            'custom'       => ['alternate-key'],
        ];
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testProcessDelegatesNewRequestContainingGeneratedGuardInstance(?string $attributeKey = null): void
    {
        $guard    = $this->createMock(CsrfGuardInterface::class);
        $request  = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $middleware = $attributeKey !== null
            ? new CsrfMiddleware($this->guardFactory, $attributeKey)
            : new CsrfMiddleware($this->guardFactory);

        $attributeKey = $attributeKey ?: CsrfMiddleware::GUARD_ATTRIBUTE;

        $this->guardFactory->expects(self::atLeastOnce())
                           ->method('createGuardFromRequest')
                           ->with($request)
                           ->willReturn($guard);
        $request->expects(self::atLeastOnce())
                ->method('withAttribute')
                ->with($attributeKey, $guard)
                ->willReturn($request);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects(self::atLeastOnce())->method('handle')->with($request)->willReturn($response);

        $this->assertSame($response, $middleware->process($request, $handler));
    }
}

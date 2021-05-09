<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace MezzioTest\Csrf;

use Mezzio\Csrf\Exception;
use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Csrf\SessionCsrfGuardFactory;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class SessionCsrfGuardFactoryTest extends TestCase
{
    public function testConstructionUsesSaneDefaults(): void
    {
        $factory = new SessionCsrfGuardFactory();
        /**
         * TODO: Replace checks to internal properties
         */
        //$this->assertAttributeSame(SessionMiddleware::SESSION_ATTRIBUTE, 'attributeKey', $factory);
    }

    public function testConstructionAllowsPassingAttributeKey(): void
    {
        $factory = new SessionCsrfGuardFactory('alternate-attribute');
        /**
         * TODO: Replace checks to internal properties
         */
        //$this->assertAttributeSame('alternate-attribute', 'attributeKey', $factory);
    }

    public function attributeKeyProvider(): array
    {
        return [
            'default' => [SessionMiddleware::SESSION_ATTRIBUTE],
            'custom'  => ['custom-session-attribute'],
        ];
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testCreateGuardFromRequestRaisesExceptionIfAttributeDoesNotContainSession(string $attribute): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())->method('getAttribute')->with($attribute, false)->willReturn(false);

        $factory = new SessionCsrfGuardFactory($attribute);

        $this->expectException(Exception\MissingSessionException::class);
        $factory->createGuardFromRequest($request);
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testCreateGuardFromRequestReturnsCsrfGuardWithSessionWhenPresent(string $attribute): void
    {
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())->method('getAttribute')->with($attribute, false)->willReturn($session);

        $factory = new SessionCsrfGuardFactory($attribute);

        $guard = $factory->createGuardFromRequest($request);
        $this->assertInstanceOf(SessionCsrfGuard::class, $guard);
    }
}

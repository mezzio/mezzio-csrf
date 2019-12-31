<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

namespace MezzioTest\Csrf;

use Mezzio\Csrf\Exception;
use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Csrf\SessionCsrfGuardFactory;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;

class SessionCsrfGuardFactoryTest extends TestCase
{
    public function testConstructionUsesSaneDefaults()
    {
        $factory = new SessionCsrfGuardFactory();
        $this->assertAttributeSame(SessionMiddleware::SESSION_ATTRIBUTE, 'attributeKey', $factory);
    }

    public function testConstructionAllowsPassingAttributeKey()
    {
        $factory = new SessionCsrfGuardFactory('alternate-attribute');
        $this->assertAttributeSame('alternate-attribute', 'attributeKey', $factory);
    }

    public function attributeKeyProvider() : array
    {
        return [
            'default' => [SessionMiddleware::SESSION_ATTRIBUTE],
            'custom'  => ['custom-session-attribute'],
        ];
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testCreateGuardFromRequestRaisesExceptionIfAttributeDoesNotContainSession(string $attribute)
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute($attribute, false)->willReturn(false);

        $factory = new SessionCsrfGuardFactory($attribute);

        $this->expectException(Exception\MissingSessionException::class);
        $factory->createGuardFromRequest($request->reveal());
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testCreateGuardFromRequestReturnsCsrfGuardWithSessionWhenPresent(string $attribute)
    {
        $session = $this->prophesize(SessionInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute($attribute, false)->willReturn($session);

        $factory = new SessionCsrfGuardFactory($attribute);

        $guard = $factory->createGuardFromRequest($request->reveal());
        $this->assertInstanceOf(SessionCsrfGuard::class, $guard);
    }
}

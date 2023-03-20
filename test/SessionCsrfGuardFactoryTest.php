<?php

declare(strict_types=1);

namespace MezzioTest\Csrf;

use Mezzio\Csrf\Exception;
use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Csrf\SessionCsrfGuardFactory;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class SessionCsrfGuardFactoryTest extends TestCase
{
    public function testConstructionUsesSaneDefaults(): void
    {
        $factory = new SessionCsrfGuardFactory();
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())
                ->method('getAttribute')
                ->with(SessionMiddleware::SESSION_ATTRIBUTE, false)
                ->willReturn($session);

        $factory->createGuardFromRequest($request);
    }

    public function testConstructionAllowsPassingAttributeKey(): void
    {
        $factory = new SessionCsrfGuardFactory('alternate-attribute');
        $session = $this->createMock(SessionInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())
                ->method('getAttribute')
                ->with('alternate-attribute', false)
                ->willReturn($session);

        $factory->createGuardFromRequest($request);
    }

    public static function attributeKeyProvider(): array
    {
        return [
            'default' => [SessionMiddleware::SESSION_ATTRIBUTE],
            'custom'  => ['custom-session-attribute'],
        ];
    }

    #[DataProvider('attributeKeyProvider')]
    public function testCreateGuardFromRequestRaisesExceptionIfAttributeDoesNotContainSession(string $attribute): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())->method('getAttribute')->with($attribute, false)->willReturn(false);

        $factory = new SessionCsrfGuardFactory($attribute);

        $this->expectException(Exception\MissingSessionException::class);
        $factory->createGuardFromRequest($request);
    }

    #[DataProvider('attributeKeyProvider')]
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

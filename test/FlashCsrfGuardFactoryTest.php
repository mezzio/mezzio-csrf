<?php

declare(strict_types=1);

namespace MezzioTest\Csrf;

use Mezzio\Csrf\Exception;
use Mezzio\Csrf\FlashCsrfGuard;
use Mezzio\Csrf\FlashCsrfGuardFactory;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class FlashCsrfGuardFactoryTest extends TestCase
{
    public function testConstructionUsesSaneDefaults(): void
    {
        $factory  = new FlashCsrfGuardFactory();
        $messages = $this->createMock(FlashMessagesInterface::class);
        $request  = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())
                ->method('getAttribute')
                ->with(FlashMessageMiddleware::FLASH_ATTRIBUTE, false)
                ->willReturn($messages);

        $factory->createGuardFromRequest($request);
    }

    public function testConstructionAllowsPassingAttributeKey(): void
    {
        $factory  = new FlashCsrfGuardFactory('alternate-attribute');
        $messages = $this->createMock(FlashMessagesInterface::class);
        $request  = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())
                ->method('getAttribute')
                ->with('alternate-attribute', false)
                ->willReturn($messages);

        $factory->createGuardFromRequest($request);
    }

    public function attributeKeyProvider(): array
    {
        return [
            'default' => [FlashMessageMiddleware::FLASH_ATTRIBUTE],
            'custom'  => ['custom-flash-attribute'],
        ];
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testCreateGuardFromRequestRaisesExceptionIfAttributeDoesNotContainFlash(string $attribute): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())->method('getAttribute')->with($attribute, false)->willReturn(false);

        $factory = new FlashCsrfGuardFactory($attribute);

        $this->expectException(Exception\MissingFlashMessagesException::class);
        $factory->createGuardFromRequest($request);
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testCreateGuardFromRequestReturnsCsrfGuardWithSessionWhenPresent(string $attribute): void
    {
        $flash   = $this->createMock(FlashMessagesInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())->method('getAttribute')->with($attribute, false)->willReturn($flash);

        $factory = new FlashCsrfGuardFactory($attribute);

        $guard = $factory->createGuardFromRequest($request);
        $this->assertInstanceOf(FlashCsrfGuard::class, $guard);
    }
}

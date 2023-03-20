<?php

declare(strict_types=1);

namespace MezzioTest\Csrf;

use Mezzio\Csrf\FlashCsrfGuard;
use Mezzio\Flash\FlashMessagesInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FlashCsrfGuardTest extends TestCase
{
    /** @var MockObject&FlashMessagesInterface  */
    private FlashMessagesInterface $flash;
    private FlashCsrfGuard $guard;

    protected function setUp(): void
    {
        $this->flash = $this->createMock(FlashMessagesInterface::class);
        $this->guard = new FlashCsrfGuard($this->flash);
    }

    public static function keyNameProvider(): array
    {
        return [
            'default' => ['__csrf'],
            'custom'  => ['CSRF'],
        ];
    }

    #[DataProvider('keyNameProvider')]
    public function testGenerateTokenStoresTokenInFlashAndReturnsIt(string $keyName): void
    {
        $expected = '';
        $this->flash->expects(self::atLeastOnce())->method('flash')
            ->with(
                $keyName,
                $this->callback(function ($token) use (&$expected): bool {
                    $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $token);
                    $expected = $token;
                    return true;
                })
            );

        $token = $this->guard->generateToken($keyName);
        $this->assertSame((string) $expected, $token);
    }

    public static function tokenValidationProvider(): array
    {
        // @codingStandardsIgnoreStart
        return [
            // case                  => [token,   key,      flash token, assertion    ]
            'default-not-found'      => ['token', '__csrf', '',          'assertFalse'],
            'default-found-not-same' => ['token', '__csrf', 'different', 'assertFalse'],
            'default-found-same'     => ['token', '__csrf', 'token',     'assertTrue'],
            'custom-not-found'       => ['token', 'CSRF',   '',          'assertFalse'],
            'custom-found-not-same'  => ['token', 'CSRF',   'different', 'assertFalse'],
            'custom-found-same'      => ['token', 'CSRF',   'token',     'assertTrue'],
        ];
        // @codingStandardsIgnoreEnd
    }

    #[DataProvider('tokenValidationProvider')]
    public function testValidateTokenValidatesProvidedTokenAgainstOneStoredInFlash(
        string $token,
        string $csrfKey,
        string $flashTokenValue,
        string $assertion
    ): void {
        $this->flash->expects(self::atLeastOnce())
                    ->method('getFlash')
                    ->with($csrfKey, '')
                    ->willReturn($flashTokenValue);
        $this->$assertion($this->guard->validateToken($token, $csrfKey));
    }
}

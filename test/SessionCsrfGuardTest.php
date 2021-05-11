<?php

declare(strict_types=1);

namespace MezzioTest\Csrf;

use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Session\SessionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SessionCsrfGuardTest extends TestCase
{
    /** @var MockObject<SessionInterface> */
    private $session;
    /** @var SessionCsrfGuard */
    private $guard;

    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionInterface::class);
        $this->guard   = new SessionCsrfGuard($this->session);
    }

    public function keyNameProvider(): array
    {
        return [
            'default' => ['__csrf'],
            'custom'  => ['CSRF'],
        ];
    }

    /**
     * @dataProvider keyNameProvider
     */
    public function testGenerateTokenStoresTokenInSessionAndReturnsIt(string $keyName): void
    {
        $expected = '';
        $this->session->expects(self::atLeastOnce())->method('set')
            ->with(
                $keyName,
                $this->callback(function ($token) use (&$expected) {
                    $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $token);
                    $expected = $token;
                    return true;
                })
            );

        $token = $this->guard->generateToken($keyName);
        $this->assertSame($expected, $token);
    }

    public function tokenValidationProvider(): array
    {
        // @codingStandardsIgnoreStart
        return [
            // case                  => [token,   key,      session token, assertion    ]
            'default-not-found'      => ['token', '__csrf', '',            'assertFalse'],
            'default-found-not-same' => ['token', '__csrf', 'different',   'assertFalse'],
            'default-found-same'     => ['token', '__csrf', 'token',       'assertTrue'],
            'custom-not-found'       => ['token', 'CSRF',   '',            'assertFalse'],
            'custom-found-not-same'  => ['token', 'CSRF',   'different',   'assertFalse'],
            'custom-found-same'      => ['token', 'CSRF',   'token',       'assertTrue'],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider tokenValidationProvider
     */
    public function testValidateTokenValidatesProvidedTokenAgainstOneStoredInSession(
        string $token,
        string $csrfKey,
        string $sessionTokenValue,
        string $assertion
    ): void {
        $this->session->expects(self::atLeastOnce())->method('get')->with($csrfKey, '')->willReturn($sessionTokenValue);
        $this->session->expects(self::atLeastOnce())->method('unset')->with($csrfKey);
        $this->$assertion($this->guard->validateToken($token, $csrfKey));
    }
}

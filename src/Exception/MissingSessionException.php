<?php

declare(strict_types=1);

namespace Mezzio\Csrf\Exception;

use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Session\SessionMiddleware;
use RuntimeException;

use function sprintf;

class MissingSessionException extends RuntimeException implements ExceptionInterface
{
    public static function create(): self
    {
        return new self(sprintf(
            'Cannot create %s; could not locate session in request. '
            . 'Make sure the %s is piped to your application.',
            SessionCsrfGuard::class,
            SessionMiddleware::class
        ));
    }
}

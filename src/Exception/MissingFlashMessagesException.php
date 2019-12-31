<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Csrf\Exception;

use Mezzio\Csrf\FlashCsrfGuard;
use Mezzio\Flash\FlashMessageMiddleware;
use RuntimeException;

use function sprintf;

class MissingFlashMessagesException extends RuntimeException implements ExceptionInterface
{
    public static function create() : self
    {
        return new self(sprintf(
            'Cannot create %s; could not locate session in request. '
            . 'Make sure the %s is piped to your application.',
            FlashCsrfGuard::class,
            FlashMessageMiddleware::class
        ));
    }
}

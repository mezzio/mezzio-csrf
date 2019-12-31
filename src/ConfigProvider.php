<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Csrf;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies() : array
    {
        return [
            'aliases' => [
                // Change this to the CsrfGuardFactoryInterface implementation you wish to use:
                CsrfGuardFactoryInterface::class => SessionCsrfGuardFactory::class,

                // Legacy Zend Framework aliases
                \Zend\Expressive\Csrf\CsrfGuardFactoryInterface::class => CsrfGuardFactoryInterface::class,
                \Zend\Expressive\Csrf\FlashCsrfGuardFactory::class => FlashCsrfGuardFactory::class,
                \Zend\Expressive\Csrf\SessionCsrfGuardFactory::class => SessionCsrfGuardFactory::class,
                \Zend\Expressive\Csrf\CsrfMiddleware::class => CsrfMiddleware::class,
            ],
            'invokables' => [
                FlashCsrfGuardFactory::class   => FlashCsrfGuardFactory::class,
                SessionCsrfGuardFactory::class => SessionCsrfGuardFactory::class,
            ],
            'factories' => [
                CsrfMiddleware::class => CsrfMiddlewareFactory::class,
            ],
        ];
    }
}

<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;
use Boundwize\StructArmed\Rule\Rules\Class_\MustBeFinalRule;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Contract', [
        'src/CsrfGuardInterface.php',
        'src/CsrfGuardFactoryInterface.php',
    ])
    ->layer('Guard', [
        'src/FlashCsrfGuard.php',
        'src/SessionCsrfGuard.php',
    ])
    ->layer('Exception', 'src/Exception')
    ->layer('GuardFactory', [
        'src/FlashCsrfGuardFactory.php',
        'src/SessionCsrfGuardFactory.php',
    ])
    ->layer('Middleware', 'src/CsrfMiddleware.php')
    ->layer('MiddlewareFactory', 'src/CsrfMiddlewareFactory.php')
    ->layer('ConfigProvider', 'src/ConfigProvider.php')
    ->layer('tests', 'test')
    ->ruleset([
        'Contract'          => [],
        'Guard'             => ['Contract'],
        'Exception'         => ['+Guard'],
        'GuardFactory'      => ['+Exception'],
        'Middleware'        => ['Contract'],
        'MiddlewareFactory' => ['+Middleware'],
        'ConfigProvider'    => ['+GuardFactory', '+MiddlewareFactory'],
    ])
    ->rule(
        'tests_classes.must_be_final',
        new MustBeFinalRule(layer: 'tests')
    );

<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src'
    ]);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);

    // import php 7.4 set list for php 7.4 features
    $containerConfigurator->import(SetList::PHP_74);

    // set Typed Property only for private property
    $services = $containerConfigurator->services();
    $services->set(TypedPropertyRector::class)
        ->call('configure', [[
            TypedPropertyRector::PRIVATE_PROPERTY_ONLY => true,
        ]]);
};

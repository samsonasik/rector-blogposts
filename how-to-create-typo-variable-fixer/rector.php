<?php

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Utils\Rector\TypoVariableFixerRule;

return static function (ContainerConfigurator $containerConfigurator): void {
	$parameters = $containerConfigurator->parameters();
	$parameters->set(Option::PATHS, [__DIR__ . '/app']);

	$services = $containerConfigurator->services();
	$services->set(TypoVariableFixerRule::class);
};

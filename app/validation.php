<?php

use App\Application\Services\ValidatorService;

//return function (\DI\Container $container) {
//	$container->set(ValidatorService::class, new ValidatorService());
//};


return function (\DI\ContainerBuilder $containerBuilder) {
	// Global Settings Object
	$containerBuilder->addDefinitions([
		ValidatorService::class => function () {
			return new ValidatorService();
		}
	]);
};

<?php

use DI\ContainerBuilder;
use Symfony\Component\Console\Application;
use App\Application\Console\UserCreateCommand;

require_once __DIR__ . '/../vendor/autoload.php';

// LOAD ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$logger = require __DIR__ . '/../app/logger.php';
$logger($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Register eloquent database
$eloquent = require __DIR__ . '/../app/eloquent.php';
$eloquent($container);

try {
	/** @var Application $application */
	$app = $container->get(Application::class);

	// Register your console commands here
	$app->add($container->get(UserCreateCommand::class));

	exit($app->run());
} catch (Throwable $exception) {
	echo $exception->getMessage();
	exit(1);
}

<?php

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

	// Global Settings Object
	$containerBuilder->addDefinitions([
		SettingsInterface::class => function () {
			return new Settings([
				'env' 				  => $_ENV['APP_ENV'] ?? 'production',
				'displayErrorDetails' => true, // Should be set to false in production
				'logError'            => true,
				'logErrorDetails'     => true,
				'logger'              => [
					'name'  => 'slim-app',
					'path'  => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
					'level' => Logger::DEBUG,
				],

				'mysql' => [
					'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
					'host'      => $_ENV['DB_HOST'] ?? 'localhost',
					'database'  => $_ENV['DB_DATABASE'],
					'username'  => $_ENV['DB_USERNAME'],
					'password'  => $_ENV['DB_PASSWORD'],
					'charset'   => 'utf8',
					'collation' => 'utf8_unicode_ci',
					'prefix'    => '',
				],
				'sqlite' => [
					'driver'                  => 'sqlite',
					'url'                     => env('DATABASE_URL'),
					'database'                => env('DB_DATABASE', __DIR__ .'/../tests/database.sqlite'),
					'prefix'                  => '',
					'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
				],
			]);
		}
	]);
};

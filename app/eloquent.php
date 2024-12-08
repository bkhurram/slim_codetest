<?php

use Illuminate\Database\Capsule\Manager as CapsuleManager;
use App\Application\Settings\SettingsInterface;

return function (\DI\Container $container) {
	$settings = $container->get(SettingsInterface::class);
	if($settings->get('env') === 'testing') {
		$dbSettings = $settings->get('sqlite');
	} else {
		$dbSettings = $settings->get('mysql');
	}

	// Eloquent ORM Setup
	$capsule = new CapsuleManager;

	// Database configuration
	$capsule->addConnection($dbSettings);

	// Make the Capsule instance available globally
	$capsule->setAsGlobal();
	$capsule->bootEloquent();

	$container->set(CapsuleManager::class, $capsule);
	return $capsule;
};

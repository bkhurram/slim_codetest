<?php

use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Events\Dispatcher;
use App\Application\Settings\SettingsInterface;
use Illuminate\Container\Container;

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

	// debug
	// $capsule->getConnection()->enableQueryLog();

	// model boot creating | updating
	$capsule->setEventDispatcher(new Dispatcher(new Container()));

	// Make the Capsule instance available globally
	$capsule->setAsGlobal();
	$capsule->bootEloquent();

	$container->set(CapsuleManager::class, $capsule);
	return $capsule;
};

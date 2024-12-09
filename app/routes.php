<?php

use App\Application\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\User\UserListAction;
use App\Application\Actions\User\UserByEmailAction;
use App\Application\Actions\User\LoginAction;
use App\Application\Actions\User\UserCreateAction;
use App\Application\Actions\User\UserUpdateAction;
use App\Application\Actions\User\UserDeleteAction;

return function (App $app) {
	$app->post('/login', LoginAction::class);

	$app->group('/users', function (Group $group) {
		$group->get('', UserListAction::class);
		$group->post('', UserCreateAction::class);
		$group->get('/{email}', UserByEmailAction::class);
		$group->put('/{email}', UserUpdateAction::class);
		$group->delete('/{email}', UserDeleteAction::class);
	})->add(AuthMiddleware::class);
};

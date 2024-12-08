<?php


use App\Application\Middleware\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\User\UserListAction;
use App\Application\Actions\User\UserByEmailAction;
use App\Application\Actions\User\LoginAction;

return function (App $app) {
//	$app->options('/{routes:.*}', function (Request $request, Response $response) {
//		// CORS Pre-Flight OPTIONS Request Handler
//		return $response;
//	});

	$app->post('/login', LoginAction::class);

	$app->group('/users', function (Group $group) {
		$group->get('', UserListAction::class);
		$group->get('/{email}', UserByEmailAction::class);
	})->add(AuthMiddleware::class);
};

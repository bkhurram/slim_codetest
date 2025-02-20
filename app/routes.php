<?php

use App\Application\Actions\Event\EventAction;
use App\Application\Actions\Static\StaticServeAction;
use App\Application\Actions\User\LoginAction;
use App\Application\Actions\User\UserByEmailAction;
use App\Application\Actions\User\UserCreateAction;
use App\Application\Actions\User\UserDeleteAction;
use App\Application\Actions\User\UserListAction;
use App\Application\Actions\User\UserUpdateAction;
use App\Application\Controller\PostsController;
use App\Application\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->post('/login', LoginAction::class);

    $app->group('/users', function (Group $group) {
        $group->get('', UserListAction::class);
        $group->post('', UserCreateAction::class);
        $group->get('/{email}', UserByEmailAction::class);
        $group->put('/{email}', UserUpdateAction::class);
        $group->delete('/{email}', UserDeleteAction::class);
    })->add(AuthMiddleware::class);

    $app->group('/posts', function (Group $group) {
        $group->get('', [PostsController::class, 'index']);
        $group->post('', [PostsController::class, 'store']);
        $group->get('/{id}', [PostsController::class, 'view']);
        $group->put('/{id}', [PostsController::class, 'update']);
        $group->delete('/{id}', [PostsController::class, 'delete']);
    })->add(AuthMiddleware::class);

    $app->get('/events', EventAction::class);
    $app->get('/static/{file}', StaticServeAction::class);
};

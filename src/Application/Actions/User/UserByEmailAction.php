<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Models\User;
use App\Application\Response\PaginateResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class UserByEmailAction extends Action
{
	/**
	 * {@inheritdoc}
	 */
	protected function action(): Response
	{
		$email = $this->args['email'];

		$user = User::query()->where('email', $email);
		if (!$user->exists()) {
			throw new HttpNotFoundException($this->request, 'Email not found');
		}

		return $this->respondWithData($user->with(['address'])->first());
	}
}

<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UserDeleteAction extends Action
{
	public function action(): Response
	{
		$email = $this->args['email'];

		// remove user
		$user = User::firstWhere('email', $email);
		if (!$user) {
			throw new HttpBadRequestException($this->request, 'Email not found');
		}

		$user->delete();

		return $this->respondWithData(null, 204);
	}
}

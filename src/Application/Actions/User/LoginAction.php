<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Models\User;
use App\Application\Services\JwtService;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class LoginAction extends Action
{
	protected function action(): Response
	{
		$data = $this->request->getParsedBody();
		$user = User::firstWhere('email', $data['email']);

		if(!$user || !password_verify($data['password'], $user->password)){
			// user not found or invalid password
			throw new HttpBadRequestException($this->request, "Error on login, please check credentials.");
		}

		$this->logger->info("Login: $user->toJson()");

		$issuedAt = time();
		// jwt valid for 1 hour (60 seconds * 60 minutes)
		$expirationTime = $issuedAt + 60 * 60;
		$payload = array(
			'sub'        => $user->id,
			'name'       => $user->name,
			'email'      => $user->email,
			'giveName'   => $user->giveName,
			'familyName' => $user->familyName,
			'exp'        => $expirationTime,
			'iat'        => $issuedAt,
		);

		$jwtService = new JwtService($_ENV['JWT_SECRET']);
		$token = $jwtService->createToken($payload);

		return $this->respondWithData([ 'jwt' => $token ]);
	}
}

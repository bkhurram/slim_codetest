<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Exception\HttpUnprocessableException;
use App\Application\Models\User;
use App\Application\Services\JwtService;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class LoginAction extends Action
{
    protected function action(): Response
    {
        $data = $this->getFormData();
        $this->validateFormData($data);

        // check user and password
        $user = User::firstWhere('email', $data['email']);
        if(!$user || !password_verify($data['password'], $user->password)){
            // user not found or invalid password
            throw new HttpBadRequestException($this->request, 'Error on login, please check credentials.');
        }

        $this->logger->info("Login: $user->email");

        $issuedAt = time();
        // jwt valid for 1 hour (60 seconds * 60 minutes)
        $expirationTime = $issuedAt + 60 * 60;
        $payload = [
            'sub'        => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'givenName'  => $user->givenName,
            'familyName' => $user->familyName,
            'exp'        => $expirationTime,
            'iat'        => $issuedAt,
        ];

        $jwtService = new JwtService($_ENV['JWT_SECRET']);
        $token = $jwtService->createToken($payload);

        return $this->respondWithData(['jwt' => $token]);
    }

    private function validateFormData(array $data)
    {
        $rules = [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ];

        $messages = [
            'email.required'    => 'Email is required.',
            'email.email'       => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
            'password.min'      => 'Minimum 6 characters.',
        ];

        // Validate the data
        $errors = $this->validator->validate($data, $rules, $messages);
        if ($errors) {
            // Return validation errors as a JSON response
            throw new HttpUnprocessableException($this->request, $errors);
        }
    }
}

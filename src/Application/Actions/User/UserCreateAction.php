<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Models\User;
use App\Application\Services\Rules\EmailUnique;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface as Response;

class UserCreateAction extends Action
{
	protected function action(): Response
	{
		$data = $this->request->getParsedBody();

		// Define the validation rules
		$rules = [
			'givenName' => 'required|string',
			'familyName' => 'required|string',
			'email'    => ['required','email', new EmailUnique()],
			'dateOfBirth' => 'nullable|date',
			'password' => [
				'required',
				'string',
				'min:6',  // Minimum length of 6 characters
				'regex:/[0-9]/',  // At least one number
				'regex:/[a-z]/',  // At least one lowercase letter
				'regex:/[A-Z]/',  // At least one uppercase letter
				'regex:/[,.:;-_$%&()=]{2,}/',
				'regex:/^(?!.*([a-zA-Z0-9])\1{1}).*$/',  // No consecutive identical characters
				function ($attribute, $value, $fail) use ($data) { // Email local-part can't be part of password
					$emailPrefix = preg_quote(explode('@', $data['email'])[0]);
					if (str_contains($value, $emailPrefix)) {
						$fail('The password cannot contain the prefix of your email address.');
					}
				},
			],
		];

		$messages = [
			'givenName.required' => 'Given Name is required.',
			'familyName.required' => 'Family Name is required.',
			'email.required' => 'Email is required.',
			'email.email'   => 'Please provide a valid email address.',
			'dateOfBirth.date' => 'Please provide a valid date of birth.',
			'password.required' => 'Password is required.',
			'password.min' => 'Minimum 6 characters.',
			'password.regex' => 'The password does not meet the required security criteria.',
		];

		// Validate the data
		$errors = $this->validator->validate($data, $rules, $messages);

		if ($errors) {
			// Return validation errors as a JSON response
			return $this->respondWithData(['errors' => $errors], 422);
		}

		// ********
		$user = new User();
		$user->forceFill(Arr::except($data, 'password'));
		$user->password = password_hash($data['password'], PASSWORD_BCRYPT);
		$user->save();

		return $this->respondWithData($user, 201);
	}
}

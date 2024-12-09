<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Exception\HttpUnprocessableException;
use App\Application\Models\User;
use App\Application\Models\UserAddress;
use App\Application\Validation\Rules\EmailUnique;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;

class UserCreateAction extends Action
{
	public function action(): Response
	{
		$data = $this->getFormData();
		$this->validateFormData($data);

		$userdata = Arr::where($data, function ($value) { return $value !== null && $value !== ''; }); // remove empty data from array
		$userdata = Arr::except($userdata, ['password', 'address']);

		$connection = $this->capsule->getConnection();

		try{
			$connection->beginTransaction(); // Start the transaction
			// Store user
			$user = new User();
			$user->forceFill($userdata);
			$user->password = password_hash($data['password'], PASSWORD_BCRYPT);
			$user->save();

			$address = $data['address'];
			$userAddressData = Arr::except($address, 'coordinates');
			$userAddressData['lat'] = Arr::get($address, 'coordinates.lat');
			$userAddressData['lng'] = Arr::get($address, 'coordinates.lng');

			$userAddress = new UserAddress();
			$userAddress->forceFill($userAddressData);
			$userAddress->user()->associate($user);
			$userAddress->save();

			$connection->commit(); // Commit the transaction
		} catch (\Exception $e) {
			$connection->rollBack(); // Rollback the transaction on error
			$this->logger->error("Fail create user: " . $e->getMessage());
			throw new HttpInternalServerErrorException($this->request);
		}

		return $this->respondWithData($user, 201);
	}

	private function validateFormData(array $data)
	{
		// Define the validation rules
		$rules = [
			'givenName'   => ['required','string'],
			'familyName'  => ['required','string'],
			'email'       => ['required','email', new EmailUnique()],
			'dateOfBirth' => ['nullable', 'date_format:Y-m-d'],
			'password'    => [
				'required',
				'string',
				'min:6',  // Minimum length of 6 characters
				'regex:/[0-9]/',  // At least one number
				'regex:/[a-z]/',  // At least one lowercase letter
				'regex:/[A-Z]/',  // At least one uppercase letter
				'regex:/(?:[^,.:;\-_$%&()=]*[,.:;\-_$%&()=]){2}/', // 2 special characters
				'regex:/^(?!.*([a-zA-Z0-9])\1{1}).*$/',  // No consecutive identical characters
				function ($attribute, $value, $fail) use ($data) { // Email local-part can't be part of password
					$emailPrefix = preg_quote(explode('@', $data['email'])[0]);
					if (str_contains($value, $emailPrefix)) {
						$fail('The password cannot contain the prefix of your email address.');
					}
				},
			],

			"address.street"          => ['required','string', 'min:3'],
			"address.city"            => ['required','string', 'min:3'],
			"address.postCode"        => ['required','string', 'min:3'],
			"address.countryCode"     => ['required','string', 'min:2', 'max:2', 'regex:/[A-Z]{2}/',],
			"address.coordinates.lat" => ['required','string'],
			"address.coordinates.lng" => ['required','string'],
		];

		$messages = [
			'givenName.required'  => 'Given Name is required.',
			'familyName.required' => 'Family Name is required.',
			'email.required'      => 'Email is required.',
			'email.email'         => 'Please provide a valid email address.',
			'dateOfBirth.date_format'    => 'Please provide a valid date of birth.',
			'password.required'   => 'Password is required.',
			'password.min'        => 'Minimum 6 characters.',
			'password.regex'      => 'The password does not meet the required security criteria.',

			'address.street.required'          => 'Street is required.',
			'address.street.min'               => 'Street must be at least 3 characters.',
			'address.city.required'            => 'City is required.',
			'address.city.min'                 => 'City must be at least 3 characters.',
			'address.postCode.required'        => 'PostCode is required.',
			'address.postCode.min'             => 'PostCode must be at least 3 characters.',
			'address.countryCode.required'     => 'Country code is required.',
			'address.coordinates.lat.required' => 'latitude is required.',
			'address.coordinates.lng.required' => 'longitude is required.',
		];

		// Validate the data
		$errors = $this->validator->validate($data, $rules, $messages);
		if($errors) {
			throw new HttpUnprocessableException($this->request, $errors);
		}
	}
}

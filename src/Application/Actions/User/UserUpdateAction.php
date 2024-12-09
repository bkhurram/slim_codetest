<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Exception\HttpUnprocessableException;
use App\Application\Models\User;
use App\Application\Models\UserAddress;
use App\Application\Validation\Rules\EmailUnique;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;

class UserUpdateAction extends Action
{
	public function action(): Response
	{
		$email = $this->args['email'];

		$data = $this->getFormData();
		$this->validateFormData($email, $data);

//		$userId = $this->request->getAttribute('userId');
//		if(!User::where('id', $userId)->where('email', $email)->exists()) {
//			throw new HttpForbiddenException($this->request, 'User not enable for update');
//		}

		$userdata = Arr::where($data, function ($value) { return $value !== null && $value !== ''; }); // remove empty data from array
		$userdata = Arr::except($userdata, ['password', 'address']);

		$connection = $this->capsule->getConnection();

		try{
			$connection->beginTransaction(); // Start the transaction
			// update user
			$user = User::firstWhere('email', $email);
			$user->forceFill($userdata);
			$user->save();

			$address = $data['address'];
			$userAddressData = Arr::except($address, 'coordinates');
			$userAddressData['lat'] = Arr::get($address, 'coordinates.lat');
			$userAddressData['lng'] = Arr::get($address, 'coordinates.lng');

			$userAddress = $user->address;
			$userAddress->forceFill($userAddressData);
			$userAddress->save();

			$connection->commit(); // Commit the transaction
		} catch (\Exception $e) {
			$connection->rollBack(); // Rollback the transaction on error
			$this->logger->info("Fail update user: " . $e->getMessage());
			throw new HttpInternalServerErrorException($this->request);
		}

		return $this->respondWithData($user, 202);
	}

	private function validateFormData(string $email, array $data)
	{
		// Define the validation rules
		$rules = [
			'givenName'   => ['required','string'],
			'familyName'  => ['required','string'],
			'email'       => ['required','email', new EmailUnique($this->request->getAttribute('userId'))],
			'dateOfBirth' => ['nullable', 'date_format:Y-m-d'],

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
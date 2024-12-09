<?php

namespace App\Application\Response;

use App\Application\Models\User;
use App\Application\Models\UserAddress;

class AddressResponse
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function map(UserAddress $address): array
	{
		return [
			'street'      => $address->street,
			'city'        => $address->city,
			'postCode'    => $address->postCode,
			'countryCode' => $address->countryCode,
			'coordinates' => [
				'lat' => $address->lat,
				'lng' => $address->lng,
			]
		];
	}
}

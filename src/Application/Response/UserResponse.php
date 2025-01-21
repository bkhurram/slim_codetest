<?php

namespace App\Application\Response;

use App\Application\Models\User;
use DateTime;
use DateTimeZone;

class UserResponse
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function map(User $user): array
    {
        $date = null;
        if(isset($user->createdAt)) {
            $date = new DateTime($user->createdAt, new DateTimeZone('UTC'));
            $date = $date->format('Y-m-d\TH:i:s\Z');
        }

        return [
            'name'        => $user->name,
            'givenName'   => $user->givenName,
            'familyName'  => $user->familyName,
            'email'       => $user->email,
            'dateOfBirth' => $user->dateOfBirth,
            'createdAt'   => $date,
            'address'     => $user->address ?
                (new AddressResponse())->map($user->address)
                : null,
        ];
    }
}

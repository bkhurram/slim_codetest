<?php

namespace App\Application\Response;

class UserCollectionResponse
{
    public function map(array $users): array
    {
        return array_map(function ($user) {
                return (new UserResponse())->map($user);
            }, $users);
    }
}

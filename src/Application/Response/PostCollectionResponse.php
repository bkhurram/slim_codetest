<?php

namespace App\Application\Response;

class PostCollectionResponse
{
    public function map(array $posts): array
    {
        return array_map(function ($post) {
                return (new PostResponse())->map($post);
            }, $posts);
    }
}

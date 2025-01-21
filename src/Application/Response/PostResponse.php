<?php

namespace App\Application\Response;

use App\Application\Models\Post;

class PostResponse
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function map(Post $post): array
    {
        return [
            'id'     => $post->uuid,
            'title'  => $post->title,
            'body'   => $post->body,
            'status' => $post->status,
            'tags'   => $post->tags()->pluck('name')->toArray(),
        ];
    }
}

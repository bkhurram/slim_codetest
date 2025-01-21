<?php

namespace App\Application\Models;

use App\Application\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasSlug;

    protected $fillable = [
        'slug',
        'name',
    ];

    protected $hidden = [
        'id',
    ];

    public $timestamps = false;

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function generateSlug(): string
    {
        return Str::slug($this->name);
    }
}

<?php

namespace App\Application\Models\Concerns;

use App\Application\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUser
{
    public function initializeHasUser(): void
    {
        $this->hidden[] = 'userId';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

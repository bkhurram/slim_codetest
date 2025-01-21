<?php

namespace App\Application\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            $model->uuid ??= Str::uuid()->toString();
        });
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function scopeWhereUuid($query, $uuid)
    {
        return $query->where('uuid', $uuid);
    }
}
